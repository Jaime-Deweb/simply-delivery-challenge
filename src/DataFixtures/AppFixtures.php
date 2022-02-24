<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Item;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $item = new Item();
        $item->setName('Pizza');
        $item->setPrice(1000);
        $manager->persist($item);
        
        $item = new Item();
        $item->setName('Hamburguer');
        $item->setPrice(900);
        $manager->persist($item);

        $manager->flush();
    }
}
