<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topic = Topic::with('user:id,name')->latest()->paginate(10);

        return TopicResource::collection($topic);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTopicRequest $request)
    {
        $topic = Topic::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->user()->id,
        ]);

        return new TopicResource($topic->load('user'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        return new TopicResource($topic->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update($request->validated());

        return new TopicResource($topic->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}
