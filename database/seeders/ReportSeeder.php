<?php

namespace Database\Seeders;

use App\Models\UserReport;
use App\Models\ProductReport;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 40 rapports utilisateurs
        UserReport::factory(40)->create();

        // Créer 30 rapports produits
        ProductReport::factory(30)->create();
    }
}
