<?php

namespace App\Http\Controllers\Api\V1_0;

use Illuminate\Support\Arr;
use Gate;
use App\Http\Controllers\Api\BaseAuthController;
use App\Http\Requests\Api\V1_0\CreateProjectRequest;
use App\Http\Requests\Api\V1_0\ShowProjectRequest;
use App\Http\Requests\Api\V1_0\UpdateProjectRequest;
use App\Http\Requests\Api\V1_0\AttachVolunteerInProjectRequest;
use App\Http\Requests\Api\V1_0\DetachVolunteerInProjectRequest;
use App\Project;
use App\Hyperlink;
use App\Volunteer;
use App\Services\TransformerService;
use App\Exceptions\GeneralException;
use App\Exceptions\AccessDeniedException;
use App\Repositories\ProjectDbQueryRepository;
use App\Utils\ArrayUtil;
use App\Transformers\ProjectTransformer;

class ProjectController extends BaseAuthController
{
    /**
     * Create a new project
     *
     * @param  CreateProjectRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(CreateProjectRequest $request)
    {
        $user = $this->jwtService->getUser();

        // Retrive the project input from the request
        $data = $request->only([
            'data.attributes.name',
            'data.attributes.description',
            'data.attributes.organization',
            'data.attributes.is_published',
            'data.attributes.permission'
        ]);
        $projectData = Arr::get($data, 'data.attributes');

        $project = Project::create($projectData);

        // Assign the project owner
        $user->manageProjects()->attach($project);

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceItem($project,
            'App\Transformers\JsonApiProjectTransformer', 'data');

        return response()->json($manager->createData($resource)->toArray(), 201);
    }

    /**
     * Show a particular project information
     *
     * @param  integer      $id
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

        $manager = TransformerService::getJsonApiManager();
        $resource = TransformerService::getResourceItem($project,
            'App\Transformers\JsonApiProjectTransformer', 'projects');

        // set meta for the resource
        $meta = [
            'role' => $this->getRoleInProject($user, $project)
        ];
        $resource->setMeta($meta);

        $manager->parseIncludes(['managers', 'hyperlinks']);

        $result = $manager->createData($resource)->toArray();
        $status = 200;

        return response()->json($result, $status);
    }

    public function delete($id)
    {
        /**
         * TODO To be implemented
         */
    }

    /**
     * Update a project
     *
     * @param  UpdateProjectRequest $request For authorization and rules validation
     * @param  integer              $id      Project id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        $data = $request->only([
            'data.attributes'
        ]);
        $projectData = Arr::get($data, 'data.attributes');

        $project->update($projectData);

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

        $manager = TransformerService::getJsonApiManager();
        $resource = TransformerService::getResourceCollection($projects,
            'App\Transformers\JsonApiProjectTransformer', 'projects');

        $manager->parseIncludes(['managers', 'hyperlinks']);

        $result = $manager->createData($resource)->toArray();
        $status = 200;

        return response()->json($result, $status);
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

        if (!empty($projects)) {
            $manager = TransformerService::getJsonApiManager();
            $resource = TransformerService::getResourceCollection($projects,
                'App\Transformers\JsonApiProjectArrayTransformer', 'projects');

            $manager->parseIncludes(['managers', 'hyperlinks']);

            $result = $manager->createData($resource)->toArray();
            $status = 200;
        } else {
            $result = [];
            $status = 200;
        }

        return response()->json($result, $status);
    }

    /**
     * Attch a volunteer in a project
     * @param  AttachVolunteerInProjectRequest $request
     * @param  integer                          $id      Project id
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
     * Detach a volunteer from a project
     * @param  DetachVolunteerInProjectRequest $request
     * @param  integer                         $projectId
     * @param  integer                         $userId    [description]
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

    protected function isPublic(Project $project)
    {
        if ($project->is_published === true) {
            return ($project->permission == config('constants.project_permission.PUBLIC'));
        }

        return false;
    }

    protected function getRoleInProject(Volunteer $user, Project $project)
    {
        $role = [];

        if ($user->isCreatorOfProject($project)) {
            $role = [
                'name' => 'creator'
            ];
        } elseif ($user->inProject($project)) {
            $role = [
                'name' => 'member'
            ];

            if ($user->isAttendingProject($project)) {
                $role['status'] = 'attending';
            } elseif ($user->isPendingProject($project)) {
                $role['status'] = 'pending';
            }
        } else {
            $role = [
                'name' => 'guest'
            ];
        }

        return $role;
    }
}
