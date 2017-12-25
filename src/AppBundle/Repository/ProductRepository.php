<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @param $categorySlug
     * @return Product[]
     */
    public function findProductsByCategoryAndType($categorySlug, $type)
    {
        $qb = $this->createQueryBuilder('product')
            ->select('product')
            ->andWhere('product.type = :type')
            ->setParameter('type', $type);

        if ($categorySlug) {
            $qb->leftJoin('product.category', 'category')
                ->andWhere('category.slug = :categorySlug')
                ->setParameter('categorySlug', $categorySlug);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $categorySlug
     * @param $productSlug
     * @return Product
     */
    public function findProductByCategorySlugAndProductSlug($categorySlug, $productSlug)
    {
        return $this->createQueryBuilder('product')
            ->select('product')
            ->leftJoin('product.category', 'category')
            ->andWhere('product.slug = :productSlug')
            ->setParameter('productSlug', $productSlug)
            ->andWhere('category.slug = :categorySlug')
            ->setParameter('categorySlug', $categorySlug)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}