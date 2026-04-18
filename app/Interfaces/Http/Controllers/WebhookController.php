<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\UseCases\ProcessPaymentWebhookUseCase;
use App\Domain\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private ProcessPaymentWebhookUseCase $processPaymentWebhookUseCase
    ) {
    }

    public function handlePayment(Request $request): JsonResponse
    {
        // Simple validation
        $request->validate([
            'appointment_id' => 'required|string',
            'status' => 'required|string|in:approved,rejected',
        ]);

        $appointmentId = $request->input('appointment_id');
        $status = $request->input('status');

        Log::info("Received payment webhook for {$appointmentId} with status {$status}");

        try {
            $this->processPaymentWebhookUseCase->execute($appointmentId, $status);
            return response()->json(['success' => true]);
        } catch (DomainException $e) {
            Log::warning("Domain exception in webhook: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error("System error in webhook: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal error'], 500);
        }
    }
}
