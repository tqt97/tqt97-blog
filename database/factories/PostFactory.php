<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
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
    public function definition(): array
    {
        $title = $this->faker->text(40);
        $color = $this->faker->hexColor();
        $text = $this->faker->paragraph(1, true);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'image' => 'fake'.substr($color, 1).'/ffffff?text='.$text,
            'excerpt' => $this->faker->paragraph(1),
            'content' => $this->faker->paragraphs(15, true),
            'featured' => $this->faker->boolean(),
            'published_at' => $this->faker->dateTime(),
        ];
    }
}
