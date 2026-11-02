<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function next(): ?self
    {
        return match ($this) {
            self::Pending => self::Accepted,
            self::Accepted => self::Preparing,
            self::Preparing => self::Ready,
            self::Ready => self::Completed,
            self::Completed, self::Cancelled => null,
        };
    }

    public function canTransitionTo(self $next): bool
    {
        if ($next === self::Cancelled) {
            return in_array($this, [self::Pending, self::Accepted], true);
        }

        return $this->next() === $next;
    }
}
