<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Exceptions\AccessDeniedException;
use App\Http\Controllers\Api\BaseAuthController;
use App\Http\Requests\Api\V1_0\CreateHyperlinkRequest;
use App\Http\Requests\Api\V1_0\CreateOrUpdateHyperlinksRequest;
use App\Project;
use App\Services\TransformerService;
use App\Transformers\Project\ProjectHyperlinkTransformer;
use Gate;

class HyperlinkController extends BaseAuthController
{
    public function store(CreateHyperlinkRequest $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        $hyperlinks = $project->hyperlinks()->createMany($request->all());

        $manager = TransformerService::getDataArrayManager();
        $resource = TransformerService::getResourceCollection($hyperlinks,
            'App\Transformers\Project\ProjectHyperlinkTransformer', 'data');

        return response()->json($manager->createData($resource)->toArray(), 201);
    }

    public function showByProjectId($projectId)
    {
        $project = Project::findOrFail($projectId);

        if (Gate::denies('show', $project)) {
            throw new AccessDeniedException();
        }

        $hyperlinks = $project->hyperlinks()->get();

        return $this->response
                    ->collection($hyperlinks, new ProjectHyperlinkTransformer());
    }

    public function update($id)
    {
    }

    public function createOrUpdateWithBulk(
        CreateOrUpdateHyperlinksRequest $request,
        $projectId)
    {
        $project = Project::findOrFail($projectId);

        $createdHyperlinks = [];
        $updatedHyperlinks = [];

        // Create
        if ($request->has('create.*')) {
            $newHyperlinksInput = $request->input('create.*');
            $createdHyperlinks = $project->hyperlinks()
                                        ->createMany($newHyperlinksInput);
        }

        // Update
        if ($request->has('update.*')) {
            $updateHyperlinksInput = $request->input('update.*');

            foreach ($updateHyperlinksInput as $item) {
                $hyperlink = $project->hyperlinks()
                                    ->where('hyperlinks.id', $item['id'])
                                    ->first();

                if ($hyperlink != null) {
                    $fields = array_only($item, ['name', 'link']);

                    $hyperlink->fill($fields);
                    $hyperlink->save();

                    array_push($updatedHyperlinks, $hyperlink);
                }
            }
        }

        $merged = collect($createdHyperlinks)->merge($updatedHyperlinks);

        return $this->response
                    ->collection($merged, new ProjectHyperlinkTransformer());
    }

    public function delete($projectId, $hyperlinkId)
    {
        $project = Project::findOrFail($projectId);

        if (Gate::denies('update', $project)) {
            throw new AccessDeniedException();
        }

        $deletedRows = $project->hyperlinks()
                                ->where('hyperlinks.id', $hyperlinkId)
                                ->delete();

        if ($deletedRows === 0) {
            return $this->response->errorNotFound();
        }

        return $this->response->noContent();
    }
}
