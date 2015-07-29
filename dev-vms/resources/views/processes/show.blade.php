@extends('app')
 
@section('content')
    <h2>
        {!! link_to_route('projects.show', $project->name, [$project->slug]) !!} -
        {{ $process->name }}
    </h2>
 
    {{ $process->description }}
@endsection
