<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Exceptions\AccessDeniedException;
use App\Http\Controllers\Api\BaseAuthController;
use App\Http\Requests\Api\V1_0\AttachVolunteerInProjectRequest;
use App\Http\Requests\Api\V1_0\CreateProjectRequest;
use App\Http\Requests\Api\V1_0\DetachVolunteerInProjectRequest;
use App\Http\Requests\Api\V1_0\InviteVolunteerInProjectRequest;
use App\Http\Requests\Api\V1_0\ShowProjectRequest;
use App\Http\Requests\Api\V1_0\UpdateProjectRequest;
use App\Project;
use App\Repositories\ProjectDbQueryRepository;
use App\Services\TransformerService;
use App\Transformers\Project\ProjectTransformer;
use App\Volunteer;
use Gate;

class ProjectController extends BaseAuthController
{
    /**
     * Create a new project.
     *
     * @param CreateProjectRequest $request
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function store(CreateProjectRequest $request)
    {
        $user = $this->jwtService->getUser();

        // Retrive the project input from the request
        $data = $request->only([
            'name',
            'description',
            'organization',
            'is_published',
            'permission',
        ]);

        $project = Project::create($data);

        // Assign the project owner
        $user->manageProjects()->attach($project);

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceItem($project,
            'App\Transformers\JsonApiProjectTransformer', 'data');

        return response()->json($manager->createData($resource)->toArray(), 201);
    }

    /**
     * Show a particular project information.
     *
     * @param int $id
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Get the project model by $id
        $project = Project::findOrFail($id);
        $user = $this->jwtService->getUser();

        if (!$this->isPublic($project)) {

            // Check the permission
            if (Gate::denies('show', $project)) {
                // Forbidden to delete the experience record
                throw new AccessDeniedException();
            }
        }

        return $this->response
                    ->item($project, new ProjectTransformer())
                    ->addMeta('role', $this->getRoleInProject($user, $project));
    }

    public function delete($id)
    {
        /*
         * TODO To be implemented
         */
    }

    /**
     * Update a project.
     *
     * @param UpdateProjectRequest $request For authorization and rules validation
     * @param int                  $id      Project id
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        $data = $request->only([
            'name',
            'description',
            'organization',
            'is_published',
            'permission',
        ]);

        $project->update($data);

        $manager = TransformerService::getJsonApiManager();
        $resource = TransformerService::getResourceItem($project,
            'App\Transformers\JsonApiProjectTransformer', 'projects');

        $manager->parseIncludes(['managers', 'hyperlinks']);

        $result = $manager->createData($resource)->toArray();
        $status = 200;

        return response()->json($result, $status);
    }

    public function showManagedProjects()
    {
        $user = $this->jwtService->getUser();
        $projects = $user->manageProjects()->get();

        return $this->collection($projects, new ProjectTransformer());
    }

    public function showSelfAttendingProjects()
    {
        $volunteer = $this->jwtService->getUser();
        $projects = $volunteer->attendingProjects()->get();

        $manager = TransformerService::getJsonApiManager();
        $resource = TransformerService::getResourceCollection($projects,
            'App\Transformers\JsonApiProjectTransformer', 'projects');

        $manager->parseIncludes(['managers', 'hyperlinks']);

        $result = $manager->createData($resource)->toArray();
        $status = 200;

        return response()->json($result, $status);
    }

    public function showAll(ProjectDbQueryRepository $repository)
    {
        $user = $this->jwtService->getUser();

        $projects = $repository->getViewableProjects($user);
        $projectsCollection = collect($projects);
        // var_dump($projectsCollection);

        if (!empty($projects)) {
            $manager = TransformerService::getManager();
            $resource = TransformerService::getResourceCollection($projectsCollection,
                'App\Transformers\Project\ProjectBriefTransformer', 'data');

            // $manager->parseIncludes(['managers', 'hyperlinks']);

            $result = $manager->createData($resource)->toArray();
            $status = 200;
        } else {
            $result = [];
            $status = 200;
        }

        return response()->json($result, $status);
    }

    /**
     * For the project manager to invite a volunteer to become member of his/her project.
     */
    public function inviteVolunteer(InviteVolunteerInProjectRequest $request, $projectId)
    {
        // get the ids of to-be-invited volunteers from the $request
        $volunteersId = $request->input('volunteers.*.id');
        // check if these volunteers can be added to project
        $volunteers = Volunteer::find($volunteersId);
        $project = Project::find($projectId);
        $checked = true;

        $volunteers->each(function ($volunteer) use ($project, $checked) {
            if ($volunteer->inProject($project)) {
                $checked = false;

                return false;
            }
        });

        // if yes, invite them to the project, send them an email to notify that they
        // are being invited to this project. Then respond 200
        //
        // if no, respond error
        // resond
    }

    /**
     * Attch a volunteer in a project.
     *
     * @param AttachVolunteerInProjectRequest $request
     * @param int                             $id      Project id
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function attachVolunteer(AttachVolunteerInProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);

        $userId = $request->input('data.attributes.volunteer_id');
        $user = Volunteer::find($userId);

        $user->attachProject($project, config('constants.member_project_permission.PRIVATE_FOR_ALL_ATTENDING_MANAGER'));

        return response()->json([], 204);
    }

    public function attend($projectId)
    {
        $volunteer = $this->jwtService->getUser();
        $project = Project::findOrFail($projectId);

        if (!$volunteer->inProject($project)) {
            // The volunteer is not in the project
            $volunteer->attachProject(
                $project,
                config('constants.member_project_permission.PRIVATE_FOR_ALL_ATTENDING_MANAGER')
            );

            $result = [];
            $status = 204;
        } else {
            // The volunteer is already in the project
            // Throw a exception
            throw new s\GeneralException(
                'Exists in the project',
                'exists_in_the_project',
                409
            );
        }

        return response()->json($result, $status);
    }

    /**
     * Detach a volunteer from a project.
     *
     * @param DetachVolunteerInProjectRequest $request
     * @param int                             $projectId
     * @param int                             $userId    [description]
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function detachVolunteer(DetachVolunteerInProjectRequest $request, $projectId, $userId)
    {
        $project = Project::findOrFail($projectId);
        $user = Volunteer::find($userId);
        $user->detachProject($project);

        return response()->json([], 204);
    }

    public function showMembers(ShowProjectRequest $request, $projectId)
    {
        $user = $this->jwtService->getUser();
        $project = Project::findOrFail($projectId);

        if ($user->isCreatorOfProject($project)) {
            $members = $project->members()->get();
        } else {
            // Not a project manager
            $members = $project->viewableMembers($user, $project);
        }

        $manager = TransformerService::getJsonApiManager();
        $resource = TransformerService::getResourceCollection($members,
            'App\Transformers\JsonApiMemberTransformer', 'members');

        $result = $manager->createData($resource)->toArray();
        $status = 200;

        return response()->json($result, $status);
    }

    public function showPSPMembers(ShowProjectRequest $request, $projectId)
    {
        $user = $this->jwtService->getUser();
        $project = Project::findOrFail($projectId);

        if ($user->isCreatorOfProject($project)) {
            $members = $project->members()->get();
        } else {
            // Not a project manager
            $members = $project->viewableMembers($user, $project);
        }

        $manager = TransformerService::getJsonApiManager();
        $resource = TransformerService::getResourceCollection($members,
            'App\Transformers\JsonApiMemberTransformer', 'members');

        $result = $manager->createData($resource)->toArray();
        $status = 200;

        return response()->json($result, $status);
    }

    public function addPMs(InviteVolunteerInProjectRequest $request, $projectId)
    {

        //echo "I'm herer\n";

         // get the ids of to-be-promoted volunteers from the $request
         // by promoted, we mean, to become a PM of the given project
        $volunteersId = $request->input('volunteers.*.id');
        // check if these volunteers can be added to project
        $volunteers = Volunteer::find($volunteersId);
        //echo $volunteers[0];
        $user = $this->jwtService->getUser();
        //echo $user;
        $project = Project::findOrFail($projectId);
        // if this is a pm of the project
        if ($user->isCreatorOfProject($project)) {
            $pms = $project->managers()->get();
            // Assign the project owner
            $volunteers[0]->manageProjects()->attach($project);

            return response()->json([], 204);
        } else {
            return response()->json([], 400);
        }
    }

    // add parameters
    public function showPMs(ShowProjectRequest $request, $projectId)
    {
        $user = $this->jwtService->getUser();
        $project = Project::findOrFail($projectId);
        // if this is a pm of the project
        if ($user->isCreatorOfProject($project)) {
            $pms = $project->managers()->get();
        } else {
            // return error status
        }

        $manager = TransformerService::getJsonApiManager();
        $resource = TransformerService::getResourceCollection($pms,
            'App\Transformers\JsonApiManagerTransformer', 'pms');
        $result = $manager->createData($resource)->toArray();
        $status = 200;

        return response()->json($result, $status);
    }

    protected function isPublic(Project $project)
    {
        if ($project->is_published === true) {
            return $project->permission == config('constants.project_permission.PUBLIC');
        }

        return false;
    }

    protected function getRoleInProject(Volunteer $user, Project $project)
    {
        $role = [];

        if ($user->isCreatorOfProject($project)) {
            $role = [
                'name' => 'creator',
            ];
        } elseif ($user->inProject($project)) {
            $role = [
                'name' => 'member',
            ];

            if ($user->isAttendingProject($project)) {
                $role['status'] = 'attending';
            } elseif ($user->isPendingProject($project)) {
                $role['status'] = 'pending';
            }
        } else {
            $role = [
                'name' => 'guest',
            ];
        }

        return $role;
    }
}
