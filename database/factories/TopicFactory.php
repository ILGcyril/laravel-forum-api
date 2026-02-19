<?php

namespace Database\Factories;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Topic::class; 

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'user_id' => User::factory()
        ];
    }
}
