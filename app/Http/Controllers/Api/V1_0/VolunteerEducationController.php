<?php

namespace App\Http\Controllers\Api\V1_0;

use Illuminate\Http\Request;
use App\Http\Requests\Api\V1_0\EducationRequest;
use App\Http\Requests\Api\V1_0\UpdateEducationRequest;
use Gate;
use App\Http\Controllers\Api\BaseAuthController;
use App\Exceptions\AccessDeniedException;
use App\Transformers\Volunteer\VolunteerEducationTransformer;
use App\Education;
use App\Services\JwtService;
use App\Services\TransformerService;

/**
 * The contoller provides user to show, store, update and destroy
 * his/her own education.
 *
 * @Author: Yi-Ming, Huang <ymhuang>
 * @Date:   2016-04-05T13:43:19+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   aming
 * @Last modified time: 2016-06-04T17:53:11+08:00
 * @License: GPL-3
 */
class VolunteerEducationController extends BaseAuthController
{
    /**
     * Show volunteer's own existing educations
     * @return Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $volunteer = $this->jwtService->getVolunteer();

        $educations = $volunteer->educations()->get();

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceCollection($educations,
            'App\Transformers\Volunteer\VolunteerEducationTransformer', 'educations');

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Store a new education
     * @param  App\Http\Requests\Api\V1_0\EducationRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(EducationRequest $request)
    {
        $volunteer = $this->jwtService->getVolunteer();

        $education = new Education($request->all());
        $education = $volunteer->educations()->save($education);
        $responseJson = [
            'education' => [
                'id' => (int) $education->id
            ]
        ];

        return response()->json($responseJson, 201);
    }

    /**
     * Update volunteer's own education
     * @param  App\Http\Requests\Api\V1_0\UpdateEducationRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function update(UpdateEducationRequest $request)
    {
        $education = Education::findOrFail($request->input('id'));

        // Check the App\Policies\VolunteerEducationPolicy::update()
        if (Gate::denies('update', $education)) {
            // Forbidden to update
            throw new AccessDeniedException();
        }

        $education->update($request->except('id'));

        return response()->json(null, 204);
    }

    /**
     * Delete volunteer's own education
     * @param  Integer $educationId
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $education = Education::findOrFail($id);

        // // Check the App\Policies\VolunteerEducationPolicy::update()
        if (Gate::denies('delete', $education)) {
            // Forbidden to delete the education record
            throw new AccessDeniedException();
        }

        $education->delete();

        return response()->json(null, 204);
    }
}
