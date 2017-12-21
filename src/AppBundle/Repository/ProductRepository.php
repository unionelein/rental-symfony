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
    public function findProductsByCategoryType($categorySlug, $type)
    {
        $qb = $this->createQueryBuilder('product')
            ->select('product');

        if ($categorySlug) {
            $qb->leftJoin('product.category', 'category')
                ->andWhere('category.slug = :categorySlug')
                ->setParameter('categorySlug', $categorySlug)
                ->andWhere('category.type = :type')
                ->setParameter('type', $type)
            ;
        }

        return $qb->getQuery()->getResult();
    }
}