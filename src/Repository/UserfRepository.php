<?php

namespace App\Repository;

use App\Entity\Userf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Userf>
 *
 * @method Userf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Userf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Userf[]    findAll()
 * @method Userf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Userf::class);
    }

    //    /**
    //     * @return Userf[] Returns an array of Userf objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Userf
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
