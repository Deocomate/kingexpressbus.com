<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the cancel-booking action slide-over form.
 * The 'custom' value for cancel_reason triggers a required custom_reason textarea.
 */
class CancelBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancel_reason' => ['required', 'string'],
            'custom_reason' => [
                'required_if:cancel_reason,custom',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /** The final cancel reason string passed to BookingService::cancelBooking(). */
    public function resolvedReason(): string
    {
        if ($this->input('cancel_reason') === 'custom') {
            return trim((string) $this->input('custom_reason', ''));
        }

        return $this->input('cancel_reason');
    }
}
