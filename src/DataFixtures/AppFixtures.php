<?php

namespace App\DataFixtures;

use App\Story\ProductStory;
use App\Story\SlideStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        SlideStory::load();
        ProductStory::load();
        $manager->flush();
    }
}
