<?php

namespace App\Services;

use App\Models\Booking;
use SePay\Builders\CheckoutBuilder;
use SePay\SePayClient;

class SePayService
{
    protected SePayClient $client;

    public function __construct()
    {
        $this->client = new SePayClient(
            (string) config('sepay.merchant_id'),
            (string) config('sepay.secret_key'),
            $this->environment()
        );
    }

    public function createCheckoutHtml(Booking $booking): string
    {
        $checkoutData = CheckoutBuilder::make()
            ->currency('VND')
            ->paymentMethod('BANK_TRANSFER')
            ->orderAmount((int) $booking->total_price)
            ->operation('PURCHASE')
            ->orderDescription('Thanh toan ve xe King Express Bus - Code: ' . $booking->booking_code)
            ->orderInvoiceNumber($booking->booking_code)
            ->successUrl(route('client.sepay.success', ['code' => $booking->booking_code]))
            ->errorUrl(route('client.sepay.error', ['code' => $booking->booking_code]))
            ->cancelUrl(route('client.sepay.cancel', ['code' => $booking->booking_code]))
            ->build();

        return $this->client->checkout()->generateFormHtml(
            $checkoutData,
            $this->environment(),
            [
                'id' => 'sepay-checkout-form',
                'no_submit_button' => true,
            ]
        );
    }

    public function verifyIpnSecret(?string $headerSecret): bool
    {
        $secretKey = (string) config('sepay.secret_key');

        return $secretKey !== '' && is_string($headerSecret) && hash_equals($secretKey, $headerSecret);
    }

    private function environment(): string
    {
        return config('sepay.environment') === SePayClient::ENVIRONMENT_PRODUCTION
            ? SePayClient::ENVIRONMENT_PRODUCTION
            : SePayClient::ENVIRONMENT_SANDBOX;
    }
}
