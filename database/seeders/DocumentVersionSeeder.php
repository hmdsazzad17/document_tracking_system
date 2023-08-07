<?php

namespace Database\Seeders;

use App\Models\DocumentVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentVersion::factory()->count(2500)->create();
    }
}
