<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Controllers\Api\BaseAuthController;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Api\V1_0\UpdateEquipmentRequest;
use App\Http\Requests\Api\V1_0\UpdateSkillsRequest;
use App\Http\Requests\Api\V1_0\UpdateProfileRequest;
use App\Http\Requests\Api\V1_0\UploadAvatarRequest;
use App\Http\Requests\Api\V1_0\CredentialRequest;
use App\Http\Responses\Avatar;
use App\Exceptions\ExceedingIndexException;
use App\City;
use App\Volunteer;
use App\Utils\ArrayUtil;
use App\Transformers\VolunteerProfileTransformer;
use App\Transformers\VolunteerAvatarTransformer;
use App\Services\AvatarStorageService;
use App\Services\JwtService;
use App\Services\TransformerService;
use App\Repositories\CityRepository;
use App\Repositories\VolunteerRepository;

/**
 * The controller manages user's profile, including basic profile, avatar,
 * skills and equipment.
 *
 * @Author: Yi-Ming, Huang <ymhuang>
 * @Date:   2016-04-05T13:43:19+08:00
 * @Email:  ym.huang0808@gmail.com
 * @Project: VMS
 * @Last modified by:   ymhuang
 * @Last modified time: 2016-05-31T13:38:27+08:00
 * @License: GPL-3
 */

class VolunteerProfileController extends BaseAuthController
{
    /**
     * Display the user's profile.
     *
     * It should identify and get user's model object from JWT token.
     * And then, the user model is transformed into array.
     *
     * @return \Illuminate\Http\JsonResponse    user's profile with HTTP 200
     */
    public function showMe()
    {
        $volunteer = $this->jwtService->getVolunteer();

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceItem($volunteer,
            'App\Transformers\VolunteerProfileTransformer', 'volunteer');

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Update volunteer's own profile
     *
     * The request will be validated through
     * `App\Http\Requests\Api\V1_0\UpdateProfileRequest`
     *
     * @param  App\Http\Requests\Api\V1_0\UpdateProfileRequest  request validation
     * @param  App\Repositories\CityRepository
     * @param  App\Repositories\volunteerRepository
     * @return \Illuminate\Http\JsonResponse                    user's profile with HTTP 200
     */
    public function updateMe(UpdateProfileRequest $request, CityRepository $cityRepository,
        VolunteerRepository $volunteerRepository)
    {
        $volunteer = $this->jwtService->getVolunteer();

        if ($request->has('city') && $request->has('city.id')) {
            // Filter some unnecessary fields
            $input = $request->except(['city', 'city.id', 'username', 'password',
                'is_actived', 'is_locked', 'updated_at', 'created_at']);

            $cityInput = $request->input('city.id');
            $city = $cityRepository->findById($cityInput);

            // Update volunteer city
            $volunteerRepository->updateCity($city, $volunteer);
        } else {
            // Filter some unnecessary fields
            $input = $request->except(['username', 'password', 'is_actived', 'is_locked', 'updated_at', 'created_at']);
        }

        // Update volunteer profile
        $volunteer->update($input);

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceItem($volunteer,
            'App\Transformers\VolunteerProfileTransformer', 'volunteer');

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Upload volunteer's avatar
     *
     * @param  UploadAvatarRequest          request validation
     * @param  AvatarStorageService         manage avatar store
     * @return Illuminate\Http\JsonResponse avatar URL with HTTP 200
     */
    public function uploadAvatarMe(UploadAvatarRequest $request, AvatarStorageService $avatarStorageService, Avatar $avatar)
    {
        $volunteer = $this->jwtService->getVolunteer();

        if ($request->has('avatar')) {
            // Get avatar data in base64 format
            $avatarBase64File = $request->input('avatar');
            // Save the avatar data
            $avatarStorageService->save($avatarBase64File);
            $volunteer->avatar_path = $avatarStorageService->getFileName();
            $volunteer->save();
        }

        $manager = TransformerService::getManager();

        $skipProfile = $request->input('skip_profile', false);

        if ($skipProfile) {
            // Not response full profile
            $avatar->avatar_name = $avatarStorageService->getFileName();
            $resource = TransformerService::getResourceItem($avatar, 'App\Transformers\VolunteerAvatarTransformer', 'avatar');

            return response()->json($manager->createData($resource)->toArray(), 200);
        }

        $resource = TransformerService::getResourceItem($avatar,
            'App\Transformers\VolunteerProfileTransformer', 'volunteer');

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Upload avatar without authorization
     *
     * The avatar image is encode by base64
     *
     * @param  App\Http\Requests\Api\V1_0\UploadAvatarRequest   $request
     * @param  App\Services\AvatarStorageService                $avatarStorageService
     * @return \Illuminate\Http\JsonResponse                    avatar URL with HTTP 200
     */
    public function uploadAvatar(UploadAvatarRequest $request, AvatarStorageService $avatarStorageService, Avatar $avatar)
    {
        if ($request->has('avatar')) {
            $avatarBase64File = $request->input('avatar');

            $avatarStorageService->save($avatarBase64File);
            $avatar->avatar_name = $avatarStorageService->getFileName();
        }
        $resource = TransformerService::getResourceItem($avatar, 'App\Transformers\VolunteerAvatarTransformer', 'avatar');
        $manager = TransformerService::getManager();

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Update volunteer's own skills
     *
     * The request body contains new and existing skills.
     * The indexes of existing skills are `existing_skill_indexes`, and
     * the rest of skills will be attached into user.
     *
     * @param  App\Http\Requests\Api\V1_0\UpdateSkillsRequest   $request
     * @return \Illuminate\Http\JsonResponse                    HTTP response 204
     */
    public function updateSkillsMe(UpdateSkillsRequest $request)
    {
        $volunteer = $this->jwtService->getVolunteer();

        $skillsList = $request->input('skills');
        $existingSkillIndexes = $request->input('existing_skill_indexes');

        // Check if the skill array is empty
        if (count($existingSkillIndexes) != 0) {
            $maxIndex = max($existingSkillIndexes);

            if (ArrayUtil::isIndexExceed($skillsList, $maxIndex)) {
                // Index exceeds $skillsList size
                throw new ExceedingIndexException();
            }
        }
        // Get nonexistent skills.
        // The non-existent skills will be created.
        $nonexistentSkills = ArrayUtil::getNonexistent($skillsList, $existingSkillIndexes);

        // If a skill doesn't exist, it will be detach from the user.
        $this->deleteNonUpdatedSkillEquipment($volunteer->skills(), $skillsList, $nonexistentSkills);

        // Update volunteer's nonexistant skills
        foreach ($nonexistentSkills as $skill) {
            $volunteer->skills()
                 ->firstOrCreate(['name' => $skill]);
        }

        return response()->json(null, 204);
    }

    /**
     * Get user's skills
     *
     * @return \Illuminate\Http\JsonResponse    user's skills with HTTP 200
     */
    public function getSkillsMe()
    {
        $volunteer = $this->jwtService->getVolunteer();
        $skills = $volunteer->skills()->get();

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceCollection($skills,
            'App\Transformers\VolunteerSkillTransformer', 'skills');

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Get skill candidated keywords
     *
     * @param  String $keyword
     * @return \Illuminate\Http\JsonResponse    skill candidates with HTTP 200
     */
    public function getSkillCandidatedKeywords($keyword)
    {
        return $this->getCandidatedKeywordsResult('App\Skill', $keyword);
    }

    /**
     * Get equipment candidated keywords
     *
     * @param  String $keyword
     * @return \Illuminate\Http\JsonResponse    equipment candidates with HTTP 200
     */
    public function getEquipmentCandidatedKeywords($keyword)
    {
        return $this->getCandidatedKeywordsResult('App\Equipment', $keyword);
    }

    /**
     * Update volunteer's own equipment
     *
     * The request body contains new and existing equipment.
     * The indexes of existing equipment are `existing_equipment_indexes`, and
     * the rest of equipment will be attached into user.
     *
     * @param  App\Http\Requests\Api\V1_0\UpdateEquipmentRequest $request
     * @return \Illuminate\Http\JsonResponse    no content with HTTP 204
     */
    public function updateEquipmentMe(UpdateEquipmentRequest $request)
    {
        $volunteer = $this->jwtService->getVolunteer();

        $equipmentList = $request->input('equipment');
        $existingEquipmentIndexes = $request->input('existing_equipment_indexes');

        if (count($existingEquipmentIndexes) != 0) {
            $maxIndex = max($existingEquipmentIndexes);

            if (ArrayUtil::isIndexExceed($equipmentList, $maxIndex)) {
                // Index exceeds $equipmentList size
                throw new ExceedingIndexException();
            }
        }

        // Get nonexistent equipment
        $nonexistentEquipment = ArrayUtil::getNonexistent($equipmentList, $existingEquipmentIndexes);

        $this->deleteNonUpdatedSkillEquipment($volunteer->equipment(), $equipmentList, $nonexistentEquipment);

        // Update volunteer's skills
        foreach ($nonexistentEquipment as $equipment) {
            $volunteer->equipment()
                 ->firstOrCreate(['name' => $equipment]);
        }

        return response()->json(null, 204);
    }

    /**
     * Get user's equipment
     *
     * @return \Illuminate\Http\JsonResponse [description]
     */
    public function getEquipmentMe()
    {
        $volunteer = $this->jwtService->getVolunteer();
        $equipment = $volunteer->equipment()->get();

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceCollection($equipment,
            'App\Transformers\VolunteerEquipmentTransformer', 'equipment');

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Delete volunteer's own account
     * @param  App\Http\Requests\Api\V1_0\CredentialRequest    $request
     * @param  App\Services\AvatarStorageService               $avatarStorageService
     * @param  App\Services\JwtService                         $jwtService
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMe(CredentialRequest $request, AvatarStorageService $avatarStorageService,
        JwtService $jwtService)
    {
        $volunteer = $this->jwtService->getVolunteer();
        $credentials = $request->only('username', 'password');

        // Check the credentials
        // If it fails, it will throw an exception
        $jwtService->getToken($credentials);

        $avatarFileName = $volunteer->avatar_path;

        // Check if the volunteer has avatar
        if (!empty($avatarFileName)) {
            // Delete the volunteer's avatar
            $avatarStorageService->delete($avatarFileName);
        }

        $volunteer->delete();

        return response()->json(null, 204);
    }

    /**
     * Get candidated keywords from models
     *
     * @param  String                           $model
     * @param  String                           $keyword
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getCandidatedKeywordsResult($model, $keyword)
    {
        $candidate = $model::keywordName($keyword);

        $resource = TransformerService::getResourceCollection(
            $candidate,
            'App\Transformers\CandidateKeywordsTransformer',
            'result'
        );
        $manager = TransformerService::getManager();

        return response()->json($manager->createData($resource)->toArray(), 200);
    }

    /**
     * Delete non-updated skills or equipment
     *
     * @param Object                            $model
     * @param Array                             $originalList
     * @param Array                             $nonExistenceList
     */
    private function deleteNonUpdatedSkillEquipment($model, $originalList,
        $nonExistenceList)
    {
        $existent = array_diff($originalList, $nonExistenceList);

        if (!empty($existent)) {
            foreach ($model->get() as $value) {
                if (! in_array($value->name, $existent)) {
                    $model->detach($value->id);
                }
            }
        }
    }
}
