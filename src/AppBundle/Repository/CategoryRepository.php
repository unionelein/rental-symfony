<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    /**
     * @param  $type
     * @return Category[]
     */
    public function getCategoriesByType($type)
    {
        $qb = $this->createQueryBuilder('category')
            ->select('category.slug, category.name, product.type as type')
            ->innerJoin('category.products', 'product')
            ->orderBy('category.name','ASC');

        if ($type !== null) {
            $qb = $qb->andWhere('product.type = :type')
                ->setParameter('type', $type);
        }

        return  $qb->groupBy('category.slug, category.name, type')
            ->getQuery()
            ->getResult();
    }
}