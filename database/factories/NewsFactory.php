<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => null, // otomatis dari judul
            'excerpt' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'category' => 'news',
            'jurusan_id' => null,
            'cover_image' => null,
            'author_id' => null,
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
