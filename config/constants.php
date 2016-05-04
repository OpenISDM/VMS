<?php

return [

    /**
     * Permissions constants
     */
    'project_permission' => [
        'PUBLIC' => 0,
        'PRIVATE_FOR_USER' => 1,
        'PRIVATE_FOR_MEMBER' => 2,
    ],

    'member_project_status' => [
        'ATTENDING' => 0,
        'PENDING' => 1,
    ],

    'member_project_permission' => [
        'PUBLIC' => 0,
        'PRIVATE_FOR_ALL_ATTENDING_MANAGER' => 1,
        'PRIVATE_FOR_MEMBER' => 2
    ],

    'custom_field_type' => [
        'TEXT' => [
            'number' => 0,
            'metadata' => 'App\CustomField\TextMetadata',
        ],
        'LONGTEXT' => [
            'number' => 1,
            'metadata' => 'App\CustomField\Types\LongTextType',
        ],
        'RADIO_BUTTON' => [
            'number' => 3,
            'metadata' => 'App\CustomField\RadioButtonMetadata',
        ]
    ],
];
