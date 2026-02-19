<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Topic $topic, Post $post)
    {
        $comments = Comment::withCount('likes')->with(['user:id,name', 'children.user'])
        ->where('post_id', $post->id)
        ->whereNull('parent_id')
        ->latest()
        ->get();

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Topic $topic, Post $post, Comment $comment)
    {
        if ($request->parent_id) {
            $parent = Comment::findOrFail($request->parent_id);

            if ($parent->post_id !== $post->id) {
                abort(403);
            }
        }

        $comment = Comment::create([
            'content'   => $request->content,
            'user_id'   => auth()->user()->id,
            'post_id'   => $post->id,
            'parent_id' => $request->parent_id,
        ]);

        return new CommentResource(
            $comment->load('user')->loadCount('likes')
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic, Post $post, Comment $comment)
    {
        return new CommentResource($comment->load(['user', 'children.user'])->loadCount('likes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Topic $topic, Post $post, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return new CommentResource($comment->load('user')->loadCount('likes'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic, Post $post, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}
