@extends('app')
 
@section('content')
    <h2>{{ $project->name }}</h2>
 
    @if ( !$project->processes->count() )
        Your project has no processes.
    @else
        <ul>
            @foreach( $project->processes as $process )
                <li><a href="{{ route('projects.processes.show', [$project->slug, $process->slug]) }}">{{ $process->name }}</a></li>
            @endforeach
        </ul>
    @endif
@endsection
