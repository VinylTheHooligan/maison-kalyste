<?php

namespace App\Story;

use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use Zenstruck\Foundry\Story;

final class ProductStory extends Story
{
    public function build(): void
    {
        // Catégories
        $robes = CategoryFactory::createOne([
            'name'        => 'Robes',
            'slug'        => 'robes',
            'description' => 'Robes légères et élégantes aux influences méditerranéennes',
        ]);

        $hauts = CategoryFactory::createOne([
            'name'        => 'Hauts',
            'slug'        => 'hauts',
            'description' => 'Blouses, tops et chemises artisanaux',
        ]);

        $pantalons = CategoryFactory::createOne([
            'name'        => 'Pantalons & Jupes',
            'slug'        => 'pantalons-jupes',
            'description' => 'Pièces fluides et naturelles pour un style solaire',
        ]);

        $vestes = CategoryFactory::createOne([
            'name'        => 'Vestes & Manteaux',
            'slug'        => 'vestes-manteaux',
            'description' => 'Vestes légères et manteaux au caractère artisanal',
        ]);

        // Robes
        ProductFactory::createOne([
            'name'          => 'Robe Soleil en Lin Naturel',
            'slug'          => 'robe-soleil-en-lin-naturel',
            'sku'           => 'ROB-001',
            'description'   => 'Robe fluide taillée dans un lin naturel non blanchi. Coupe droite, col rond brodé à la main, manches courtes légèrement évasées.',
            'price'         => 8900,
            'stockQuantity' => 10,
            'inStock'       => true,
            'featured'      => true,
            'category'      => $robes,
        ]);

        ProductFactory::createOne([
            'name'          => 'Robe Kaftan Brodée Blanche',
            'slug'          => 'robe-kaftan-brodee-blanche',
            'sku'           => 'ROB-002',
            'description'   => 'Inspiration orientale pour cette robe kaftan en coton léger, ornée de broderies artisanales au fil doré.',
            'price'         => 11500,
            'stockQuantity' => 6,
            'inStock'       => true,
            'featured'      => true,
            'category'      => $robes,
        ]);

        ProductFactory::createOne([
            'name'          => 'Robe Midi Ecru à Smocks',
            'slug'          => 'robe-midi-ecru-a-smocks',
            'sku'           => 'ROB-003',
            'description'   => 'Robe midi en gaze de coton ecru avec empiècement smocké à la main. Légère et respirante.',
            'price'         => 7500,
            'stockQuantity' => 8,
            'inStock'       => true,
            'featured'      => false,
            'category'      => $robes,
        ]);

        ProductFactory::createOne([
            'name'          => 'Robe Portefeuille Terracotta',
            'slug'          => 'robe-portefeuille-terracotta',
            'sku'           => 'ROB-004',
            'description'   => 'Robe portefeuille en viscose douce coloris terracotta. Teinture naturelle, coupe flatteuse, finitions cousues main.',
            'price'         => 9200,
            'stockQuantity' => 5,
            'inStock'       => true,
            'featured'      => false,
            'category'      => $robes,
        ]);

        // Hauts
        ProductFactory::createOne([
            'name'          => 'Blouse Paysanne en Coton Blanc',
            'slug'          => 'blouse-paysanne-en-coton-blanc',
            'sku'           => 'HAU-001',
            'description'   => 'Blouse à encolure froncée et manches larges brodées. Fabriquée en coton biologique, inspiration méditerranéenne.',
            'price'         => 5400,
            'stockQuantity' => 14,
            'inStock'       => true,
            'featured'      => true,
            'category'      => $hauts,
        ]);

        ProductFactory::createOne([
            'name'          => 'Top Crochet Fait Main Sable',
            'slug'          => 'top-crochet-fait-main-sable',
            'sku'           => 'HAU-002',
            'description'   => 'Top en crochet réalisé entièrement à la main dans un fil de coton coloris sable. Pièce unique et légère.',
            'price'         => 6800,
            'stockQuantity' => 7,
            'inStock'       => true,
            'featured'      => false,
            'category'      => $hauts,
        ]);

        ProductFactory::createOne([
            'name'          => 'Chemise Oversize Lin Bleu Lavande',
            'slug'          => 'chemise-oversize-lin-bleu-lavande',
            'sku'           => 'HAU-003',
            'description'   => 'Chemise oversize en lin lavé coloris bleu lavande. Col classique, boutons en nacre, coupe décontractée.',
            'price'         => 6200,
            'stockQuantity' => 9,
            'inStock'       => true,
            'featured'      => false,
            'category'      => $hauts,
        ]);

        // Pantalons & Jupes
        ProductFactory::createOne([
            'name'          => 'Pantalon Large Lin Écru',
            'slug'          => 'pantalon-large-lin-ecru',
            'sku'           => 'PAN-001',
            'description'   => 'Pantalon large à taille élastiquée en lin écru. Coupe ample et confortable, jambes droites tombantes.',
            'price'         => 7800,
            'stockQuantity' => 11,
            'inStock'       => true,
            'featured'      => true,
            'category'      => $pantalons,
        ]);

        ProductFactory::createOne([
            'name'          => 'Jupe Longue Imprimé Olives',
            'slug'          => 'jupe-longue-imprime-olives',
            'sku'           => 'PAN-002',
            'description'   => 'Jupe longue fluide en viscose à imprimé olives et feuillages méditerranéens. Taille froncée avec ceinture à nouer.',
            'price'         => 6500,
            'stockQuantity' => 8,
            'inStock'       => true,
            'featured'      => false,
            'category'      => $pantalons,
        ]);

        ProductFactory::createOne([
            'name'          => 'Pantalon Sarouel Coton Ocre',
            'slug'          => 'pantalon-sarouel-coton-ocre',
            'sku'           => 'PAN-003',
            'description'   => 'Sarouel artisanal en coton tissé main coloris ocre. Coupe généreuse, ceinture cordon, chevilles resserrées.',
            'price'         => 7200,
            'stockQuantity' => 6,
            'inStock'       => true,
            'featured'      => false,
            'category'      => $pantalons,
        ]);

        // Vestes & Manteaux
        ProductFactory::createOne([
            'name'          => 'Veste Brodée Style Berbère',
            'slug'          => 'veste-brodee-style-berbere',
            'sku'           => 'VES-001',
            'description'   => 'Veste courte en laine fine ornée de broderies berbères multicolores réalisées à la main. Pièce d\'exception.',
            'price'         => 14500,
            'stockQuantity' => 4,
            'inStock'       => true,
            'featured'      => true,
            'category'      => $vestes,
        ]);

        ProductFactory::createOne([
            'name'          => 'Kimono Coton Imprimé Azulejos',
            'slug'          => 'kimono-coton-imprime-azulejos',
            'sku'           => 'VES-002',
            'description'   => 'Kimono léger en coton imprimé à motifs azulejos bleu et blanc. Taille unique, ceinture à nouer.',
            'price'         => 8900,
            'stockQuantity' => 7,
            'inStock'       => true,
            'featured'      => false,
            'category'      => $vestes,
        ]);

        ProductFactory::createOne([
            'name'          => 'Manteau Laine Naturelle Camel',
            'slug'          => 'manteau-laine-naturelle-camel',
            'sku'           => 'VES-003',
            'description'   => 'Manteau en laine naturelle non teintée coloris camel. Coupe droite oversize, boutonnage en corne naturelle, made in Portugal.',
            'price'         => 22000,
            'stockQuantity' => 3,
            'inStock'       => true,
            'featured'      => true,
            'category'      => $vestes,
        ]);
    }
}