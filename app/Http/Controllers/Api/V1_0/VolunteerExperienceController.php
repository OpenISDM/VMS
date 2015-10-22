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

class VolunteerExperienceController extends BaseVolunteerController
{
    /**
     * Show volunteer's own experiences
     * @return Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $this->getVolunteerIdentifier();

        $experiences = $this->volunteer->experiences()->get();

        // Set serialzer for a transformer
        $manager = new \League\Fractal\Manager();
        $manager->setSerializer(new \League\Fractal\Serializer\ArraySerializer());

        // transform Experience model into array
        $resource = new \League\Fractal\Resource\Collection(
            $experiences,
            new VolunteerExperienceTransformer,
            'experiences'
        );

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Store a new volunteer's experience
     * @param  ExperienceRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(ExperienceRequest $request)
    {
        $this->getVolunteerIdentifier();
        
        $experience = new Experience($request->all());
        $experience = $this->volunteer->experiences()->save($experience);
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
