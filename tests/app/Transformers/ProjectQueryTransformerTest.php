<?php

use App\Services\TransformerService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProjectQueryTransformerTest extends TestCase
{
    use DatabaseMigrations;

    protected $project;

    protected function setUpDatabase()
    {
        $user = factory(App\Volunteer::class)->create([
            'username'    => 'aBcD',
            'first_name'  => 'LollLo',
            'last_name'   => 'CocO',
            'avatar_path' => 'http://avatar.vms.app/01a34d.jpg',
        ]);

        $this->project = factory(App\Project::class)->create([
            'name'         => 'OrzZz',
            'description'  => 'AQoQO0Ol',
            'organization' => 'fOoFoo',
            'is_published' => true,
            'permission'   => 1,
        ]);

        $hyperlink1 = factory(App\Hyperlink::class)->make([
            'name' => 'WaHahA',
            'link' => 'http://qoo.oo/AbC',
        ]);

        $hyperlink2 = factory(App\Hyperlink::class)->make([
            'name' => 'OoOoOkO',
            'link' => 'http://cc.Qoqo/1k/l',
        ]);

        $this->project->hyperlinks()->saveMany([
            $hyperlink1,
            $hyperlink2,
        ]);

        $this->project->managers()->save($user);
    }

    public function testTransform()
    {
        $this->setUpDatabase();

        $project = $this->createMock(App\Test\Stubs\Project::class);
        $project->method('__get')
                ->with($this->equalTo('id'))
                ->willReturn(1);

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceItem($project,
            'App\Transformers\Project\ProjectQueryTransformer', 'data');

        $actual = $manager->createData($resource)->toArray();

        $expectedProject = [
            'id'           => 1,
            'name'         => 'OrzZz',
            'description'  => 'AQoQO0Ol',
            'organization' => 'fOoFoo',
            'is_published' => true,
            'permission'   => 1,
        ];

        $expectedManagers = [
            [
                'id'          => 1,
                'username'    => 'aBcD',
                'first_name'  => 'LollLo',
                'last_name'   => 'CocO',
                'avatar_path' => 'http://avatar.vms.app/01a34d.jpg',
            ],
        ];

        $expectedHyperlinks = [
            [
                'id'   => 1,
                'name' => 'WaHahA',
                'link' => 'http://qoo.oo/AbC',
            ],
            [
                'id'   => 2,
                'name' => 'OoOoOkO',
                'link' => 'http://cc.Qoqo/1k/l',
            ],
        ];

        $this->assertContains($expectedProject, $actual);

        $this->assertCount(1, $actual['managers']);
        $this->assertEquals($expectedManagers, $actual['managers']);

        $this->assertCount(2, $actual['hyperlinks']);
        $this->assertEquals($expectedHyperlinks, $actual['hyperlinks']);
    }
}
