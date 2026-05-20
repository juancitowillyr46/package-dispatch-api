<?php

declare(strict_types=1);

namespace App\Courier\Infrastructure\Persistence;

use App\Courier\Domain\Entity\Courier;
use App\Courier\Domain\Repository\CourierRepositoryInterface;
use App\Shared\Application\DTO\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Courier>
 */
final class DoctrineCourierRepository extends ServiceEntityRepository implements CourierRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Courier::class);
    }

    public function save(Courier $courier): void
    {
        $this->_em->persist($courier);
        $this->_em->flush();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Courier
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    public function lock(string $id): ?Courier
    {
        return $this->_em->find(Courier::class, $id, LockMode::PESSIMISTIC_WRITE);
    }

    public function paginate(int $page, int $perPage): PaginatedResult
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb->getQuery());
        $items = iterator_to_array($paginator->getIterator());

        return new PaginatedResult($items, $page, $perPage, count($paginator));
    }
}
