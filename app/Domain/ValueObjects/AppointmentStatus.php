<?php

namespace App\Domain\ValueObjects;

enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';
}
