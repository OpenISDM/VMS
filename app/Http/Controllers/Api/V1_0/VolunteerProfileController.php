<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Controllers\Api\BaseVolunteerController;
use Dingo\Api\Routing\Helpers;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Api\V1_0\UpdateEquipmentRequest;
use App\Http\Requests\Api\V1_0\UpdateSkillsRequest;
use App\Http\Requests\Api\V1_0\UpdateProfileRequest;
use App\Http\Requests\Api\V1_0\UploadAvatarRequest;
use App\Http\Requests\Api\V1_0\CredentialRequest;
use App\Http\Responses\Error;
use App\Exceptions\ExceedingIndexException;
use App\City;
use App\Volunteer;
use App\Utils\ArrayUtil;
use App\Transformers\VolunteerProfileTransformer;
use App\Services\AvatarStorageService;

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
        $this->getVolunteerIdentifier();

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
        $this->getVolunteerIdentifier();
        
        if ($request->has('city') && $request->has('city.id')) {
            $input = $request->except(['city', 'city.id', 'username', 'password', 'is_actived', 'is_locked', 'updated_at', 'created_at']);
            $cityInput = $request->input('city.id');
            $city = City::find($cityInput);

            $this->volunteer->city()->associate($city);
            $this->volunteer->save();
        } else {
            $input = $request->except(['username', 'password', 'is_actived', 'is_locked', 'updated_at', 'created_at']);
        }

        $this->volunteer->update($input);

        // retrive volunteer's profile
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

    public function uploadAvatarMe(UploadAvatarRequest $request)
    {
        $this->getVolunteerIdentifier();

        if ($request->has('avatar')) {
            $avatarBase64File = $request->input('avatar');
            $avatarStorageService = new AvatarStorageService();

            $avatarStorageService->save($avatarBase64File);

            $this->volunteer->avatar_path = $avatarStorageService->getFileName();
            $this->volunteer->save();
        }
        
        $skipProfile = $request->input('skip_profile', false);

        if ($skipProfile) {
            // Not response full profile
            $rootUrl = request()->root();

            $responseJson = [
                'avatar_url' => config('vms.avatarHost') . '/' . config('vms.avatarRootPath') . '/' . $avatarStorageService->getFileName(),
                'avatar_name' => $avatarStorageService->getFileName()
            ];

            return response()->json($responseJson, 200);
        }

        // Set serialzer for a transformer
        $manager = new \League\Fractal\Manager();
        $manager->setSerializer(new \League\Fractal\Serializer\ArraySerializer());

        // transform Experience model into array
        $resource = new \League\Fractal\Resource\Item(
            $this->volunteer,
            new VolunteerProfileTransformer,
            'volunteer'
        );

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    public function uploadAvatar(UploadAvatarRequest $request)
    {
        if ($request->has('avatar')) {
            $avatarBase64File = $request->input('avatar');
            $avatarStorageService = new AvatarStorageService();

            $avatarStorageService->save($avatarBase64File);
        }

        $rootUrl = request()->root();

        $responseJson = [
            'avatar_url' => config('vms.avatarHost') .
                            '/' . config('vms.avatarRootPath') .
                            '/' . $avatarStorageService->getFileName(),
            'avatar_name' => $avatarStorageService->getFileName()
        ];

        return response()->json($responseJson, 200);
    }

    /**
     * Update volunteer's own skills
     * @param  App\Http\Requests\Api\V1_0\UpdateSkillsRequest $request
     * @return Illuminate\Http\JsonResponse
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
     * @param  App\Http\Requests\Api\V1_0\UpdateEquipmentRequest $request
     * @return Illuminate\Http\JsonResponse
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

    /**
     * Delete volunteer's own account
     * @param  CredentialRequest $request
     * @return JsonResonse
     */
    public function deleteMe(CredentialRequest $request)
    {
        $this->getVolunteerIdentifier();

        $credentials = $request->only('username', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                $message = 'Authentication failed';
                $error = new Error('incorrect_login_credentials');
                $statusCode = 401;

                return response()->apiJsonError($message, $error, $statusCode);
            }
        } catch (JWTException $e) {
            $message = 'Server error';
            $error = new Error('unable_to_authenticate');
            $statusCode = 500;

            return response()->apiJsonError($message, $error, $statusCode);
        }

        $avatarStorageService = new AvatarStorageService();
        $avatarFileName = $this->volunteer->avatar_path;

        if (!empty($avatarFileName)) {
            $avatarStorageService->delete($avatarFileName);
        }

        $this->volunteer->delete();

        return response()->json(null, 204);
    }
}
