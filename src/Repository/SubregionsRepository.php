<?php

namespace App\Repository;

use App\Entity\Subregions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subregions>
 *
 * @method Subregions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subregions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subregions[]    findAll()
 * @method Subregions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubregionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subregions::class);
    }

//    /**
//     * @return Subregions[] Returns an array of Subregions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Subregions
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
