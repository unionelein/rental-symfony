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
}