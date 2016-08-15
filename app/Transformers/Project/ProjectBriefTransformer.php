<?php

namespace App\Transformers\Project;

use Illuminate\Contracts\Support\Arrayable;
use League\Fractal\TransformerAbstract;
use App\Project;

class ProjectBriefTransformer extends TransformerAbstract
{
    public function __call($method, $arguments)
    {
        if ($method === 'transform') {
            if ($arguments instanceof Project) {
                return $this->_transformProjectType($arguments);
            } elseif (is_array($arguments)) {
                return $this->transformStdClassType($arguments[0]);
            }
        }
    }

    private function transformProjectType(Project $project)
    {
        $item = $project->toArray();
        $item['process_number'] = 0;
        $item['member_number'] = $project->members()->count();

        return $item;
    }

    private function transformStdClassType($value)
    {
        $project = Project::find($value->id);

        return $this->transformProjectType($project);
    }
}
