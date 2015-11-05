<?php

namespace App\Http\Controllers\Api\V1_0;

use Illuminate\Http\Request;
use Gate;
use App\Http\Requests\Api\V1_0\ExperienceRequest;
use App\Http\Requests\Api\V1_0\UpdateExperienceRequest;
use App\Http\Controllers\Controller;
use App\Exceptions\AccessDeniedException;
use App\Experience;
use App\Transformers\VolunteerExperienceTransformer;
use App\Http\Controllers\Api\BaseVolunteerController;
use App\Services\JwtService;
use App\Services\TransformerService;

class VolunteerExperienceController extends BaseVolunteerController
{
    protected $jwtService;

    public function __construct(JwtService $jwtService)
    {
        parent::__construct();

        $this->jwtService = $jwtService;
    }

    /**
     * Show volunteer's own experiences
     * @return Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $volunteer = $this->jwtService->getVolunteer();
        $experiences = $volunteer->experiences()->get();

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceCollection($experiences,
            'App\Transformers\VolunteerExperienceTransformer', 'experiences');

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Store a new volunteer's experience
     * @param  ExperienceRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(ExperienceRequest $request)
    {
        $volunteer = $this->jwtService->getVolunteer();
        
        $experience = new Experience($request->all());
        $experience = $volunteer->experiences()->save($experience);
        $responseJson = [
            'experience' => [
                'id' => $experience->id
            ]
        ];

        return response()->json($responseJson, 201);
    }

    /**
     * Update an existing volunteer's own experience
     * @param  App\Http\Requests\Api\V1_0\UpdateExperienceRequest $request [description]
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdateExperienceRequest $request)
    {
        $experience = Experience::findOrFail($request->input('id'));

        // Check the App\Policies\VolunteerExperiencePolicy::update()
        if (Gate::denies('update', $experience)) {
            // Forbidden to update
            throw new AccessDeniedException();
        }

        $experience->update($request->except('id'));

        return response()->json(null, 204);
    }

    /**
     * Destroy an existing volunteer's own experience
     * @param  integer $id [description]
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $experience = Experience::findOrFail($id);

        // Check the App\Policies\VolunteerExperiencePolicy::delete()
        if (Gate::denies('delete', $experience)) {
            // Forbidden to delete the experience record
            throw new AccessDeniedException();
        }

        $experience->delete();

        return response()->json(null, 204);
    }
}
