<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Controllers\Api\BaseVolunteerController;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests\Api\V1_0\UpdateEquipmentRequest;
use App\Http\Requests\Api\V1_0\UpdateSkillsRequest;
use App\Http\Requests\Api\V1_0\UpdateProfileRequest;
use App\Exceptions\ExceedingIndexException;
use App\City;
use App\Volunteer;
use App\Utils\ArrayUtil;
use App\Transformers\VolunteerProfileTransformer;

class VolunteerProfileController extends BaseVolunteerController
{
    use Helpers;

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMe()
    {        
        // Set serialzer for a transformer
        $manager = new \League\Fractal\Manager();
        $manager->setSerializer(new \League\Fractal\Serializer\ArraySerializer());

        // transform Experience model into array
        $resource = new \League\Fractal\Resource\Item(
            $this->volunteer,
            new VolunteerProfileTransformer,
            'volunteer');

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Update volunteer's own profile
     * @param  App\Http\Requests\Api\V1_0\UpdateProfileRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function updateMe(UpdateProfileRequest $request)
    {
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
     * @param  App\Http\Requests\Api\V1_0\UpdateSkillsRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function updateSkillsMe(UpdateSkillsRequest $request)
    {
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
     * @param  App\Http\Requests\Api\V1_0\UpdateEquipmentRequest $request
     * @return Illuminate\Http\JsonResponse
     */
    public function updateEquipmentMe(UpdateEquipmentRequest $request)
    {
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
}
