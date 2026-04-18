<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Appointment;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\ValueObjects\AppointmentStatus;
use App\Domain\ValueObjects\TimeSlot;
use App\Infrastructure\Persistence\Eloquent\AppointmentModel;
use DateTimeImmutable;

class AppointmentRepositoryEloquent implements AppointmentRepositoryInterface
{
    public function save(Appointment $appointment): void
    {
        AppointmentModel::updateOrCreate(
            ['id' => $appointment->getId()],
            [
                'customer_id' => $appointment->getCustomerId(),
                'barber_id' => $appointment->getBarberId(),
                'service_id' => $appointment->getServiceId(),
                'start_time' => $appointment->getTimeSlot()->getStartTime()->format('Y-m-d H:i:s'),
                'end_time' => $appointment->getTimeSlot()->getEndTime()->format('Y-m-d H:i:s'),
                'status' => $appointment->getStatus()->value,
            ]
        );
    }

    public function findById(string $id): ?Appointment
    {
        $model = AppointmentModel::find($id);

        if (!$model) {
            return null;
        }

        return new Appointment(
            id: $model->id,
            customerId: $model->customer_id,
            barberId: $model->barber_id,
            serviceId: $model->service_id,
            timeSlot: new TimeSlot(
                new DateTimeImmutable($model->start_time),
                new DateTimeImmutable($model->end_time)
            ),
            status: AppointmentStatus::from($model->status)
        );
    }

    public function hasOverlappingAppointments(string $barberId, DateTimeImmutable $startTime, DateTimeImmutable $endTime): bool
    {
        return AppointmentModel::where('barber_id', $barberId)
            ->where('status', '!=', AppointmentStatus::CANCELED->value)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime->format('Y-m-d H:i:s'))
                      ->where('end_time', '>', $startTime->format('Y-m-d H:i:s'));
                });
            })
            ->exists();
    }
}
