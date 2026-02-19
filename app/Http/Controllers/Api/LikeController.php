<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function togglePost(Post $post, Request $request)
    {
        return $this->toggle($post, $request);
    }

    public function toggleComment(Comment $comment, Request $request)
    {
        return $this->toggle($comment, $request);
    }

    private function toggle($model, Request $request)
    {
        $user = $request->user();

        $like = Like::where([
            'user_id'      => $user->id,
            'likeable_id'   => $model->id,
            'likeable_type' => get_class($model),
        ])->first();

        if ($like) {
            $like->delete();

            $status = 'unliked';
        } else {
            Like::create([
                'user_id'      => $user->id,
                'likeable_id'   => $model->id,
                'likeable_type' => get_class($model),
            ]);

            $status = 'liked';
        }

        return response()->json([
            'status' => $status,
            'count'  => $model->likes()->count(),
        ]);
    }
}
