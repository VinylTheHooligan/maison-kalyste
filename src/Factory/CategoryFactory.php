<?php

namespace App\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class CategoryFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Category::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name'        => self::faker()->words(2, true),
            'slug'        => self::faker()->slug(),
            'description' => self::faker()->sentence(),
            'createdAt'   => new \DateTimeImmutable(),
            'updatedAt'   => new \DateTimeImmutable(),
        ];
    }
}