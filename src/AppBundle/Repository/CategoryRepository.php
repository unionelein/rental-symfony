<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    /**
     * @return Category[]
     */
    public function findCategoriesContainsProducts()
    {
        return $this->createQueryBuilder('category')
            ->select('category')
            ->innerJoin('category.products', 'product')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  $type
     * @return Category[]
     */
    public function getCategoriesByType($type)
    {
        return $this->createQueryBuilder('category')
            ->select('category')
            ->innerJoin('category.products', 'product')
            ->andWhere('product.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
}