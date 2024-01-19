<?php

namespace App\Http\Controllers;

use App\Enums\MatchStatus;
use App\Exceptions\Tournaments\InvalidMatchPairException;
use App\Managers\TournamentManager;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{
    public function index()
    {
        $teams = Team::query()->get();

        return view('tournaments.index')
            ->with('teams', $teams);
    }

    public function store()
    {
        return DB::transaction(function () {
            $teams = Team::query()->select(['id', 'name'])->get()->toArray();

            try {
                /** @var TournamentManager $manager */
                $manager = app(TournamentManager::class);
                $matches = $manager->roundRobin($teams, 6);
            } catch (InvalidMatchPairException $e) {
                return redirect()
                    ->back()
                    ->with('error', $e->getMessage());
            }

            try {
                $tournament = Tournament::query()->create([
                    'name' => 'Tournament'
                ]);

                $week = 1;
                foreach ($matches as $match) {
                    foreach ($match as $team) {
                        TournamentMatch::query()->create([
                            'tournament_id' => $tournament->id,
                            'team_1' => $team[0]['id'],
                            'team_2' => $team[1]['id'],
                            'week' => $week
                        ]);
                    }

                    $week++;
                }

                foreach ($teams as $team) {
                    Participant::query()->create([
                        'tournament_id' => $tournament->id,
                        'team_id' => $team['id']
                    ]);
                }

            } catch (\Exception $e) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->with('error', $e->getMessage());
            }

            return redirect()
                ->route('tournaments.show', ['tournamentId' => $tournament->id]);
        });
    }

    public function show(Tournament $tournament)
    {
        $tournament->load([
            'matches' => function ($query) {
                $query->with(['team1', 'team2']);
            }
        ]);

        return view('tournaments.show')
            ->with('tournament', $tournament);
    }

    public function simulation(Tournament $tournament)
    {
        $participants = Participant::query()
            ->with('team')
            ->where('tournament_id', '=', $tournament->id)
            ->get();

        $matchInWeeks = TournamentMatch::query()
            ->with(['team1', 'team2'])
            ->where('tournament_id', $tournament->id)
            ->where('status', MatchStatus::PENDING->value)
            ->orderBy('id', 'ASC')
            ->limit(2)
            ->get()
        ;

        $championships = Participant::query()
            // ->select([
            //     'participants.*',
            //     DB::raw("((participants.win / participants.played) * 100) AS calculated_predictions")
            // ])
            ->with(['team'])
            ->where('tournament_id', $tournament->id)
            ->orderBy('predictions', 'DESC')
            ->get()
            ;

        return view('tournaments.simulation')
            ->with('participants', $participants)
            ->with('tournament', $tournament)
            ->with('championships', $championships)
            ->with('matchInWeeks', $matchInWeeks);
    }

    public function play(Request $request, Tournament $tournament)
    {
        return DB::transaction(function () use ($tournament, $request) {
            try {
                /** @var \App\Managers\TournamentManager $manager */
                $manager = app(TournamentManager::class);

                $matchQuery = TournamentMatch::query()
                    ->with(['team1', 'team2'])
                    ->where('tournament_id', $tournament->id)
                    ->where('status', '=', MatchStatus::PENDING->value)
                    ->orderBy('id', 'ASC')
                ;

                if (!$request->filled('all')) {
                    $matchQuery
                        ->limit(2);
                }

                $matches = $matchQuery->get();

                foreach ($matches as $match) {
                    $manager->setMatch($match)->updateScoreboard();
                }

                return redirect()->back();
            } catch (\Exception $e) {
                dd($e);
                return back()->with('error', $e->getMessage());
            }
        });
    }

    public function reset(Tournament $tournament)
    {
        return DB::transaction(function () use ($tournament) {
            try {
                $manager = app(TournamentManager::class);
                $manager
                    ->setTournament($tournament)
                    ->reset();
                
                return redirect()->back();
            } catch (\Exception $e) {
                dd($e);
                return back()->with('error', $e->getMessage());
            }
        });
    }
}
