<?php

namespace App\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

class TimeSlot
{
    public function __construct(
        private DateTimeImmutable $startTime,
        private DateTimeImmutable $endTime
    ) {
        if ($startTime >= $endTime) {
            throw new InvalidArgumentException("Start time must be before end time.");
        }
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeImmutable
    {
        return $this->endTime;
    }

    public function overlapsWith(TimeSlot $other): bool
    {
        return $this->startTime < $other->getEndTime() && $this->endTime > $other->getStartTime();
    }
}
