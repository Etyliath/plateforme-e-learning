<?php

namespace App\Repository;

use App\Entity\MyLesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MyLesson>
 *
 * @method MyLesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method MyLesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method MyLesson[]    findAll()
 * @method MyLesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MyLessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MyLesson::class);
    }
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.user= :user')
            ->setParameter('user',$user)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return MyLesson[] Returns an array of MyLesson objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MyLesson
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
