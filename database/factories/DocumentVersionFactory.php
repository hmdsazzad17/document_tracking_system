<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentVersion>
 */
class DocumentVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentIds = Document::take(500)->pluck('id')->toArray();
        $versions = 100; // Example last viewed versions
        $bodyContent = [
            'introduction' => '<ul><li>Federal Government\'s superannuation reforms in the 2020.</li></ul>',
            'facts' => '<ul><li>Federal Government\'s superannuation reforms in the 2020.</li></ul>',
            'summary' => '<ul><li>Federal Government\'s superannuation reforms in the 2020.</li></ul>',
        ];

        $tagsContent = '<ul><li>Federal Government\'s superannuation reforms in the 2020.</li></ul>';

        return [
            'document_id' => $this->faker->randomElement($documentIds),
            'version' => $versions++, // Initial version
            'body_content' => json_encode($bodyContent),
            'tags_content' => json_encode($tagsContent),
        ];
    }
}
