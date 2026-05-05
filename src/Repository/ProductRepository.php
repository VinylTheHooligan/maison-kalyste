<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllInStock(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.inStock = true')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.images', 'i')->addSelect('i')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.inStock = true')
            ->andWhere('p.category = :cat')
            ->setParameter('cat', $categoryId)
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.images', 'i')->addSelect('i')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.inStock = true')
            ->andWhere('p.name LIKE :q OR p.description LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.images', 'i')->addSelect('i')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFeatured(int $limit = 4): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.featured = true')
            ->andWhere('p.inStock = true')
            ->leftJoin('p.images', 'i')->addSelect('i')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}