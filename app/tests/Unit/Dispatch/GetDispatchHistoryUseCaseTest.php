<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dispatch;

use App\Courier\Domain\Entity\Courier;
use App\Dispatch\Application\UseCase\GetDispatchHistoryUseCase;
use App\Dispatch\Domain\Entity\Dispatch;
use App\Dispatch\Domain\Entity\DispatchHistory;
use App\Dispatch\Domain\Repository\DispatchHistoryRepositoryInterface;
use App\Dispatch\Domain\Repository\DispatchRepositoryInterface;
use App\Package\Domain\Entity\ShipmentPackage;
use App\Shared\Domain\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

final class GetDispatchHistoryUseCaseTest extends TestCase
{
    public function testReturnsHistoryWhenDispatchExists(): void
    {
        $package = new ShipmentPackage('TRK-1', 'John Doe', 'Main street', 1.5);
        $courier = new Courier('Courier One');
        $dispatch = new Dispatch($package, 'REF-1', 'Warehouse', 'Customer');

        $dispatchRepository = $this->createMock(DispatchRepositoryInterface::class);
        $historyRepository = $this->createMock(DispatchHistoryRepositoryInterface::class);

        $dispatchRepository->expects(self::once())->method('find')->with('dispatch-1')->willReturn($dispatch);

        $history = [
            new DispatchHistory($dispatch, 'pending', 'assigned'),
        ];

        $historyRepository->expects(self::once())->method('findByDispatchId')->with('dispatch-1')->willReturn($history);

        $useCase = new GetDispatchHistoryUseCase($dispatchRepository, $historyRepository);

        $result = $useCase->execute('dispatch-1');

        self::assertCount(1, $result);
        self::assertSame('assigned', $result[0]->getNewStatus());
    }

    public function testThrowsWhenDispatchDoesNotExist(): void
    {
        $dispatchRepository = $this->createMock(DispatchRepositoryInterface::class);
        $historyRepository = $this->createMock(DispatchHistoryRepositoryInterface::class);

        $dispatchRepository->method('find')->willReturn(null);
        $historyRepository->expects(self::never())->method('findByDispatchId');

        $useCase = new GetDispatchHistoryUseCase($dispatchRepository, $historyRepository);

        $this->expectException(NotFoundException::class);

        $useCase->execute('missing-dispatch');
    }
}
