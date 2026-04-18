<?php

namespace App\Infrastructure\Queue\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(
        private string $appointmentId
    ) {
    }

    public function handle(): void
    {
        Log::info("Processing payment for appointment: {$this->appointmentId}");

        // Simulate artificial delay
        sleep(2);

        // Simulate random success/failure
        $status = rand(1, 100) > 20 ? 'approved' : 'rejected';

        // Simulate hitting an external payment gateway which then fires a webhook back to our system
        // Note: In real life, the gateway hits our webhook asynchronously.
        // For simulation, we'll just hit our own webhook route.
        $webhookUrl = config('app.url') . '/api/webhooks/payment';
        
        try {
            Http::post($webhookUrl, [
                'appointment_id' => $this->appointmentId,
                'status' => $status,
            ]);
            Log::info("Payment webhook sent for {$this->appointmentId} with status {$status}");
        } catch (\Exception $e) {
            Log::error("Failed to send webhook: " . $e->getMessage());
            throw $e; // Retry
        }
    }
}
