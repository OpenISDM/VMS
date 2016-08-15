<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Item;
use App\Project;
use App\Transformers\Project\ProjectTransformer;
use App\Services\TransformerService;

class ProjectTransformerTest extends TestCase
{
    use DatabaseMigrations;

    protected $project;

    protected function setUpDatabase()
    {
        $user = factory(App\Volunteer::class)->create([
            'username' => 'aBcD',
            'first_name' => 'LollLo',
            'last_name' => 'CocO',
            'avatar_path' => '01a34d.jpg',
            'email' => 'abc@abc.cc'
        ]);

        $this->project = factory(App\Project::class)->create([
            'name' => 'OrzZz',
            'description' => 'AQoQO0Ol',
            'organization' => 'fOoFoo',
            'is_published' => true,
            'permission' => 1,
        ]);

        $hyperlink1 = factory(App\Hyperlink::class)->make([
            'name' => 'WaHahA',
            'link' => 'http://qoo.oo/AbC'
        ]);

        $hyperlink2 = factory(App\Hyperlink::class)->make([
            'name' => 'OoOoOkO',
            'link' => 'http://cc.Qoqo/1k/l'
        ]);

        $this->project->hyperlinks()->saveMany([
            $hyperlink1,
            $hyperlink2
        ]);

        $this->project->managers()->save($user);
    }

    public function testOutput()
    {
        $this->setUpDatabase();

        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceItem($this->project,
            'App\Transformers\Project\ProjectTransformer', 'data');

        $actual = $manager->createData($resource)->toArray();

        $expectedProject = [
            'id'=> 1,
            'name' => 'OrzZz',
            'description' => 'AQoQO0Ol',
            'organization' => 'fOoFoo',
            'is_published' => true,
            'permission' => 1
        ];

        $expectedManagers = [
            'data' => [
                [
                    'id' => 1,
                    'username' => 'aBcD',
                    'first_name' => 'LollLo',
                    'last_name' => 'CocO',
                    'avatar_url' => 'http://vms-openisdm.s3-website-ap-northeast-1.amazonaws.com/upload/avatars/01a34d.jpg',
                    'email' => 'abc@abc.cc'
                ]
            ]
        ];

        $expectedHyperlinks = [
            [
                'id' => 1,
                'name' => 'WaHahA',
                'link' => 'http://qoo.oo/AbC'
            ],
            [
                'id' => 2,
                'name' => 'OoOoOkO',
                'link' => 'http://cc.Qoqo/1k/l'
            ]
        ];

        $this->assertContains($expectedProject, $actual);

        $this->assertCount(1, $actual['managers']['data']);
        $this->assertEquals($expectedManagers, $actual['managers']);

        $this->assertCount(2, $actual['hyperlinks']['data']);
        $this->assertEquals($expectedHyperlinks, $actual['hyperlinks']['data']);
    }
}
