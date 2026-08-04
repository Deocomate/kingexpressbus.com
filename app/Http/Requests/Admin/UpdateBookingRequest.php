<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Validates admin booking update form.
 *
 * IMPORTANT: status, confirmed_at, payment_status, and payment_transaction_id
 * are NOT in Booking::$fillable and must NOT be changed via mass-assignment.
 * payment_status and payment_transaction_id can be set explicitly after validation
 * if the admin explicitly corrects a payment error. Status transitions must use
 * BookingService actions (confirm / cancel / complete) to ensure emails are sent.
 */
class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bookingId = $this->route('booking');

        return [
            'booking_code'              => ['required', 'string', 'max:50', Rule::unique('bookings', 'booking_code')->ignore($bookingId)],
            'user_id'                   => ['nullable', 'integer', 'exists:users,id'],
            'trip_id'                   => ['required', 'integer', 'exists:trips,id'],
            'booking_date'              => ['required', 'date'],
            'customer_name'             => ['required', 'string', 'max:255'],
            'customer_email'            => ['nullable', 'email', 'max:255'],
            'customer_phone'            => ['required', 'string', 'max:50'],
            'pickup_stop_id'            => ['nullable', 'integer', 'exists:stops,id'],
            'dropoff_stop_id'           => ['required', 'integer', 'exists:stops,id'],
            'quantity'                  => ['required', 'integer', 'min:1'],
            'total_price'               => ['required', 'numeric', 'min:0'],
            'payment_method'            => ['required', new Enum(PaymentMethod::class)],
            'notes'                     => ['nullable', 'string', 'max:2000'],
            'base_unit_price'           => ['required', 'numeric', 'min:0'],
            'global_surcharge_unit'     => ['required', 'numeric', 'min:0'],
            'route_surcharge_unit'      => ['required', 'numeric', 'min:0'],
            'final_unit_price'          => ['required', 'numeric', 'min:0'],
            'total_surcharge_amount'    => ['required', 'numeric', 'min:0'],
            'surcharge_reason_snapshot' => ['nullable', 'string', 'max:2000'],
            // Explicit payment fields — NOT used in fill(), assigned explicitly when needed
            'payment_status'            => ['nullable', Rule::in(['unpaid', 'paid'])],
            'payment_transaction_id'    => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Fields safe for Booking::fill().
     * Excludes: status, confirmed_at, payment_status, payment_transaction_id.
     */
    public function fillableData(): array
    {
        return $this->only([
            'booking_code', 'user_id', 'trip_id', 'booking_date',
            'customer_name', 'customer_email', 'customer_phone',
            'pickup_stop_id', 'dropoff_stop_id', 'quantity', 'total_price',
            'payment_method', 'notes',
            'base_unit_price', 'global_surcharge_unit', 'route_surcharge_unit',
            'final_unit_price', 'total_surcharge_amount', 'surcharge_reason_snapshot',
        ]);
    }
}
