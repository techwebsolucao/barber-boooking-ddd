<?php

namespace App\Application\DTOs;

class CreateAppointmentOutputDTO
{
    public function __construct(
        public readonly string $appointmentId,
        public readonly string $status,
        public readonly string $message
    ) {
    }
}
