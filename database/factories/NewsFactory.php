<?php

namespace Database\Factories;

use App\Models\News; // Pastikan ini mengarah ke model News Anda
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory.
     * @var string
     */
    protected $model = News::class;

    /**
     * Tentukan status default model.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->text(200), // minimal 200 karakter
            'publisher' => $this->faker->name(),
            'image' => null, // Biarkan null, atau atur path default jika perlu
            // Kolom timestamps akan diisi otomatis
        ];
    }
}