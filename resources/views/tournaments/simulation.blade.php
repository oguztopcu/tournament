@extends('tournaments.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h4 class="text-center py-5">Simulation</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th class="text-center">P</th>
                            <th class="text-center">W</th>
                            <th class="text-center">D</th>
                            <th class="text-center">L</th>
                            <th class="text-center">GD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($participants as $participant)
                            <tr>
                                <td>{{ $participant->team->name }}</td>
                                <td class="text-center">{{ $participant->played ?? '-' }}</td>
                                <td class="text-center">{{ $participant->win ?? '-' }}</td>
                                <td class="text-center">{{ $participant->draw ?? '-' }}</td>
                                <td class="text-center">{{ $participant->lose ?? '-' }}</td>
                                <td class="text-center">{{ ($participant->goal_score - $participant->goal_conceded) ?? '-' }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="col-12 col-md-2">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Week 1</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($matchInWeeks as $match)
                            <tr>
                                <td>{{ $match->team1->name }} - {{ $match->team2->name }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="col-12 col-md-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Championship Prodictions</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($championships as $champion)
                            <tr>
                                <td>{{ $champion->team->name }}</td>
                                <td>{{ number_format($champion->predictions, 2) ?? '-' }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 col-md-4 text-center">
                <form action="{{ route('tournaments.play', ['tournamentId' => $tournament->id]) }}" method="POST">
                    @csrf

                    <input type="hidden" name="all" value="true" />

                    <button type="submit" class="btn btn-primary">Play All Weeks</button>
                </form>
            </div>

            <div class="col-12 col-md-4 text-center">
                <form action="{{ route('tournaments.play', ['tournamentId' => $tournament->id]) }}" method="POST">
                    @csrf
                    
                    <button type="submit" class="btn btn-primary">Play Next Week</button>
                </form>
            </div>

            <div class="col-12 col-md-4 text-center">
                <form action="{{ route('tournaments.reset', ['tournamentId' => $tournament->id]) }}" method="POST">
                    @csrf

                    <button type="submit" class="btn btn-danger">Reset Data</button>
                </form>
            </div>
        </div>
    </div>
@endsection
