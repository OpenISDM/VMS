<?php

namespace App\Transformers\Volunteer;

use App\Volunteer;
use League\Fractal\TransformerAbstract;

class VolunteerProfileTransformer extends TransformerAbstract
{
    public function transform(Volunteer $volunteer)
    {
        $rootUrl = request()->root();
        $city = $volunteer->city()->first();
        $skills = $volunteer->skills()->get();
        $equipment = $volunteer->equipment()->get();

        $volunteerItem = [
            'username'          => $volunteer->username,
            'first_name'        => $volunteer->first_name,
            'last_name'         => $volunteer->last_name,
            'birth_year'        => (int) $volunteer->birth_year,
            'gender'            => $volunteer->gender,
            'city'              => ['id' => empty($city) ? null : (int) $city->id, 'name_en' => empty($city) ? null : $city->name],
            'location'          => empty($volunteer->location) ? null : $volunteer->location,
            'phone_number'      => empty($volunteer->phone_number) ? null : $volunteer->phone_number,
            'email'             => empty($volunteer->email) ? null : $volunteer->email,
            'emergency_contact' => empty($volunteer->emergency_contact) ? null : $volunteer->emergency_contact,
            'emergency_phone'   => empty($volunteer->emergency_phone) ? null : $volunteer->emergency_phone,
            'introduction'      => empty($volunteer->introduction) ? null : $volunteer->introduction,
            'experiences'       => ['href' => env('APP_URL', $rootUrl).'/api/users/me/experiences'],
            'educations'        => ['href' => env('APP_URL', $rootUrl).'/api/users/me/educations'],
            'skills'            => $skills,
            'equipment'         => $equipment,
            'projects'          => ['href' => env('APP_URL', $rootUrl).'/api/users/me/projects'],
            'processes'         => [
                'participating_number' => 0,
                'participated_number'  => 0,
                'href'                 => env('APP_URL', $rootUrl).'/api/users/me/processes',
            ],
            'avatar_url' => config('vms.avatarHost').'/'.config('vms.avatarRootPath').'/'.$volunteer->avatar_path,
            'is_actived' => $volunteer->is_actived,
            'updated_at' => $volunteer->updated_at,
            'created_at' => $volunteer->created_at,
        ];

        return $volunteerItem;
    }
}
