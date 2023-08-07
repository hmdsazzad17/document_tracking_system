<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DocumentUser;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;

class DocumentUserFactory extends Factory
{
    protected $model = DocumentUser::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentIds = Document::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $lastViewedVersions =  DocumentVersion::pluck('id')->toArray(); // Example last viewed versions
        return [
            'document_id' => $this->faker->randomElement($documentIds),
            'user_id' => $this->faker->randomElement($userIds),
            'last_viewed_version' => $this->faker->randomElement($lastViewedVersions),
        ];
    }

}
