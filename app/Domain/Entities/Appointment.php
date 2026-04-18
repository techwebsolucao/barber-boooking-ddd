<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\AppointmentStatus;
use App\Domain\ValueObjects\TimeSlot;
use App\Domain\Exceptions\DomainException;
use DateTimeImmutable;

class Appointment
{
    public function __construct(
        private string $id,
        private string $customerId,
        private string $barberId,
        private string $serviceId,
        private TimeSlot $timeSlot,
        private AppointmentStatus $status = AppointmentStatus::PENDING
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getBarberId(): string
    {
        return $this->barberId;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function getTimeSlot(): TimeSlot
    {
        return $this->timeSlot;
    }

    public function getStatus(): AppointmentStatus
    {
        return $this->status;
    }

    public function confirm(): void
    {
        if ($this->status !== AppointmentStatus::PENDING) {
            throw new DomainException("Only pending appointments can be confirmed.");
        }

        $this->status = AppointmentStatus::CONFIRMED;
    }

    public function cancel(DateTimeImmutable $now): void
    {
        if ($this->status === AppointmentStatus::CANCELED) {
            throw new DomainException("Appointment is already canceled.");
        }

        $hoursDifference = ($this->timeSlot->getStartTime()->getTimestamp() - $now->getTimestamp()) / 3600;
        
        if ($hoursDifference < 2) {
            throw new DomainException("Appointments must be canceled at least 2 hours in advance.");
        }

        $this->status = AppointmentStatus::CANCELED;
    }
}
