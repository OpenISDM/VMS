<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Item;
use App\Project;
use App\Transformers\ProjectTransformer;
use App\Services\TransformerService;
use App\CustomField\RadioButtonMetadata;

class ProjectMemberTransformerTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    /**
     * @before
     */
    protected function setUpDatabase()
    {
        $this->user = factory(App\Volunteer::class)->create([
            'username' => 'cOcoCo',
            'first_name' => 'AbCdEfGhIJ',
            'last_name' => 'ooqOoQ',
            'email' => 'cOco@cocc.cc',
            'avatar_path' => 'http://foo.Oof'
        ]);
    }

    public function testTransform()
    {
        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceItem($this->user,
            'App\Transformers\ProjectMemberTransformer', 'data');

        $actual = $manager->createData($resource)->toArray();

        $expected = [
            'id' => 1,
            'username' => 'cOcoCo',
            'first_name' => 'AbCdEfGhIJ',
            'last_name' => 'ooqOoQ',
            'email' => 'cOco@cocc.cc',
            'avatar_path' => 'http://foo.Oof'
        ];

        $this->assertEquals($expected, $actual);
    }
}
