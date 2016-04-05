<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Controllers\Api\BaseAuthController;
use App\Http\Requests\Api\V1_0\CreateHyperlinkRequest;
use App\Hyperlink;
use App\Services\TransformerService;
use App\Exceptions\AccessDeniedException;
use App\Project;
use Illuminate\Support\Arr;

class HyperlinkController extends BaseAuthController
{
    public function store(CreateHyperlinkRequest $request)
    {
        $user = $this->jwtService->getUser();
        $relationshipProjectId = $request->input('data.relationships.project.data.id');
        $project = Project::find($relationshipProjectId);

        if ($user->isCreatorOfProject($project)) {
            $data = $request->only([
                'data.attributes.name',
                'data.attributes.link'
            ]);
            $hyperlinksData = Arr::get($data, 'data.attributes');
            $hyperlink = new Hyperlink($hyperlinksData);

            $project->hyperlinks()->save($hyperlink);
        }

        $manager = TransformerService::getJsonApiManager();
        $resource = TransformerService::getResourceItem($hyperlink,
            'App\Transformers\JsonApiHyperlinkTransformer', 'hyperlinks');

        $manager->parseIncludes('hyperlink');

        return response()->json($manager->createData($resource)->toArray(), 201);
    }

    public function update($id)
    {
    }

    public function delete($id)
    {
    }
}
