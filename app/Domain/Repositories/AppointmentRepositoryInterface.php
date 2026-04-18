<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Appointment;
use DateTimeImmutable;

interface AppointmentRepositoryInterface
{
    public function save(Appointment $appointment): void;
    public function findById(string $id): ?Appointment;
    public function hasOverlappingAppointments(string $barberId, DateTimeImmutable $startTime, DateTimeImmutable $endTime): bool;
}
