@extends('tournaments.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @forelse ($tournament->matches->groupBy('week') as $week => $weeks)
                <div class="col-12 col-md-4">
                    <h4 class="py-2 px-3 bg-dark text-white">Week {{ $week }}</h4>
                    <ul class="list-unstyled">
                        @forelse ($weeks as $match)
                            <li class="p-2 border-bottom d-flex justify-content-center">
                                <span>{{ $match->team1->name }}</span>
                                <span class="mx-2">-</span>
                                <span>{{ $match->team2->name }}</span>
                            </li>
                        @empty
                        @endforelse
                    </ul>
                </div>
            @empty
            @endforelse
        </div>

        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <a href="{{ route('tournaments.simulation', ['tournamentId' => $tournament->id]) }}" class="btn btn-success">
                    Start Simulation
                </a>
            </div>
        </div>
    </div>
@endsection
