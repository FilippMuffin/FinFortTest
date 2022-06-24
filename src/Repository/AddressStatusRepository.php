<?php

namespace App\Repository;

use App\Entity\AddressStatus;
use App\Entity\RabbitTasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AddressStatus>
 *
 * @method AddressStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressStatus[]    findAll()
 * @method AddressStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressStatus::class);
    }

    public function add(AddressStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AddressStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return AddressStatus[] Returns an array of AddressStatus objects
     */
    public function allPaginate(int $pageNum, int $perPage): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.created_at', 'desc')
            ->setFirstResult($perPage * ($pageNum-1)) // set the offset
            ->setMaxResults($perPage) // set the limit
            ->getQuery()
            ->getResult()
        ;
    }
}
