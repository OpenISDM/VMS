<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use App\Project;
use App\Hyperlink;

class ProjectControllerTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function testCreateProjectSuccessfully()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);
        $postData = [
            'data' => [
                'type' => 'projects',
                'attributes' => [
                    'name' => 'Flood surveillance',
                    'description' => 'FoOfOoFoOfOoFoOfOoFoOfOoFoOfOo',
                    'organization' => 'QOQOQOQOQO...OOO',
                    'is_published' => true,
                    'permission' => 0,
                ],
            ],
        ];

        $jsonRequest = $this->json('post',
                    '/api/projects',
                    $postData,
                    [
                        'Authorization' => 'Bearer '.$token,
                        'X-VMS-API-Key' => $this->getApiKey(),
                    ]);

        $project = App\Project::all()->first();

        // $expect = [
        //     'data' => [
        //         'type' => 'projects',
        //         'id' => '1',
        //         'attributes' => [
        //             'name' => $project->name,
        //             'description' => $project->description,
        //             'organization' => $project->organization,
        //             'is_published' => $project->is_published,
        //             'permission' => $project->permission,
        //             'created_at' => $project->created_at,
        //             'updated_at' => $project->updated_at,
        //             'hyperlinks' => [
        //                 [
        //                     'name' => 'aaaBBBccc',
        //                     'link' => 'http://Qoo.ccccccc.ttt'
        //                 ]
        //             ]
        //         ],
        //         'relationships' => [
        //             'managers' => [
        //                 'data' => [
        //                     [
        //                         'type' => 'managers',
        //                         'id' => (string)$volunteer->id
        //                     ]
        //                 ]
        //             ]
        //         ]
        //     ],
        //     'included' => [
        //         [
        //             'type' => 'managers',
        //             'id' => (string)$volunteer->id,
        //             'attributes' => [
        //                 'username' => $volunteer->username,
        //                 'first_name' => $volunteer->first_name,
        //                 'last_name' => $volunteer->last_name,
        //                 'email' => $volunteer->email
        //             ]
        //         ]
        //     ]
        // ];

        // TODO need to check the JSON response
        // $jsonRequest->seeJsonEquals([])->assertResponseStatus(201);
        $jsonRequest->assertResponseStatus(201);
    }

    public function testShowPublicSuccessfully()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $project = factory(App\Project::class)->create();
        $hyperlink = factory(App\Hyperlink::class)->make();

        $project->hyperlinks()->save($hyperlink);
        $project->managers()->save($volunteer);

        $this->json('get',
            '/api/projects/' . $project->id,
            [],
            [
                'X-VMS-API-Key' => $this->getApiKey(),
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($volunteer),
            ]
        )->assertResponseStatus(200);

        /**
         * TODO: test transformer
         */
    }

    public function testUpdateSuccessfully()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $project = factory(App\Project::class)->create();
        $hyperlink = factory(App\Hyperlink::class)->make();

        $project->hyperlinks()->save($hyperlink);
        $project->managers()->save($this->volunteer);

        $putData = [
            'data' => [
                'type' => 'projects',
                'id' => '1',
                'attributes' => [
                    'name' => 'POPOPO',
                    'description' => 'OPOPOOPPOOOP',
                    'organization' => 'ZOZOZOZOZ',
                    'is_published' => false,
                    'permission' => 1,
                ]
            ]
        ];

        $this->json(
            'put',
            '/api/projects/' . $project->id,
            $putData,
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(200);

        /**
         * @TODO Test transformer
         */
    }

    public function testShowAll()
    {
        $this->factoryModel();

        $user1 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user2 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);

        $user1ManageProjects = $this->makeCreateProjectWithPublic($user1, 2);
        $user1ManageProjectsWithPrivateForMember = $this->makeCreateProjectWithPrivateForMember($user1, 4);
        $user2ManageProjectsWithPrivateForMember = $this->makeCreateProjectWithPrivateForMember($user2, 3);
        $user2AttachedProject = $this->makeCreateProjectWithPrivateForMember($user1, 1);

        $this->relationHyperlinks($user1ManageProjects, 3);
        $this->relationHyperlinks($user1ManageProjectsWithPrivateForMember, 2);
        $this->relationHyperlinks($user2ManageProjectsWithPrivateForMember, 4);

        $user2->attachProject($user2AttachedProject, 0);

        $this->json(
            'get',
            '/api/projects',
            [],
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user2),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(200);

        /**
         * TODO Transformer testing
         */
    }

    public function testAttachVolunteer()
    {
        $this->factoryModel();

        $user1 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $user2 = factory(App\Volunteer::class)->create([
                    'is_actived' => true
            ]);
        $project = $this->makeCreateProjectWithPrivateForUser($user1, 1, true);

        $postData = [
            'data' => [
                'type' => 'members',
                'attributes' => [
                    'volunteer_id' => $user2->id
                ]
            ]
        ];

        $this->json(
            'post',
            '/api/projects/' . $project->id . '/members',
            $postData,
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user1),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(204);

        $newProject = Project::find($project->id)->first();

        $this->seeInDatabase('project_volunteers', [
            'project_id' => $project->id,
            'volunteer_id' => $user2->id
        ]);
    }

    public function testDetachVolunteer()
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

        $this->json(
            'delete',
            '/api/projects/' . $project->id . '/members/' . $user2->id,
            [],
            [
                'Authorization' => 'Bearer ' . JWTAuth::fromUser($user1),
                'X-VMS-API-Key' => $this->getApiKey()
            ]
        )->assertResponseStatus(204);

        $this->notSeeInDatabase('project_volunteers', [
            'project_id' => $project->id,
            'volunteer_id' => $user2->id
        ]);
    }

    public function testShowMembers()
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

        $this->json(
            'get',
            '/api/projects/' . $project->id . '/members',
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

    protected function makeCreateProjectWithPrivateForMember($user, $count, $isPublished = true)
    {
        $projects = factory(App\Project::class, 'project_private_for_member', $count)->create([
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

    protected function relationHyperlinks($models, $count)
    {
        $hyperlinkMapping = [];

        if ($models instanceof Collection) {
            $models->each(function ($model) use ($count, &$hyperlinkMapping) {
                $hyperlinks = factory(App\Hyperlink::class, $count)->make();

                // echo '!!! hyperlinks !!! ';
                // var_dump($hyperlinks);

                $model->hyperlinks()->saveMany($hyperlinks);
                $hyperlinkMapping[$model->id] = $hyperlinks;
            });
        } else {
            $hyperlinks = factory(App\Hyperlink::class, $count)->make();

            $models->hyperlinks()->saveMany($hyperlinks);
            $hyperlinkMapping[$model->id] = $hyperlinks;
        }

        return $hyperlinkMapping;
    }

    protected function makeCreateProjectWithPublic($user, $count, $isPublished = true)
    {
        $projects = factory(App\Project::class, $count)->create([
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
