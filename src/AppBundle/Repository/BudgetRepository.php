<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Budget;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;

/**
 * BudgetRepository
 */
class BudgetRepository extends EntityRepository
{

    /**
     * @param $user User
     * @return Budget || null
     */
    public function findByActiveBudgetAndByUser($user)
    {
        return $this->createQueryBuilder('u')
                ->andWhere('u.user = :user')
                ->andWhere('u.isActive = :isActive')
                ->setParameter('user', $user->getId())
                ->setParameter('isActive', true)
                ->getQuery()
                ->getOneOrNullResult();

    }

    public function findByActiveBudgetsAndByUser($user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :user')
            ->andWhere('u.isActive = :isActive')
            ->setParameter('user', $user->getId())
            ->setParameter('isActive', true)
            ->getQuery()
            ->getResult();

    }

    /**
     * @param $id int
     * @param $user User
     * @return Budget || null
     */
    public function findByUserAndById($id,$user)
    {
            return $this->createQueryBuilder('u')
                ->andWhere('u.user = :user')
                ->andWhere('u.id = :id')
                ->setParameter('id', $id)
                ->setParameter('user', $user)
                ->getQuery()
                ->getOneOrNullResult();
    }

}
