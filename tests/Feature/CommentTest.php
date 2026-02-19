<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_comment()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();

        $response = $this->actingAs($user)
            ->postJson("/api/topics/{$topic->id}/posts/{$post->id}/comments", [
                'content' => 'test content'
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('comments', [
            'content' => 'test content',
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }

    public function test_guest_cannot_create_comments()
    {
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();

        $response = $this->actingAsGuest()
            ->postJson("/api/topics/{$topic->id}/posts/{$post->id}/comments", [
                'content' => 'test content'
            ]);

        $response->assertUnauthorized();
    }

    public function test_user_cannot_reply_to_comment_from_another_post()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        $postA = Post::factory()->for($topic)->create();
        $postB = Post::factory()->for($topic)->create();
        $foreignComment = Comment::factory()->for($postB)->create();

        $response = $this->actingAs($user)
            ->postJson("/api/topics/{$topic->id}/posts/{$postA->id}/comments", [
                'content' => 'test content',
                'parent_id' => $foreignComment->id
            ]);

        $response->assertStatus(403);
    }

    public function test_content_required()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();

        $response = $this->actingAs($user)
            ->postJson("/api/topics/{$topic->id}/posts/{$post->id}/comments", [
                'content' => ''
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('content');
    }

    public function test_user_can_update_own_comment()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->actingAs($user)
            ->putJson("/api/topics/{$topic->id}/posts/{$post->id}/comments/{$comment->id}", [
                'content' => 'new content'
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('comments', [
            'content' => 'new content'
        ]);
    }

    public function test_user_cannot_update_another_users_comment()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->actingAs($anotherUser)
            ->putJson("/api/topics/{$topic->id}/posts/{$post->id}/comments/{$comment->id}", [
                'content' => 'new content'
            ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_post()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/topics/{$topic->id}/posts/{$post->id}/comments/{$comment->id}");

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_can_delete_another_users_post()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();
        $comment = Comment::factory()->for($user)->for($post)->create();

        $response = $this->actingAs($anotherUser)
            ->deleteJson("/api/topics/{$topic->id}/posts/{$post->id}/comments/{$comment->id}");

        $response->assertForbidden();
    }
}
