<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Input;
use Redirect;

use App\Project;
use App\Process;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProcessesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Project $project)
    {
        //
        return view('processes.index', compact('project'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Project $project)
    {
        return view('processes.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request, Project $project)
    {
        //
        $input = Input::all();
        $input['project_id'] = $project->id;
        Process::create( $input );
 
        return Redirect::route('projects.show', $project->slug)->with('message', 'Process created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Project $project, Process $process)
    {
        //
        return view('processes.show', compact('project', 'process'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Project $project, Process $process)
    {
        //
        return view('processes.edit', compact('project', 'process'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Project $project, Process $process)
    {
        //
        $input = array_except(Input::all(), '_method');
        $task->update($input);
 
        return Redirect::route('projects.processes.show', [$project->slug, $process->slug])->with('message', 'Process updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Project $project, Process $process)
    {
        //
        $process->delete();
 
        return Redirect::route('projects.show', $project->slug)->with('message', 'Process deleted.');
    }
}
