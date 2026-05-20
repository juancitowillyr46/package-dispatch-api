<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dispatch;

use App\Courier\Domain\Entity\Courier;
use App\Courier\Domain\Repository\CourierRepositoryInterface;
use App\Dispatch\Application\DTO\CourierAssignmentData;
use App\Dispatch\Application\UseCase\AssignCourierToDispatchUseCase;
use App\Dispatch\Domain\Entity\Dispatch;
use App\Dispatch\Domain\Entity\DispatchHistory;
use App\Dispatch\Domain\Repository\DispatchHistoryRepositoryInterface;
use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Package\Domain\Entity\ShipmentPackage;
use App\Shared\Domain\Exception\ConflictException;
use App\Shared\Domain\Service\TransactionManagerInterface;
use PHPUnit\Framework\TestCase;

final class AssignCourierToDispatchUseCaseTest extends TestCase
{
    public function testAssignCourierToPendingDispatch(): void
    {
        $package = new ShipmentPackage('TRK-1', 'John Doe', 'Main street', 1.5);
        $courier = new Courier('Courier One');
        $dispatch = new Dispatch($package, 'REF-1', 'Warehouse', 'Customer');
        $this->setEntityId($courier, '11111111-1111-4111-8111-111111111111');
        $this->setEntityId($dispatch, '22222222-2222-4222-8222-222222222222');

        $transactionManager = new class implements TransactionManagerInterface {
            public function run(callable $callback): mixed
            {
                return $callback();
            }
        };

        $dispatchRepository = $this->createMock(DispatchRepositoryInterface::class);
        $courierRepository = $this->createMock(CourierRepositoryInterface::class);
        $historyRepository = $this->createMock(DispatchHistoryRepositoryInterface::class);

        $dispatchRepository->expects(self::once())->method('lock')->with('22222222-2222-4222-8222-222222222222')->willReturn($dispatch);
        $courierRepository->expects(self::once())->method('lock')->with('11111111-1111-4111-8111-111111111111')->willReturn($courier);
        $dispatchRepository->expects(self::once())->method('findActiveByCourierId')->with(self::anything())->willReturn(null);
        $dispatchRepository->expects(self::once())->method('save')->with($dispatch);
        $historyRepository->expects(self::once())->method('save')->with(self::isInstanceOf(DispatchHistory::class));

        $useCase = new AssignCourierToDispatchUseCase(
            $transactionManager,
            $dispatchRepository,
            $courierRepository,
            $historyRepository
        );

        $result = $useCase->execute(new CourierAssignmentData('22222222-2222-4222-8222-222222222222', '11111111-1111-4111-8111-111111111111'));

        self::assertSame('assigned', $result->getStatus()->value);
        self::assertSame('11111111-1111-4111-8111-111111111111', $result->getCourierId());
    }

    public function testAssignCourierFailsWhenCourierAlreadyHasActiveDispatch(): void
    {
        $package = new ShipmentPackage('TRK-1', 'John Doe', 'Main street', 1.5);
        $courier = new Courier('Courier One');
        $dispatch = new Dispatch($package, 'REF-1', 'Warehouse', 'Customer');
        $this->setEntityId($courier, '11111111-1111-4111-8111-111111111111');
        $this->setEntityId($dispatch, '22222222-2222-4222-8222-222222222222');

        $transactionManager = new class implements TransactionManagerInterface {
            public function run(callable $callback): mixed
            {
                return $callback();
            }
        };

        $dispatchRepository = $this->createMock(DispatchRepositoryInterface::class);
        $courierRepository = $this->createMock(CourierRepositoryInterface::class);
        $historyRepository = $this->createMock(DispatchHistoryRepositoryInterface::class);

        $dispatchRepository->method('lock')->willReturn($dispatch);
        $courierRepository->method('lock')->willReturn($courier);
        $dispatchRepository->method('findActiveByCourierId')->willReturn(new Dispatch($package, 'REF-2', 'A', 'B'));

        $useCase = new AssignCourierToDispatchUseCase(
            $transactionManager,
            $dispatchRepository,
            $courierRepository,
            $historyRepository
        );

        $this->expectException(ConflictException::class);
        $useCase->execute(new CourierAssignmentData('22222222-2222-4222-8222-222222222222', '11111111-1111-4111-8111-111111111111'));
    }

    private function setEntityId(object $entity, string $id): void
    {
        $reflection = new \ReflectionObject($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, $id);
    }
}
