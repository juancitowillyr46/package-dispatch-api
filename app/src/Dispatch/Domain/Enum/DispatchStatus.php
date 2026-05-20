<?php

declare(strict_types=1);

namespace App\Dispatch\Domain\Enum;

enum DispatchStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Pending => self::Assigned === $next,
            self::Assigned => in_array($next, [self::InTransit, self::Delivered], true),
            self::InTransit => self::Delivered === $next,
            self::Delivered => false,
        };
    }
}
