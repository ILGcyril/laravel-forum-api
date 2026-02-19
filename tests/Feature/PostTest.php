<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_posts_for_topic()
    {
        // arrange
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
    
        Post::factory()
        ->count(3)
        ->for($topic)
        ->create();
    
        // act
        $response = $this->actingAs($user)
            ->getJson("/api/topics/{$topic->id}/posts");
    
        // assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'content',
                    ],
                ],
            ]);
    }

    public function test_user_can_create_post_for_topic()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        
        $response = $this->actingAs($user)
            ->postJson("/api/topics/{$topic->id}/posts", [
                'title' => 'Test title',
                'content' => 'Test content'
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Test title')
            ->assertJsonPath('data.content', 'Test content');

        $this->assertDatabaseHas('posts', [
            'title'    => 'Test title',
            'content'  => 'Test content',
            'topic_id' => $topic->id,
            'user_id'  => $user->id
        ]);
    }

    public function test_guest_cannot_create_post_for_topic()
    {
        $topic = Topic::factory()->create();

        $response = $this->postJson("/api/topics/{$topic->id}/posts", [
            'title' => 'Test title',
            'content' => 'Test content',
        ]);
    
        $response->assertUnauthorized();
    }

    public function test_post_requires_title()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/topics/{$topic->id}/posts", [
                "content" => "only content"
            ]);
        
        $response->assertStatus(422)->assertJsonValidationErrors(['title']);

    }

    public function test_user_can_view_post()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();

        $response = $this->actingAs($user)->getJson("/api/topics/{$topic->id}/posts/{$post->id}");

        $response->assertOk()->assertJsonPath("data.id", $post->id)->assertJsonPath("data.content", $post->content);
    }

    public function test_user_cannot_view_post_from_another_topic()
    {
        $user = User::factory()->create();
        $topicA = Topic::factory()->create();
        $topicB = Topic::factory()->create();
        $post = Post::factory()->for($topicA)->create();

        $response = $this->actingAs($user)->getJson("/api/topics/{$topicB->id}/posts/{$post->id}");

        $response->assertNotFound();

    }

    public function test_user_can_update_own_post()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/topics/{$topic->id}/posts/{$post->id}", [
                'title' => 'Updated title',
                'content' => 'Updated content'
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('posts', [
            'title' => 'Updated title',
            'content' => 'Updated content'    
        ]);
    }

    public function test_user_cannot_update_another_users_post()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/topics/{$topic->id}/posts/{$post->id}", [
                'title' => 'Hack',
                'content' => 'Hack'
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_post()
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/topics/{$topic->id}/posts/{$post->id}", [
                'id' => $post->id
            ]);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_can_delete_another_users_post()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/topics/{$topic->id}/posts/{$post->id}", [
                'id' => $post->id
            ]);

        $response->assertForbidden();
    }

    public function test_guest_cannot_delete_post()
    {
        $topic = Topic::factory()->create();
        $post = Post::factory()->for($topic)->create();

        $response = $this->actingAsGuest()
            ->deleteJson("/api/topics/{$topic->id}/posts/{$post->id}", [
                'id' => $post->id
            ]);

        $response->assertUnauthorized();
    }
}