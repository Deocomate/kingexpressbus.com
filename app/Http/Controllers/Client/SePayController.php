<?php

namespace App\Http\Controllers\Client;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\SePayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SePayController extends Controller
{
    public function __construct(
        protected SePayService $sePayService,
        protected BookingService $bookingService
    ) {
    }

    public function redirect(string $code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();

        if ($booking->status === BookingStatus::Cancelled) {
            return redirect()
                ->route('client.home')
                ->with('error', __('client.booking.sepay.booking_cancelled'));
        }

        if ($booking->status !== BookingStatus::Confirmed) {
            return redirect()
                ->route('client.home')
                ->with('error', __('client.booking.sepay.booking_not_confirmed'));
        }

        if ($booking->payment_status === PaymentStatus::Paid) {
            return redirect(URL::temporarySignedRoute(
                'client.booking.success',
                now()->addHours(24),
                ['booking' => $booking->id],
            ));
        }

        if ($booking->payment_method !== PaymentMethod::OnlineBanking) {
            return redirect()
                ->route('client.home')
                ->with('error', __('client.booking.sepay.invalid_payment_method'));
        }

        $htmlForm = $this->sePayService->createCheckoutHtml($booking);

        return view('client.booking.sepay_redirect', compact('booking', 'htmlForm'));
    }

    public function success(string $code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();
        $booking = $this->waitForPaidStatus($booking);

        return redirect(URL::temporarySignedRoute(
            'client.booking.success',
            now()->addHours(24),
            ['booking' => $booking->id],
        ))->with('sepay_payment_returned', true);
    }

    public function error(string $code)
    {
        Booking::where('booking_code', $code)->firstOrFail();

        return redirect()
            ->route('client.home')
            ->with('error', __('client.booking.sepay.payment_failed'));
    }

    public function cancel(string $code)
    {
        Booking::where('booking_code', $code)->firstOrFail();

        return redirect()
            ->route('client.home')
            ->with('warning', __('client.booking.sepay.payment_cancelled'));
    }

    public function ipn(Request $request)
    {
        if (!$this->sePayService->verifyIpnSecret($request->header('X-Secret-Key'))) {
            Log::warning('SePay IPN rejected because the secret key is invalid.', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();

        if (($payload['notification_type'] ?? null) !== 'ORDER_PAID') {
            return response()->json(['success' => true]);
        }

        $invoiceNumber = data_get($payload, 'order.order_invoice_number');
        $transactionId = data_get($payload, 'transaction.transaction_id');
        $transactionAmount = $this->normalizeAmount(data_get($payload, 'transaction.transaction_amount'));
        $orderAmount = $this->normalizeAmount(data_get($payload, 'order.order_amount'));
        $paidAmount = $transactionAmount > 0 ? $transactionAmount : $orderAmount;
        $bookingIdForMail = null;

        if (!is_string($invoiceNumber) || $invoiceNumber === '') {
            Log::warning('SePay IPN skipped because order invoice number is missing.', [
                'transaction_id' => $transactionId,
            ]);

            return response()->json(['success' => true]);
        }

        DB::transaction(function () use ($invoiceNumber, $transactionId, $paidAmount, $payload, &$bookingIdForMail): void {
            $booking = Booking::where('booking_code', $invoiceNumber)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                Log::warning('SePay IPN skipped because booking was not found.', [
                    'booking_code' => $invoiceNumber,
                    'transaction_id' => $transactionId,
                ]);

                return;
            }

            if ((int) $booking->total_price !== $paidAmount) {
                Log::warning('SePay IPN skipped because payment amount does not match booking total.', [
                    'booking_id' => $booking->id,
                    'booking_code' => $invoiceNumber,
                    'booking_total' => (int) $booking->total_price,
                    'paid_amount' => $paidAmount,
                    'transaction_id' => $transactionId,
                ]);

                return;
            }

            if ($booking->status !== BookingStatus::Confirmed) {
                Log::warning('SePay IPN skipped because booking is not confirmed for payment.', [
                    'booking_id' => $booking->id,
                    'booking_code' => $invoiceNumber,
                    'booking_status' => $booking->status,
                    'transaction_id' => $transactionId,
                ]);

                return;
            }

            if ($booking->payment_method !== PaymentMethod::OnlineBanking) {
                Log::warning('SePay IPN ignored: wrong payment method', [
                    'booking_id' => $booking->id,
                    'booking_code' => $invoiceNumber,
                    'payment_method' => $booking->payment_method?->value ?? $booking->payment_method,
                    'transaction_id' => $transactionId,
                ]);

                return;
            }

            if ($booking->payment_status === PaymentStatus::Paid) {
                return;
            }

            DB::table('bookings')->where('id', $booking->id)->update([
                'payment_status' => PaymentStatus::Paid->value,
                'payment_transaction_id' => is_string($transactionId) ? $transactionId : null,
                'payment_log' => json_encode($payload),
                'updated_at' => now(),
            ]);

            $bookingIdForMail = (int) $booking->id;
        });

        if ($bookingIdForMail !== null) {
            $this->bookingService->sendApprovalEmail($bookingIdForMail);
            Log::info('SePay IPN marked booking as paid.', [
                'booking_id' => $bookingIdForMail,
                'booking_code' => $invoiceNumber,
                'transaction_id' => $transactionId,
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function normalizeAmount(mixed $amount): int
    {
        if (!is_numeric($amount)) {
            return 0;
        }

        return (int) round((float) $amount);
    }

    private function waitForPaidStatus(Booking $booking): Booking
    {
        if ($booking->payment_status === PaymentStatus::Paid) {
            return $booking;
        }

        for ($attempt = 0; $attempt < 5; $attempt++) {
            usleep(500000);
            $booking->refresh();

            if ($booking->payment_status === PaymentStatus::Paid) {
                break;
            }
        }

        return $booking;
    }
}
