<?php

namespace App\Repository;

use App\Entity\MicroPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MicroPost>
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    public function findAllWithComments(): array //this is used in MicroPostController to get all the post and the comments
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->leftJoin('p.comments','c')
            ->orderBy('p.Created', 'DESC')
            ->getQuery()
            ->getResult();

    }


    public function findAllWithCommentsAndSearch(string $search): array //this is used in MicroPostController to get all the post and the comments
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->leftJoin('p.comments','c')
            ->where('p.Text LIKE :search OR p.Title LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('p.Created', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithCommentsByFilter(string $filter): array //this is used in MicroPostController to get all the post and the comments
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->leftJoin('p.comments','c')
            ->orderBy('p.Created', $filter)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return MicroPost[] Returns an array of MicroPost objects
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

    //    public function findOneBySomeField($value): ?MicroPost
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
