<?php

declare(strict_types=1);

namespace App\Dispatch\Infrastructure\Persistence;

use App\Dispatch\Domain\Entity\DispatchHistory;
use App\Dispatch\Domain\Repository\DispatchHistoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DispatchHistory>
 */
final class DoctrineDispatchHistoryRepository extends ServiceEntityRepository implements DispatchHistoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DispatchHistory::class);
    }

    public function save(DispatchHistory $history): void
    {
        $this->_em->persist($history);
        $this->_em->flush();
    }

    public function findByDispatchId(string $dispatchId): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('IDENTITY(h.dispatch) = :dispatchId')
            ->setParameter('dispatchId', $dispatchId)
            ->orderBy('h.changedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
