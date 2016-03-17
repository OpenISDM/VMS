<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Equipment;

class VolunteerEquipmentTransformer extends TransformerAbstract
{
    public function transform(Equipment $equipment)
    {
        $equipmentItem = [
            'name' => $equipment->name
        ];

        return $equipmentItem;
    }
}
