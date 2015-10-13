<?php

namespace App\Http\Controllers\Api\V1_0;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Routing\Helpers;
use JWTAuth;
use Gate;
use App\Http\Requests\Api\V1_0\UpdateProfileRequest;
use App\Http\Requests\Api\V1_0\UpdateSkillsRequest;
use App\Http\Requests\Api\V1_0\UpdateEquipmentRequest;
use App\Http\Requests\Api\V1_0\EducationRequest;
use App\Http\Requests\Api\V1_0\UpdateEducationRequest;
use App\Exceptions\AuthenticatedUserNotFoundException;
use App\Exceptions\JWTTokenNotFoundException;
use App\Exceptions\ExceedingIndexException;
use App\Exceptions\AccessDeniedException;
use App\City;
use App\Education;
use App\Utils\ArrayUtil;
use App\Http\Responses\Error;
use App\Utils\StringUtil;
use App\Transformers\VolunteerEducationTransformer;

class VolunteerProfileController extends Controller
{
    use Helpers;

    protected $volunteer;

    public function __construct()
    {
        if (env('APP_ENV') == 'testing' && array_key_exists("HTTP_AUTHORIZATION", request()->server())) {
            JWTAuth::setRequest(\Route::getCurrentRequest());
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMe()
    {
        return 'qqq';
    }

    public function updateMe(UpdateProfileRequest $request)
    {
        $this->getVolunteerIdentifier();

        if ($request->has('city') && $request->has('city.id')) {
            $input = $request->expect(['city', 'city.id']);
            $cityInput = $request->only(['city.id']);
            $city = City::find($cityInput);

            $this->volunteer->city()->associate($city);
            $this->volunteer->save();
        }

        $this->volunteer->update($input);

        // retrive volunteer's profile
    }

    /**
     * Update volunteer's own skills
     * 
     * @param  UpdateSkillsRequest $request
     * @return Response
     */
    public function updateSkillsMe(UpdateSkillsRequest $request)
    {
        $this->getVolunteerIdentifier();

        $skillsList = $request->input('skills');
        $existingSkillIndexes = $request->input('existing_skill_indexes');

        if (count($existingSkillIndexes) != 0) {
            $maxIndex = max($existingSkillIndexes);
            
            if (ArrayUtil::isIndexExceed($skillsList, $maxIndex)) {
                // Index exceeds $skillsList size
                throw new ExceedingIndexException();
            }
        }

        $unexistingSkills = ArrayUtil::getUnexisting($skillsList, $existingSkillIndexes);

        // Update volunteer's skills
        foreach ($unexistingSkills as $skill) {
            $this->volunteer->skills()
                 ->firstOrCreate(['name' => $skill]);
        }

        return response()->json(null, 204);
    }

    /**
     * Update volunteer's own equipment
     * 
     * @param  UpdateEquipmentRequest $request
     * @return Response
     */
    public function updateEquipmentMe(UpdateEquipmentRequest $request)
    {
        $this->getVolunteerIdentifier();

        $equipmentList = $request->input('equipment');
        $existingEquipmentIndexes = $request->input('existing_equipment_indexes');

        if (count($existingEquipmentIndexes) != 0) {
            $maxIndex = max($existingEquipmentIndexes);
            
            if (ArrayUtil::isIndexExceed($equipmentList, $maxIndex)) {
                // Index exceeds $equipmentList size
                throw new ExceedingIndexException();
            }
        }

        $unexistingEquipment = ArrayUtil::getUnexisting($equipmentList, $existingEquipmentIndexes);

        // Update volunteer's skills
        foreach ($unexistingEquipment as $equipment) {
            $this->volunteer->equipment()
                 ->firstOrCreate(['name' => $equipment]);
        }

        return response()->json(null, 204);
    }

    public function showEducationMe()
    {
        $this->getVolunteerIdentifier();

        $educations = $this->volunteer->educations()->get();
        
        $manager = new \League\Fractal\Manager();
        $manager->setSerializer(new \League\Fractal\Serializer\ArraySerializer());

        $resource = new \League\Fractal\Resource\Collection($educations, new VolunteerEducationTransformer, 'education');

        //return $this->response->collection($educations, new VolunteerEducationTransformer, ['key' => 'education']);
        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Store a new education
     * 
     * @param  EducationRequest $request
     * @return Response
     */
    public function storeEducationMe(EducationRequest $request)
    {
        $this->getVolunteerIdentifier();

        $education = new Education($request->all());
        $education = $this->volunteer->educations()->save($education);
        $responseJson = [
            'education_id' => $this->volunteer->username . '_' . $education->id
        ];

        return response()->json($responseJson, 201);
    }

    /**
     * Update volunteer's own education
     * 
     * @param  UpdateEducationRequest $request
     * @return Response
     */
    public function updateEducationMe(UpdateEducationRequest $request)
    {
        $this->getVolunteerIdentifier();
        
        /*
         * TODO: Need to check the id
         */
        $id = StringUtil::getLastId($request->input('education_id'));
        $education = Education::findOrFail($id);

        // Check permission
        if (Gate::denies('update', $education)) {
            // Forbidden to update
            throw new AccessDeniedException();
        }

        $education->update($request->except('education_id'));

        return response()->json(null, 204);
    }

    /**
     * Delete volunteer's own education
     * 
     * @param  Integer $educationId
     */
    public function deleteEducationMe($educationId)
    {
        $this->getVolunteerIdentifier();

        $id = StringUtil::getLastId($educationId);
        $education = Education::findOrFail($id);

        // Check permission
        if (Gate::denies('delete', $education)) {
            // Forbidden to delete the education record
            throw new AccessDeniedException();
        }

        $education->delete();

        return response()->json(null, 204);
    }

    protected function getVolunteerIdentifier()
    {
        try {
            if (! $this->volunteer = JWTAuth::parseToken()->authenticate()) {
                throw new AuthenticatedUserNotFoundException();
            }
        } catch (JWTException $e) {
            throw new JWTTokenNotFoundException($e);
        }
    }
}
