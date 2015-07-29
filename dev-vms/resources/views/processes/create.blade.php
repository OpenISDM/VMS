@extends('app')
 
@section('content')
    <h2>Create Process for Project "{{ $project->name }}"</h2>
 
    {!! Form::model(new App\Process, ['route' => ['projects.processes.store', $project->slug], 'class'=>'']) !!}
        @include('processes/partials/_form', ['submit_text' => 'Create Process'])
    {!! Form::close() !!}
@endsection
