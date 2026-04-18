<?php

namespace App\Application\UseCases;

use App\Application\DTOs\CreateAppointmentInputDTO;
use App\Application\DTOs\CreateAppointmentOutputDTO;
use App\Application\Services\PaymentServiceInterface;
use App\Domain\Entities\Appointment;
use App\Domain\Exceptions\DomainException;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\ValueObjects\TimeSlot;
use DateTimeImmutable;
use Illuminate\Support\Str;

class CreateAppointmentUseCase
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private PaymentServiceInterface $paymentService
    ) {
    }

    public function execute(CreateAppointmentInputDTO $input): CreateAppointmentOutputDTO
    {
        $startTime = new DateTimeImmutable($input->startTime);
        $endTime = new DateTimeImmutable($input->endTime);

        $startHour = (int) $startTime->format('G');
        if ($startHour < 9 || $startHour >= 18) {
            throw new DomainException("Appointments can only be scheduled between 09:00 and 18:00.");
        }

        if ($this->appointmentRepository->hasOverlappingAppointments($input->barberId, $startTime, $endTime)) {
            throw new DomainException("The selected time slot is not available for this barber.");
        }

        $timeSlot = new TimeSlot($startTime, $endTime);

        $appointment = new Appointment(
            id: Str::uuid()->toString(), 
            customerId: $input->customerId,
            barberId: $input->barberId,
            serviceId: $input->serviceId,
            timeSlot: $timeSlot
        );

        $this->appointmentRepository->save($appointment);

        $this->paymentService->dispatchPaymentProcess($appointment);

        return new CreateAppointmentOutputDTO(
            appointmentId: $appointment->getId(),
            status: $appointment->getStatus()->value,
            message: "Appointment created successfully. Waiting for payment confirmation."
        );
    }
}
