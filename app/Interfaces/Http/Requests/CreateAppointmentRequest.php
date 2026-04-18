<?php

namespace App\Interfaces\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|uuid',
            'barber_id' => 'required|uuid',
            'service_id' => 'required|uuid',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ];
    }
}
