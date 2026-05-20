<?php

declare(strict_types=1);

namespace App\Dispatch\Infrastructure\Persistence;

use App\Courier\Domain\Entity\Courier;
use App\Dispatch\Domain\Entity\Dispatch;
use App\Dispatch\Domain\Enum\DispatchStatus;
use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Package\Domain\Entity\ShipmentPackage;
use App\Shared\Application\DTO\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dispatch>
 */
final class DoctrineDispatchRepository extends ServiceEntityRepository implements DispatchRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dispatch::class);
    }

    public function save(Dispatch $dispatch): void
    {
        $this->_em->persist($dispatch);
        $this->_em->flush();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Dispatch
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.package', 'p')
            ->addSelect('p')
            ->leftJoin('d.courier', 'c')
            ->addSelect('c')
            ->andWhere('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function lock(string $id): ?Dispatch
    {
        return $this->_em->find(Dispatch::class, $id, LockMode::PESSIMISTIC_WRITE);
    }

    public function paginate(int $page, int $perPage): PaginatedResult
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.package', 'p')
            ->addSelect('p')
            ->leftJoin('d.courier', 'c')
            ->addSelect('c')
            ->orderBy('d.id', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb->getQuery());
        $items = iterator_to_array($paginator->getIterator());

        return new PaginatedResult($items, $page, $perPage, count($paginator));
    }

    public function findActiveByCourierId(string $courierId): ?Dispatch
    {
        return $this->createQueryBuilder('d')
            ->andWhere('IDENTITY(d.courier) = :courierId')
            ->andWhere('d.status IN (:statuses)')
            ->setParameter('courierId', $courierId)
            ->setParameter('statuses', [
                DispatchStatus::Assigned->value,
                DispatchStatus::InTransit->value,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveByPackageId(string $packageId): ?Dispatch
    {
        return $this->createQueryBuilder('d')
            ->andWhere('IDENTITY(d.package) = :packageId')
            ->andWhere('d.status IN (:statuses)')
            ->setParameter('packageId', $packageId)
            ->setParameter('statuses', [
                DispatchStatus::Pending->value,
                DispatchStatus::Assigned->value,
                DispatchStatus::InTransit->value,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
