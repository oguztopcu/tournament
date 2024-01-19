@extends('tournaments.layouts.master')

@section('content')
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-12 h-100">
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <div class="d-block col-5">
                        <h4>Tournament Teams</h4>

                        <ul class="list-unstyled">
                            <li class="border-bottom bg-dark text-white p-2">Team Name</li>
                            @forelse ($teams as $team)
                                <li class="border-bottom p-2">{{ $team->name }}</li>
                            @empty
                            @endforelse
                        </ul>

                        <div class="d-flex w-100 justify-content-center">
                            <form action="{{ route('tournaments.store') }}" method="POST">
                                @csrf

                                <button class="btn btn-primary">Generate Fixtures</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
