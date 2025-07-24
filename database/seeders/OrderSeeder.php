<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 30 commandes
        $orders = Order::factory(30)->create();

        // Pour chaque commande, créer 1-5 items
        foreach ($orders as $order) {
            OrderItem::factory(rand(1, 5))->create([
                'order_id' => $order->id,
            ]);
        }
    }
}
