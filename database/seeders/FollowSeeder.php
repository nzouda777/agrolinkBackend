<?php

namespace Database\Seeders;

use App\Models\UserFollow;
use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 50 relations d'abonnement
        UserFollow::factory(50)->create();
    }
}
