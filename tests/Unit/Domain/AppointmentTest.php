<?php

namespace Tests\Unit\Domain;

use App\Domain\Entities\Appointment;
use App\Domain\Exceptions\DomainException;
use App\Domain\ValueObjects\AppointmentStatus;
use App\Domain\ValueObjects\TimeSlot;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AppointmentTest extends TestCase
{
    public function test_can_create_appointment_and_it_starts_as_pending()
    {
        $timeSlot = new TimeSlot(
            new DateTimeImmutable('2026-05-01 10:00:00'),
            new DateTimeImmutable('2026-05-01 11:00:00')
        );

        $appointment = new Appointment(
            id: '123',
            customerId: 'c1',
            barberId: 'b1',
            serviceId: 's1',
            timeSlot: $timeSlot
        );

        $this->assertEquals(AppointmentStatus::PENDING, $appointment->getStatus());
    }

    public function test_can_confirm_pending_appointment()
    {
        $timeSlot = new TimeSlot(
            new DateTimeImmutable('2026-05-01 10:00:00'),
            new DateTimeImmutable('2026-05-01 11:00:00')
        );

        $appointment = new Appointment(
            id: '123',
            customerId: 'c1',
            barberId: 'b1',
            serviceId: 's1',
            timeSlot: $timeSlot
        );

        $appointment->confirm();

        $this->assertEquals(AppointmentStatus::CONFIRMED, $appointment->getStatus());
    }

    public function test_cannot_cancel_appointment_with_less_than_two_hours_notice()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Appointments must be canceled at least 2 hours in advance.");

        $timeSlot = new TimeSlot(
            new DateTimeImmutable('2026-05-01 10:00:00'),
            new DateTimeImmutable('2026-05-01 11:00:00')
        );

        $appointment = new Appointment(
            id: '123',
            customerId: 'c1',
            barberId: 'b1',
            serviceId: 's1',
            timeSlot: $timeSlot
        );

        // Try to cancel 1 hour before
        $appointment->cancel(new DateTimeImmutable('2026-05-01 09:00:00'));
    }

    public function test_can_cancel_appointment_with_more_than_two_hours_notice()
    {
        $timeSlot = new TimeSlot(
            new DateTimeImmutable('2026-05-01 10:00:00'),
            new DateTimeImmutable('2026-05-01 11:00:00')
        );

        $appointment = new Appointment(
            id: '123',
            customerId: 'c1',
            barberId: 'b1',
            serviceId: 's1',
            timeSlot: $timeSlot
        );

        // Try to cancel 3 hours before
        $appointment->cancel(new DateTimeImmutable('2026-05-01 07:00:00'));

        $this->assertEquals(AppointmentStatus::CANCELED, $appointment->getStatus());
    }
}
