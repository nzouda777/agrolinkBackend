<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed les données dans l'ordre approprié
        $this->call([
            UserRoleSeeder::class,        // Créer les rôles d'abord
            UserTypeSeeder::class,        // Créer les types d'utilisateurs
            UserSeeder::class,            // Créer les utilisateurs
            CategorySeeder::class,        // Créer les catégories
            ProductSeeder::class,         // Créer les produits
            OrderSeeder::class,           // Créer les commandes
            WishlistSeeder::class,        // Créer les listes de souhaits
            FollowSeeder::class,          // Créer les relations d'abonnement
            FavoriteSeeder::class,        // Créer les produits favoris
            UserRatingSeeder::class,      // Créer les évaluations utilisateurs
            ProductRatingSeeder::class,   // Créer les évaluations produits
            FlagSeeder::class,           // Créer les signalements
            ReportSeeder::class,         // Créer les rapports
            DeliverySeeder::class,        // Créer les options de livraison
        ]);
    }
}
