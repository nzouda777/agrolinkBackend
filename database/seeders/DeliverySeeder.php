<?php

namespace Database\Seeders;

use App\Models\DeliveryOption;
use App\Models\DeliveryRate;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        // Créer 3 options de livraison
        $deliveryOptions = DeliveryOption::factory(3)->create();

        // Pour chaque option de livraison, créer des taux pour 5 villes
        foreach ($deliveryOptions as $option) {
            DeliveryRate::factory(5)->create([
                'delivery_option_id' => $option->id,
            ]);
        }
    }
}
