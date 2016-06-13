<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Controllers\Api\BaseAuthController;
use App\Http\Requests\Api\V1_0\CreateHyperlinkRequest;
use App\Hyperlink;
use App\Services\TransformerService;
use App\Exceptions\AccessDeniedException;
use App\Project;
use Illuminate\Support\Arr;
use Gate;
use App\Transformers\ProjectHyperlinkTransformer;

class HyperlinkController extends BaseAuthController
{
    public function store(CreateHyperlinkRequest $request, $projectId)
    {
        $user = $this->jwtService->getUser();

        $project = Project::findOrFail($projectId);

        // var_dump($request->all());

        $hyperlinks = $project->hyperlinks()->createMany($request->all());

        $manager = TransformerService::getDataArrayManager();
        $resource = TransformerService::getResourceCollection($hyperlinks,
            'App\Transformers\ProjectHyperlinkTransformer', 'data');

        return response()->json($manager->createData($resource)->toArray(), 201);
    }

    public function showByProjectId($projectId)
    {
        $project = Project::findOrFail($projectId);

        if (Gate::denies('show', $project)) {
            throw new AccessDeniedException();
        }

        $hyperlinks = $project->hyperlinks()->get();

        return $this->response->collection($hyperlinks, new ProjectHyperlinkTransformer);
    }

    public function update($id)
    {
    }

    public function delete($id)
    {
    }
}
