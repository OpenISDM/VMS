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
use App\Http\Controllers\Api\BaseAuthController;
use App\Services\JwtService;
use App\Services\TransformerService;

/**
 * The controller provides user to get, store, update and destroy
 * his/her own experience.
 *
 * @Author: Yi-Ming, Huang <ymhuang>
 * @Date:   2016-04-05T13:43:19+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   ymhuang
 * @Last modified time: 2016-05-30T15:35:03+08:00
 * @License: GPL-3
 */
class VolunteerExperienceController extends BaseAuthController
{
    /**
     * Show user's own experiences
     * @return \Illuminate\Http\JsonResponse
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
     * Store a new user's experience
     * @param  ExperienceRequest $request
     * @return \Illuminate\Http\JsonResponse
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
     * Update an existing user's own experience
     * @param  App\Http\Requests\Api\V1_0\UpdateExperienceRequest $request
     * @return \Illuminate\Http\JsonResponse
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
     * Destroy an existing user's own experience
     *
     * @param  integer $id
     * @return \Illuminate\Http\JsonResponse
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
