<?php

namespace App\Application\Services;

use App\Domain\Entities\Appointment;

interface PaymentServiceInterface
{
    public function dispatchPaymentProcess(Appointment $appointment): void;
}
