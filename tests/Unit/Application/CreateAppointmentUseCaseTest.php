<?php

namespace Tests\Unit\Application;

use App\Application\DTOs\CreateAppointmentInputDTO;
use App\Application\Services\PaymentServiceInterface;
use App\Application\UseCases\CreateAppointmentUseCase;
use App\Domain\Entities\Appointment;
use App\Domain\Exceptions\DomainException;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreateAppointmentUseCaseTest extends TestCase
{
    public function test_can_create_appointment_successfully()
    {
        // 1. Arrange
        $repositoryMock = $this->createMock(AppointmentRepositoryInterface::class);
        $repositoryMock->method('hasOverlappingAppointments')->willReturn(false);
        $repositoryMock->expects($this->once())->method('save');

        $paymentServiceMock = $this->createMock(PaymentServiceInterface::class);
        $paymentServiceMock->expects($this->once())->method('dispatchPaymentProcess');

        $useCase = new CreateAppointmentUseCase($repositoryMock, $paymentServiceMock);

        $input = new CreateAppointmentInputDTO(
            customerId: 'c1',
            barberId: 'b1',
            serviceId: 's1',
            startTime: '2026-05-01 10:00:00',
            endTime: '2026-05-01 11:00:00'
        );

        // 2. Act
        $output = $useCase->execute($input);

        // 3. Assert
        $this->assertEquals('pending', $output->status);
        $this->assertNotEmpty($output->appointmentId);
    }

    public function test_throws_exception_if_outside_business_hours()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Appointments can only be scheduled between 09:00 and 18:00.");

        $repositoryMock = $this->createMock(AppointmentRepositoryInterface::class);
        $paymentServiceMock = $this->createMock(PaymentServiceInterface::class);

        $useCase = new CreateAppointmentUseCase($repositoryMock, $paymentServiceMock);

        $input = new CreateAppointmentInputDTO(
            customerId: 'c1',
            barberId: 'b1',
            serviceId: 's1',
            startTime: '2026-05-01 08:00:00', // 8 AM is outside business hours
            endTime: '2026-05-01 09:00:00'
        );

        $useCase->execute($input);
    }
}
