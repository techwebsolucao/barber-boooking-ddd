<?php

namespace App\Application\UseCases;

use App\Domain\Exceptions\DomainException;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\ValueObjects\AppointmentStatus;

class ProcessPaymentWebhookUseCase
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository
    ) {
    }

    public function execute(string $appointmentId, string $paymentStatus): void
    {
        $appointment = $this->appointmentRepository->findById($appointmentId);

        if (!$appointment) {
            throw new DomainException("Appointment not found.");
        }

        // Idempotency check: if already confirmed or canceled, ignore
        if ($appointment->getStatus() !== AppointmentStatus::PENDING) {
            return;
        }

        if ($paymentStatus === 'approved') {
            $appointment->confirm();
        } else {
            // Cancel if payment failed
            $appointment->cancel(new \DateTimeImmutable());
        }

        $this->appointmentRepository->save($appointment);
    }
}
