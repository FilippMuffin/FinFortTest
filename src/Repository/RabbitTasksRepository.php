<?php

namespace App\Repository;

use App\Entity\RabbitTasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RabbitTasks>
 *
 * @method RabbitTasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method RabbitTasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method RabbitTasks[]    findAll()
 * @method RabbitTasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RabbitTasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RabbitTasks::class);
    }

    public function add(RabbitTasks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RabbitTasks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return RabbitTasks[] Returns an array of RabbitTasks objects
     */
    public function allPaginate(int $pageNum, int $perPage): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.updated_at', 'desc')
            ->setFirstResult($perPage * ($pageNum-1)) // set the offset
            ->setMaxResults($perPage) // set the limit
            ->getQuery()
            ->getResult()
        ;
    }
}
