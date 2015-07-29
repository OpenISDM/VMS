@extends('app')
 
@section('content')
    <h2>Edit Process "{{ $process->name }}"</h2>
 
    {!! Form::model($process, ['method' => 'PATCH', 'route' => ['projects.processes.update', $project->slug, $process->slug]]) !!}
        @include('processes/partials/_form', ['submit_text' => 'Edit Task'])
    {!! Form::close() !!}
@endsection
