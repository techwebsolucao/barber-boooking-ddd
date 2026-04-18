<?php

namespace App\Application\DTOs;

class CreateAppointmentInputDTO
{
    public function __construct(
        public readonly string $customerId,
        public readonly string $barberId,
        public readonly string $serviceId,
        public readonly string $startTime,
        public readonly string $endTime
    ) {
    }
}
