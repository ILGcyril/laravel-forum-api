<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'topic_id' => Topic::factory(),
        ];
    }

    public function forTopic(Topic $topic): static
    {
        return $this->state(function (array $attributes) use ($topic) {
            return [
                'topic_id' => $topic->id,  // ← Верните массив с ID
            ];
        });
    }
}
