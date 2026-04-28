<?php

namespace App\Story;

use App\Factory\SlideFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class SlideStory extends Story
{
    public function build(): void
    {
        SlideFactory::createOne([
            'image' => 'image/carousel/collectionA.png',
            'title' => 'Essence d’Orient',
            'description' => "Des lignes pures, des matières naturelles, une allure solaire. L’Orient réinventé dans une élégance moderne.",
            'cta' => 'Explore la collection',
        ]);

        SlideFactory::createOne([
            'image' => 'image/carousel/collectionB.png',
            'title' => 'Noches de Buenos Aires',
            'description' => "L’allure vibrante des rues porteñas : élégance latine, énergie nocturne et esprit tango.",
            'cta' => 'Explore la collection',
        ]);

        SlideFactory::createOne([
            'image' => 'image/carousel/collectionC.png',
            'title' => 'Terres d’Afrique',
            'description' => "Palette chaude, motifs hérités et force des traditions. Une esthétique qui célèbre la richesse du continent.",
            'cta' => 'Explore la collection',
        ]);
    }
}
