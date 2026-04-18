<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\DTOs\CreateAppointmentInputDTO;
use App\Application\UseCases\CreateAppointmentUseCase;
use App\Domain\Exceptions\DomainException;
use App\Interfaces\Http\Requests\CreateAppointmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AppointmentController extends Controller
{
    public function __construct(
        private CreateAppointmentUseCase $createAppointmentUseCase
    ) {
    }

    public function store(CreateAppointmentRequest $request): JsonResponse
    {
        try {
            $input = new CreateAppointmentInputDTO(
                customerId: $request->validated('customer_id'),
                barberId: $request->validated('barber_id'),
                serviceId: $request->validated('service_id'),
                startTime: $request->validated('start_time'),
                endTime: $request->validated('end_time')
            );

            $output = $this->createAppointmentUseCase->execute($input);

            return response()->json([
                'success' => true,
                'data' => [
                    'appointment_id' => $output->appointmentId,
                    'status' => $output->status,
                    'message' => $output->message,
                ]
            ], 201);

        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.'
            ], 500);
        }
    }
}
