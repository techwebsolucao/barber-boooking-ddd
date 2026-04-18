<?php

namespace App\Infrastructure\Payment;

use App\Application\Services\PaymentServiceInterface;
use App\Domain\Entities\Appointment;
use App\Infrastructure\Queue\Jobs\ProcessPaymentJob;

class FakePaymentService implements PaymentServiceInterface
{
    public function dispatchPaymentProcess(Appointment $appointment): void
    {
        // Dispatches the Laravel Job for asynchronous payment processing
        ProcessPaymentJob::dispatch($appointment->getId());
    }
}
