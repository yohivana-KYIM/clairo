<?php
// src/DataFixtures/ProduitFixtures.php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rows = [
            // keep this one: the listener expects name = 'carte'
            ['name' => 'carte', 'price' => '75.00', 'description' => "Carte/badge d'accès"],
            // optional sample products (safe to keep or remove)
//            ['name' => 'duplicata', 'price' => '15.00', 'description' => 'Duplicata de carte'],
            ['name' => 'porte_carte', 'price' => '5.50', 'description' => 'Porte-carte plastique'],
        ];

        foreach ($rows as $r) {
            $p = (new Produit())
                ->setName($r['name'])
                ->setPrice($r['price'])
                ->setDescription($r['description']);
            $manager->persist($p);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev', 'prod', 'demo'];
    }
}
