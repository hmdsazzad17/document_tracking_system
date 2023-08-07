<?php

namespace Database\Factories;

use App\Models\DocumentVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {


        $titles = ['Document Title 1', 'Document Title 2', 'Document Title 3']; // Example titles
        $statusOptions = ['active', 'inactive']; // Example status options
        $versionIds = DocumentVersion::pluck('id')->toArray();
        return [
            'title' => $this->faker->randomElement($titles),
            'current_version' => $this->faker->randomElement($versionIds),
            'status' => $this->faker->randomElement($statusOptions),
        ];
    }
}
