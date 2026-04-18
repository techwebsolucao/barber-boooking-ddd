<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\AppointmentRepositoryEloquent;
use App\Application\Services\PaymentServiceInterface;
use App\Infrastructure\Payment\FakePaymentService;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AppointmentRepositoryInterface::class,
            AppointmentRepositoryEloquent::class
        );

        $this->app->bind(
            PaymentServiceInterface::class,
            FakePaymentService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
