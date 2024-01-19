<?php

namespace App\Managers;

use App\Enums\MatchStatus;
use App\Enums\WinType;
use App\Exceptions\Tournaments\InvalidMatchPairException;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class TournamentManager
{
    private Tournament $tournament;

    private TournamentMatch $match;

    public function __construct()
    {
    }

    public function roundRobin(array $teams = [], int|null $repeater = null): array
    {
        $total = count($teams);

        if ($total < 1 || $total % 2 != 0) {
            throw new InvalidMatchPairException('Takım sayısı eşit değil');
        }

        if (is_null($repeater)) {
            $repeater = $total - 1;
        }

        $rounds = [];
        for ($r = 0; $r < $repeater; $r++) {
            for ($i = 0; $i < $total / 2; $i++) {
                $rounds[$r][] = [
                    $teams[$i],
                    $teams[$total - 1 - $i]
                ];
            }

            $teams[] = array_splice($teams, 1, 1)[0];
        }

        return $rounds;
    }

    public function setTournament(Tournament $tournament): self
    {
        $this->tournament = $tournament;
        return $this;
    }

    public function setMatch(TournamentMatch $match): self
    {
        $this->match = $match;
        return $this;
    }

    public function updateScoreboard()
    {
        $team1Score = mt_rand(0, 4);
        $team2Score = mt_rand(0, 4);

        $arr = [
            'team_1_score' => $team1Score,
            'team_2_score' => $team2Score,
            'status' => MatchStatus::FINISHED->value,
            'win_type' => WinType::WIN->value
        ];

        if ($team1Score > $team2Score) {
            $arr['winner_id'] = $this->match->team_1;
            $this->addWin($this->match->tournament_id, $this->match->team_1);
            $this->addLose($this->match->tournament_id, $this->match->team_2);
        } else if ($team2Score > $team1Score) {
            $arr['winner_id'] = $this->match->team_2;
            $this->addWin($this->match->tournament_id, $this->match->team_2);
            $this->addLose($this->match->tournament_id, $this->match->team_1);
        } else {
            $arr['win_type'] = WinType::DRAW->value;
            $this->addDraw($this->match->tournament_id, $this->match->team_1);
            $this->addDraw($this->match->tournament_id, $this->match->team_2);
        }

        $this->addPlayed($this->match->tournament_id, $this->match->team_1);
        $this->addPlayed($this->match->tournament_id, $this->match->team_2);

        // total goal of team 1
        $this->addGoalScore($this->match->tournament_id, $this->match->team_1, $team1Score);
        $this->addGoalConceded($this->match->tournament_id, $this->match->team_1, $team2Score);

        // total goal of team 2
        $this->addGoalScore($this->match->tournament_id, $this->match->team_2, $team2Score);
        $this->addGoalConceded($this->match->tournament_id, $this->match->team_2, $team1Score);

        $this->updatePredictions($this->match->tournament_id, $this->match->team_1);

        $this->match->update($arr);

        return $this;
    }

    public function updatePredictions(int $tournamentId, int $teamId): void
    {
        $participant = Participant::query()
            ->select(['id', 'played', 'win', 'predictions'])
            ->where('tournament_id', '=', $tournamentId)
            ->where('team_id', '=', $teamId)
            ->first();

        $participant->update([
            'predictions' => (($participant->win / $participant->played) * 100)
        ]);
    }

    public function addGoalScore(int $tournamentId, int $teamId, int $score): void
    {
        Participant::query()
            ->where('tournament_id', '=', $tournamentId)
            ->where('team_id', '=', $teamId)
            ->update([
                'goal_score' => DB::raw('goal_score + ' . $score)
            ]);
    }

    public function addGoalConceded(int $tournamentId, int $teamId, int $score): void
    {
        Participant::query()
            ->where('tournament_id', '=', $tournamentId)
            ->where('team_id', '=', $teamId)
            ->update([
                'goal_conceded' => DB::raw('goal_conceded + ' . $score)
            ]);
    }

    public function addWin(int $tournamentId, int $teamId)
    {
        return $this->incrementQuery($tournamentId, $teamId, 'win');
    }

    public function addLose($tournamentId, $teamId)
    {
        return $this->incrementQuery($tournamentId, $teamId, 'lose');
    }

    public function addDraw($tournamentId, $teamId)
    {
        return $this->incrementQuery($tournamentId, $teamId, 'draw');
    }

    public function addPlayed(int $tournamentId, int $teamId)
    {
        return $this->incrementQuery($tournamentId, $teamId, 'played');
    }

    private function incrementQuery(int $tournamentId, int $teamId, string $column)
    {
        return Participant::query()
            ->where('tournament_id', '=', $tournamentId)
            ->where('team_id', '=', $teamId)
            ->increment($column);
    }

    public function reset(): void
    {
        $this->tournament->matches()->update([
            'winner_id' => null,
            'team_1_score' => 0,
            'team_2_score' => 0,
            'win_type' => WinType::UNKNOWN->value,
            'status' => MatchStatus::PENDING->value,
        ]);

        Participant::query()
            ->where('tournament_id', $this->tournament->id)
            ->update([
                'played' => 0,
                'win' => 0,
                'lose' => 0,
                'draw' => 0,
                'goal_score' => 0,
                'goal_conceded' => 0,
                'predictions' => 0
            ]);
    }

    protected function getStatistic($teamId): array
    {
        $teamWin = TournamentMatch::query()
            ->where('tournament_id', $this->match->tournament_id)
            ->where('winner_id', '=', $teamId)
            ->where('win_type', '=', WinType::WIN->value)
            ->sum('id')
        ;

        $teamLose = TournamentMatch::query()
            ->where('tournament_id', $this->match->tournament_id)
            ->where('winner_id', '!=', $teamId)
            ->where('win_type', '=', WinType::LOSE->value)
            ->sum('id')
        ;

        $teamDraw = TournamentMatch::query()
            ->where('tournament_id', $this->match->tournament_id)
            ->where(function ($query) use ($teamId) {
                $query
                    ->where('team_1', '=', $teamId)
                    ->orWhere('team_2', '=', $teamId);
            })
            ->where('win_type', '=', WinType::DRAW->value)
            ->sum('id')
        ;

        return [
            'win' => intval($teamWin),
            'lose' => intval($teamLose),
            'draw' => intval($teamDraw)
        ];
    }
}