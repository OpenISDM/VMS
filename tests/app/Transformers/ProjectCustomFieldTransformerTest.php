<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Item;
use App\Project;
use App\Transformers\ProjectTransformer;
use App\Services\TransformerService;
use App\CustomField\RadioButtonMetadata;

class ProjectCustomFieldTransformerTest extends TestCase
{
    use DatabaseMigrations;

    protected $customField;

    /**
     * @before
     */
    protected function setUpDatabase()
    {
        $this->customField = factory(App\ProjectCustomField::class)->make([
            'name' => 'FoofOo',
            'description' => 'oOoO.O0',
            'required' => true,
            'type' => 'RADIO_BUTTON',
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
    }

    public function testTransform()
    {
        $manager = TransformerService::getManager();
        $resource = TransformerService::getResourceItem($this->customField,
            'App\Transformers\ProjectCustomFieldTransformer', 'data');

        $actual = $manager->createData($resource)->toArray();

        $expectCustomField = [
            'name' => 'FoofOo',
            'description' => 'oOoO.O0',
            'required' => true,
            'type' => 'RADIO_BUTTON',
            'order' => 1
        ];

        $expectedMetadata = [
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
        ];

        $this->assertContains($expectCustomField, $actual);
        $this->assertEquals($expectedMetadata, $actual['metadata']);
    }
}
