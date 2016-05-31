<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use App\Repositories\ProjectDbQueryRepository;
use App\Hyperlink;
use App\Project;
use App\Utils\ArrayUtil;

class ProjectDbQueryRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $repository;
    protected $user1;
    protected $user2;

    public function setUp()
    {
        parent::setup();
        $this->repository = new ProjectDbQueryRepository();
    }

    public function testUserLevelPermissionProjectsQuery()
    {
        $this->makeUsers();

        $projectWithPublicIds = $this->getModelIds($this->makeCreateProjectWithPublic($this->user1, 3));
        $projectWithPublicNotPublishedIds = $this->getModelIds($this->makeCreateProjectWithPublic($this->user1, 2, false));
        $projectWithPrivateForUserIds = $this->getModelIds($this->makeCreateProjectWithPrivateForUser($this->user1, 4));
        $projectWithPrivateForMemberIds = $this->getModelIds($this->makeCreateProjectWithPrivateForMember($this->user1, 2));

        $query = $this->invokeMethod($this->repository, 'userLevelPermissionProjectsQuery');
        $result = $query->get();

        $expectProjectIds = array_merge($projectWithPublicIds, $projectWithPrivateForUserIds);

        $collection = collect($result);

        $collection->each(function ($project) use ($expectProjectIds) {
            $this->assertContains($project->id, $expectProjectIds);
        });
        $this->assertCount(7, $result);
    }

    public function testManageProjectsQuery()
    {
        $this->makeUsers();

        $user1ExpectedProjectIds = $this->getModelIds($this->makeCreateProjectWithPublic($this->user1, 5));
        $user2ProjectIds = $this->getModelIds($this->makeCreateProjectWithPublic($this->user2, 6));

        $user1Query = $this->invokeMethod($this->repository, 'manageProjectsQuery', [$this->user1]);
        $user1ActualResult = $user1Query->get();

        collect($user1ActualResult)->each(function ($project) use ($user1ExpectedProjectIds) {
            $this->assertContains($project->id, $user1ExpectedProjectIds);
        });
    }

    public function testAsMemberProjectsQuery()
    {
        $this->makeUsers();
        $user2ExpectedAttachedProject = $this->makeCreateProjectWithPublic($this->user1, 1);
        $user1ExpectedAttachedProject = $this->makeCreateProjectWithPrivateForUser($this->user2, 1);

        $this->user2->attachProject($user2ExpectedAttachedProject, config('constants.member_project_permission.PUBLIC'));
        $this->user1->attachProject($user1ExpectedAttachedProject, config('constants.member_project_permission.PUBLIC'));

        $this->makeCreateProjectWithPrivateForMember($this->user2, 2);

        $user2Query = $this->invokeMethod($this->repository, 'asMemberProjectsQuery', [$this->user2]);
        $user2ActualResult = $user2Query->first();

        $user1Query = $this->invokeMethod($this->repository, 'asMemberProjectsQuery', [$this->user1]);
        $user1ActualResult = $user1Query->first();

        $this->assertEquals($user1ExpectedAttachedProject->id, $user1ActualResult->id);
        $this->assertEquals($user2ExpectedAttachedProject->id, $user2ActualResult->id);
    }

    public function testGetViewablePublicProjectByUser()
    {
        $this->makeUsers();

        $user1ManageProjectIds = $this->getModelIds($this->makeCreateProjectWithPublic($this->user1, 3));

        $viewableProjects = $this->invokeMethod($this->repository, 'viewableProjectsQuery', [$this->user2])->get();
        $collection = collect($viewableProjects);

        $this->assertCount(3, $viewableProjects);

        $collection->each(function ($project) use ($user1ManageProjectIds) {
            $this->assertContains($project->id, $user1ManageProjectIds);
        });
    }

    public function testGetViewableProjectsByUser()
    {
        $this->makeUsers();

        $user1ManageProjectIdsWithPublic = $this->getModelIds($this->makeCreateProjectWithPublic($this->user1, 2));
        $user1ManageProjectIdsWithPrivateForMember = $this->getModelIds($this->makeCreateProjectWithPrivateForMember($this->user1, 4));
        $user2AttachedProject = $this->makeCreateProjectWithPrivateForMember($this->user1, 1);
        $user2ManageProjectIdsWithPrivateForMember = $this->getModelIds($this->makeCreateProjectWithPrivateForMember($this->user2, 3));

        $user1Expected = array_merge($user1ManageProjectIdsWithPublic, $user1ManageProjectIdsWithPrivateForMember, [$user2AttachedProject->id]);
        $user2Expected = array_merge($user1ManageProjectIdsWithPublic, $user2ManageProjectIdsWithPrivateForMember, [$user2AttachedProject->id]);

        $user1Result = $this->invokeMethod($this->repository, 'viewableProjectsQuery', [$this->user1])->get();
        $user1ResultCollection = collect($user1Result);
        $user2Result = $this->invokeMethod($this->repository, 'viewableProjectsQuery', [$this->user2])->get();
        $user2ResultCollection = collect($user2Result);

        $user1ResultCollection->each(function ($project) use ($user1Expected) {
            $this->assertContains($project->id, $user1Expected);
        });

        $user2ResultCollection->each(function ($project) use ($user2Expected) {
            $this->assertContains($project->id, $user2Expected);
        });
    }

    public function testGetViewableProjectsWithHyperlinks()
    {
        $this->makeUsers();

        $user1ManageProjects = $this->makeCreateProjectWithPublic($this->user1, 2);
        $user1ManageProjectsWithPrivateForMember = $this->makeCreateProjectWithPrivateForMember($this->user1, 4);
        $user2ManageProjectsWithPrivateForMember = $this->makeCreateProjectWithPrivateForMember($this->user2, 3);

        $this->relationHyperlinks($user1ManageProjects, 3);
        $this->relationHyperlinks($user1ManageProjectsWithPrivateForMember, 2);
        $this->relationHyperlinks($user2ManageProjectsWithPrivateForMember, 4);

        $user1ManageProjectIdsWithPublic = $this->getModelIds($user1ManageProjects);
        $user1ManageProjectIdsWithPrivateForMember = $this->getModelIds($user1ManageProjectsWithPrivateForMember);
        $user2AttachedProject = $this->makeCreateProjectWithPrivateForMember($this->user1, 1);
        $user2ManageProjectIdsWithPrivateForMember = $this->getModelIds($user2ManageProjectsWithPrivateForMember);

        $user1Expected = array_merge($user1ManageProjectIdsWithPublic, $user1ManageProjectIdsWithPrivateForMember, [$user2AttachedProject->id]);
        $user2Expected = array_merge($user1ManageProjectIdsWithPublic, $user2ManageProjectIdsWithPrivateForMember, [$user2AttachedProject->id]);

        $user1Result = $this->repository->getViewableProjectsWithHyperlinks($this->user1);
        $user1ResultCollection = collect($user1Result);
        $user2Result = $this->repository->getViewableProjectsWithHyperlinks($this->user2);
        $user2ResultCollection = collect($user2Result);

        $user1ResultCollection->each(function ($project) use ($user1Expected) {
            $this->assertContains($project->id, $user1Expected);
        });

        $user2ResultCollection->each(function ($project) use ($user2Expected) {
            $this->assertContains($project->id, $user2Expected);
        });

        /**
         * TODO: Should be assert the count
         */
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

            $models->hyperlins()->saveMany($hyperlinks);
            $hyperlinkMapping[$model->id] = $hyperlinks;
        }

        return $hyperlinkMapping;
    }

    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function getModelIds($models)
    {
        $ids = array();

        foreach ($models as $model) {
            $ids[] = $model->id;
        }

        return $ids;
    }

    protected function makeUsers()
    {
        $this->user1 = factory(App\Volunteer::class)->create([
                'is_actived' => true
        ]);
        $this->user2 = factory(App\Volunteer::class)->create([
                'is_actived' => true
        ]);
    }
}
