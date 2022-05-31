<?php

namespace App\Repository;

use App\Entity\Estate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\classe\Filter;

/**
 * @extends ServiceEntityRepository<Estate>
 *
 * @method Estate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estate[]    findAll()
 * @method Estate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstateRepository extends ServiceEntityRepository
{

    public const PAGINATOR_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estate::class);
    }

    public function add(Estate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Estate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getEstatePaginator(Estate $estate, $offset): Paginator
    {

        $query = $this->createQueryBuilder('e')
                ->setMaxResults(self::PAGINATOR_PER_PAGE)
                ->orderBy('e.id', 'DESC')
                ->setFirstResult($offset)
                ->getQuery();

        return new Paginator($query);

    }

    public function findByCity(Filter $filter){
        
        return $this->createQueryBuilder('e')
                ->andWhere('e.city LIKE :val')
                ->setParameter('val', "%{$filter->city}%")
                ->orderBy('e.id', 'DESC')
                ->getQuery()
                ->getResult();

    }

//    /**
//     * @return Estate[] Returns an array of Estate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Estate
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
