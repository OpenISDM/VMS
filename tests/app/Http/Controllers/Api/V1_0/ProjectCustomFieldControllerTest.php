<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\ProjectCustomField;
use App\CustomField\RadioButtonMetadata;
use App\CustomField\Payload;
use App\MemberCustomFieldData;

class ProjectCustomFieldControllerTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function testStoreSuccessfully()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $project = factory(App\Project::class)->create();
        $hyperlink = factory(App\Hyperlink::class)->make();

        $project->hyperlinks()->save($hyperlink);
        $project->managers()->save($this->volunteer);

        $postData = [
            'data' => [
                'type' => 'project_custom_field',
                'attributes' => [
                    'name' => 'FOFOLOlOLo',
                    'type' => 'RADIO_BUTTON',
                    'description' => 'FooOFoooQooOOOqOqf',
                    'required' => true,
                    'metadata' => [
                        'options' => [
                            [
                                'value' => 0,
                                'display_name' => 'abc',
                            ],
                            [
                                'value' => 1,
                                'display_name' => 'def',
                            ],
                            [
                                'value' => 2,
                                'display_name' => 'ghi',
                            ],
                        ]
                    ],
                    'order' => 1
                ]
            ]
        ];

        $this->json(
            'post',
            '/api/projects/' . $project->id . '/custom_fields',
            $postData,
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(201);

        $this->seeInDatabase('project_custom_field', [
            'name' => 'FOFOLOlOLo',
            'project_id' => $project->id
        ]);
    }

    public function testStoreWithTextSuccessfully()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $project = factory(App\Project::class)->create();
        $hyperlink = factory(App\Hyperlink::class)->make();

        $project->hyperlinks()->save($hyperlink);
        $project->managers()->save($this->volunteer);

        $postData = [
            'data' => [
                'type' => 'project_custom_field',
                'attributes' => [
                    'name' => 'FOFOLOlOLo',
                    'type' => 'TEXT',
                    'description' => 'FooOFoooQooOOOqOqf',
                    'required' => true,
                    'order' => 1
                ]
            ]
        ];

        $this->json(
            'post',
            '/api/projects/' . $project->id . '/custom_fields',
            $postData,
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(201);

        $this->seeInDatabase('project_custom_field', [
            'name' => 'FOFOLOlOLo',
            'project_id' => $project->id
        ]);
    }

    public function testStoreWithUpdate()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $project = factory(App\Project::class)->create();
        $hyperlink = factory(App\Hyperlink::class)->make();

        $project->hyperlinks()->save($hyperlink);
        $project->managers()->save($this->volunteer);

        $radioCustomField1 = factory(App\ProjectCustomField::class)->make([
            'order' => 1,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'abc',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'def',
                    ],
                ]
            ])
        ]);
        $radioCustomField2 = factory(App\ProjectCustomField::class)->make([
            'order' => 2,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'qoo',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'foo',
                    ],
                ]
            ])
        ]);

        $project->customFields()->saveMany([$radioCustomField1, $radioCustomField2]);

        $postData = [
            'data' => [
                'id' => $radioCustomField2->id,
                'type' => 'project_custom_field',
                'attributes' => [
                    'name' => 'FOFOLOlOLo',
                    'type' => 'RADIO_BUTTON',
                    'description' => 'FooOFoooQooOOOqOqf',
                    'required' => true,
                    'order' => 2,
                    'metadata' => [
                        'options' => [
                            [
                                'value' => 0,
                                'display_name' => 'abc',
                            ],
                            [
                                'value' => 1,
                                'display_name' => 'def',
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $this->json(
            'post',
            '/api/projects/' . $project->id . '/custom_fields',
            $postData,
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(201);
    }

    public function testShowAll()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $project = factory(App\Project::class)->create();
        $hyperlink = factory(App\Hyperlink::class)->make();

        $project->hyperlinks()->save($hyperlink);
        $project->managers()->save($this->volunteer);

        $radioCustomField1 = factory(App\ProjectCustomField::class)->make([
            'order' => 1,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'abc',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'def',
                    ],
                ]
            ])
        ]);
        $radioCustomField2 = factory(App\ProjectCustomField::class)->make([
            'order' => 2,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'qoo',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'foo',
                    ],
                ]
            ])
        ]);

        $project->customFields()->saveMany([$radioCustomField1, $radioCustomField2]);

        $this->json(
            'get',
            '/api/projects/' . $project->id . '/custom_fields',
            [],
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(200);
    }

    public function testFillCustomField()
    {
        $this->factoryModel();

        $user1 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user2 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $project = $this->makeCreateProjectWithPrivateForUser($user1, 1, true);

        $user2->attachProject($project, 0);

        $radioCustomField1 = factory(App\ProjectCustomField::class)->make([
            'order' => 1,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'abc',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'def',
                    ],
                ]
            ])
        ]);
        $radioCustomField2 = factory(App\ProjectCustomField::class)->make([
            'order' => 2,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'qoo',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'foo',
                    ],
                ]
            ])
        ]);

        $project->customFields()->saveMany([$radioCustomField1, $radioCustomField2]);

        $postData = [
            'data' => [
                'type' => 'project_custom_field_data',
                'attributes' => [
                    'content' => [
                        'options' => [
                            'value' => 0
                        ]
                    ]
                ],
                'relationships' => [
                    'custom_field' => [
                        'data' => [
                            'type' => 'custom_fields',
                            'id' => $radioCustomField1->id
                        ]
                    ]
                ]
            ]
        ];

        $this->json(
            'post',
            '/api/projects/' . $project->id . '/members/custom_field_data',
            $postData,
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user2),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(201);

        $this->seeInDatabase('member_custom_field_data', [
            'project_custom_field_id' => $radioCustomField1->id
        ]);
    }

    public function testFillBulkCustomFields()
    {
        $this->factoryModel();

        $user1 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user2 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $project = $this->makeCreateProjectWithPrivateForUser($user1, 1, true);

        $user2->attachProject($project, 0);

        $radioCustomField1 = factory(App\ProjectCustomField::class)->make([
            'order' => 1,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'abc',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'def',
                    ],
                ]
            ])
        ]);
        $radioCustomField2 = factory(App\ProjectCustomField::class)->make([
            'order' => 2,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'qoo',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'foo',
                    ],
                ]
            ])
        ]);

        $project->customFields()->saveMany([$radioCustomField1, $radioCustomField2]);

        $postData = [
            'data' => [
                [
                    'type' => 'project_custom_field_data',
                    'attributes' => [
                        'data' => [
                            'options' => [
                                'value' => 0
                            ]
                        ]
                    ],
                    'relationships' => [
                        'project_custom_field' => [
                            'data' => [
                                'type' => 'custom_fields',
                                'id' => $radioCustomField1->id
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'project_custom_field_data',
                    'attributes' => [
                        'data' => [
                            'options' => [
                                'value' => 0
                            ]
                        ]
                    ],
                    'relationships' => [
                        'project_custom_field' => [
                            'data' => [
                                'type' => 'custom_fields',
                                'id' => $radioCustomField2->id
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->json(
            'post',
            '/api/projects/' . $project->id . '/members/bulk_custom_field_data',
            $postData,
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user2),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(201);

        $this->seeInDatabase('member_custom_field_data', [
            'project_custom_field_id' => $radioCustomField1->id
        ]);
        $this->seeInDatabase('member_custom_field_data', [
            'project_custom_field_id' => $radioCustomField2->id
        ]);
    }

    public function testFillBulkCustomFieldsWithUpdating()
    {
        $this->factoryModel();

        $user1 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user2 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $project = $this->makeCreateProjectWithPrivateForUser($user1, 1, true);

        $user2->attachProject($project, 0);

        $radioCustomField1 = factory(App\ProjectCustomField::class)->make([
            'order' => 1,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'abc',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'def',
                    ],
                ]
            ])
        ]);
        $radioCustomField2 = factory(App\ProjectCustomField::class)->make([
            'order' => 2,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'qoo',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'foo',
                    ],
                ]
            ])
        ]);

        $project->customFields()->saveMany([$radioCustomField1, $radioCustomField2]);

        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([0]),
            'project_custom_field_id' => $radioCustomField1->id,
            'member_id' => 1
        ]);
        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([1]),
            'project_custom_field_id' => $radioCustomField2->id,
            'member_id' => 1
        ]);

        $postData = [
            'data' => [
                [
                    'type' => 'project_custom_field_data',
                    'attributes' => [
                        'data' => [
                            'options' => [
                                'value' => 1
                            ]
                        ]
                    ],
                    'relationships' => [
                        'project_custom_field' => [
                            'data' => [
                                'type' => 'custom_fields',
                                'id' => $radioCustomField1->id
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'project_custom_field_data',
                    'attributes' => [
                        'data' => [
                            'options' => [
                                'value' => 0
                            ]
                        ]
                    ],
                    'relationships' => [
                        'project_custom_field' => [
                            'data' => [
                                'type' => 'custom_fields',
                                'id' => $radioCustomField2->id
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->json(
            'post',
            '/api/projects/' . $project->id . '/members/bulk_custom_field_data',
            $postData,
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user2),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(201);

        $this->seeInDatabase('member_custom_field_data', [
            'project_custom_field_id' => $radioCustomField1->id
        ]);
        $this->seeInDatabase('member_custom_field_data', [
            'project_custom_field_id' => $radioCustomField2->id
        ]);
    }

    public function testShowAllCustomFieldsData()
    {
        $this->factoryModel();

        $user1 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user2 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $project = $this->makeCreateProjectWithPrivateForUser($user1, 1, true);

        $user2->attachProject($project, 0);

        $radioCustomField1 = factory(App\ProjectCustomField::class)->make([
            'order' => 1,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'abc',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'def',
                    ],
                ]
            ])
        ]);
        $radioCustomField2 = factory(App\ProjectCustomField::class)->make([
            'order' => 2,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'qoo',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'foo',
                    ],
                ]
            ])
        ]);

        $project->customFields()->saveMany([$radioCustomField1, $radioCustomField2]);

        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([0]),
            'project_custom_field_id' => $radioCustomField1->id,
            'member_id' => 1
        ]);
        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([1]),
            'project_custom_field_id' => $radioCustomField2->id,
            'member_id' => 1
        ]);

        $this->json(
            'get',
            '/api/projects/' . $project->id . '/members/custom_field_data',
            [],
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user2),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(200);
    }

    public function testShowAllCustomFieldsDataWithEmptyData()
    {
        $this->factoryModel();

        $user1 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user2 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $project = $this->makeCreateProjectWithPrivateForUser($user1, 1, true);

        $user2->attachProject($project, 0);

        $radioCustomField1 = factory(App\ProjectCustomField::class)->make([
            'order' => 1,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'abc',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'def',
                    ],
                ]
            ])
        ]);
        $radioCustomField2 = factory(App\ProjectCustomField::class)->make([
            'order' => 2,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'qoo',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'foo',
                    ],
                ]
            ])
        ]);

        $project->customFields()->saveMany([$radioCustomField1, $radioCustomField2]);

        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([0]),
            'project_custom_field_id' => $radioCustomField1->id,
            'member_id' => 1
        ]);

        $this->json(
            'get',
            '/api/projects/' . $project->id . '/members/custom_field_data',
            [],
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user2),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(200);
    }

    public function testShowAllMembersCustomFieldData()
    {
        $this->factoryModel();

        $user1 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user2 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user3 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $project = $this->makeCreateProjectWithPrivateForUser($user1, 1, true);

        $user2->attachProject($project, 0);
        $user3->attachProject($project, 0);

        $radioCustomField1 = factory(App\ProjectCustomField::class)->make([
            'order' => 1,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'abc',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'def',
                    ],
                ]
            ])
        ]);
        $radioCustomField2 = factory(App\ProjectCustomField::class)->make([
            'order' => 2,
            'metadata' => new RadioButtonMetadata([
                'options' => [
                    [
                        'value' => 0,
                        'display_name' => 'qoo',
                    ],
                    [
                        'value' => 1,
                        'display_name' => 'foo',
                    ],
                ]
            ])
        ]);

        $project->customFields()->saveMany([$radioCustomField1, $radioCustomField2]);

        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([0]),
            'project_custom_field_id' => $radioCustomField1->id,
            'member_id' => 1
        ]);
        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([1]),
            'project_custom_field_id' => $radioCustomField2->id,
            'member_id' => 1
        ]);

        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([1]),
            'project_custom_field_id' => $radioCustomField1->id,
            'member_id' => 2
        ]);
        factory(App\MemberCustomFieldData::class)->create([
            'data' => new Payload([1]),
            'project_custom_field_id' => $radioCustomField2->id,
            'member_id' => 2
        ]);

        $this->json(
            'get',
            '/api/projects/' . $project->id . '/members/all_custom_field_data',
            [],
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user2),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(200);
    }

    protected function makeCreateProjectWithPrivateForUser($user, $count, $isPublished = true)
    {
        $projects = factory(App\Project::class, 'project_private_for_user', $count)->create([
                'is_published' => $isPublished
            ]);

        if ($count === 1) {
            $user->manageProjects()->save($projects);
        } else {
            $projects->each(function ($project) use ($user) {
                    $user->manageProjects()->save($project);
                });
        }

        return $projects;
    }
}
