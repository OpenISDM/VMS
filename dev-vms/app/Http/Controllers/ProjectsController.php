<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Input;
use Redirect;

use App\Project;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProjectsController extends Controller
{

    protected $rules = [
        'name' => ['required', 'min:3'],
        'slug' => ['required'],
        'description' => ['required'],
        'start_date' => ['date'],
        'end_date' => ['date', 'after:start_date'],
    ];

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, $this->rules);
        
        $input = Input::all();

        $input['is_ongoing'] = (Input::has('is_ongoing') ? true : false);

        $start_time = strtotime($input['start_date']);
        $start_newformat = date('Y-m-d',$start_time);
        $input['start_date'] = $start_newformat;

        if (!$input['is_ongoing']) {
            $end_time = strtotime($input['end_date']);
            $end_newformat = date('Y-m-d',$end_time);
            $input['end_date'] = $end_newformat;
        } else {
            $input['end_date'] = null;
        }

        Project::create( $input );

        return Redirect::route('projects.index')->with('message', 'Project created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Project $project)
    {
        //
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Project $project)
    {
        //
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Project $project)
    {
        //
        $this->validate($request, $this->rules);
        
        $input = array_except(Input::all(), '_method');

        $input['is_ongoing'] = (Input::has('is_ongoing') ? true : false);

        $start_time = strtotime($input['start_date']);
        $start_newformat = date('Y-m-d',$start_time);
        $input['start_date'] = $start_newformat;

        if (!$input['is_ongoing']) {
            $end_time = strtotime($input['end_date']);
            $end_newformat = date('Y-m-d',$end_time);
            $input['end_date'] = $end_newformat;
        } else {
            $input['end_date'] = null;
        }

        $project->update($input);
 
        return Redirect::route('projects.show', $project->slug)->with('message', 'Project updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Project $project)
    {
        //
        $project->delete();
 
        return Redirect::route('projects.index')->with('message', 'Project deleted.');
    }
}
