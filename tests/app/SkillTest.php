<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class SkillTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetKeywordName()
    {
        $example = [
            'Rope rescue',
            'Disaster Survellience',
            'Disaster Recovery',
            'Water rescue',
        ];

        foreach ($example as $skill) {
            factory(App\Skill::class)->create(
                [
                    'name' => $skill,
                ]
            );
        }

        $skills = App\Skill::keywordName('Dis');

        $this->assertCount(2, $skills);

        $skills->each(function ($item, $key) {
            $this->assertContains($item->name, [
                'Disaster Survellience',
                'Disaster Recovery',
            ]);
        });
    }
}
