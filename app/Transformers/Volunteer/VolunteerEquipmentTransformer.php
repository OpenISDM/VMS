<?php

namespace App\Transformers\Volunteer;

use App\Equipment;
use League\Fractal\TransformerAbstract;

class VolunteerEquipmentTransformer extends TransformerAbstract
{
    public function transform(Equipment $equipment)
    {
        $equipmentItem = [
            'name' => $equipment->name,
        ];

        return $equipmentItem;
    }
}
