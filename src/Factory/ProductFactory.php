<?php

namespace App\Factory;

use App\Entity\Product;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ProductFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Product::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name'          => self::faker()->words(3, true),
            'slug'          => self::faker()->slug(),
            'sku'           => strtoupper(self::faker()->bothify('???-###')),
            'description'   => self::faker()->paragraph(),
            'price'         => self::faker()->numberBetween(3000, 25000),
            'stockQuantity' => self::faker()->numberBetween(1, 20),
            'inStock'       => true,
            'featured'      => false,
            'createdAt'     => new \DateTimeImmutable(),
            'updatedAt'     => new \DateTimeImmutable(),
        ];
    }
}