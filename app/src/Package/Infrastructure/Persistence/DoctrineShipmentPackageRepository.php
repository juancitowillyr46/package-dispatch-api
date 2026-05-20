<?php

declare(strict_types=1);

namespace App\Package\Infrastructure\Persistence;

use App\Package\Domain\Entity\ShipmentPackage;
use App\Package\Domain\Repository\ShipmentPackageRepositoryInterface;
use App\Shared\Application\DTO\PaginatedResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShipmentPackage>
 */
final class DoctrineShipmentPackageRepository extends ServiceEntityRepository implements ShipmentPackageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentPackage::class);
    }

    public function save(ShipmentPackage $package): void
    {
        $this->_em->persist($package);
        $this->_em->flush();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?ShipmentPackage
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    public function paginate(int $page, int $perPage): PaginatedResult
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb->getQuery());
        $items = iterator_to_array($paginator->getIterator());

        return new PaginatedResult($items, $page, $perPage, count($paginator));
    }
}
