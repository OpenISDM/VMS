@extends('app')
 
@section('content')
    <h2>{{ $project->name }}</h2>
 
    @if ( !$project->processes->count() )
        Your project has no processes.
    @else
        <ul>
            @foreach( $project->processes as $process )
                <li>
                    {!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('projects.processes.destroy', $project->slug, $process->slug))) !!}
                        <a href="{{ route('projects.processes.show', [$project->slug, $process->slug]) }}">{{ $process->name }}</a>
                        (
                            {!! link_to_route('projects.processes.edit', 'Edit', array($project->slug, $process->slug), array('class' => 'btn btn-info')) !!},
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger')) !!}
                        )
                    {!! Form::close() !!}
                </li>
            @endforeach
        </ul>
    @endif

    <p>
        {!! link_to_route('projects.index', 'Back to Projects') !!} |
        {!! link_to_route('projects.processes.create', 'Create Process', $project->slug) !!}
    </p>
@endsection
