<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Topic $topic)
    {
        $posts = $topic->posts()->withCount('likes')->with('user:id,name')->latest()->paginate('10');

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request, Topic $topic)
    {
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->user()->id,
            'topic_id' => $topic->id,
        ]);

        return new PostResource($post->load('user')->loadCount('likes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic, Post $post)
    {
        return new PostResource($post->load('user')->loadCount('likes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Topic $topic, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return new PostResource($post->load('user')->loadCount('likes'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic, Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}
