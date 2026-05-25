---
name: sepay-php-integration
description: 'Workflow and patterns for integrating SePay Payment Gateway into Laravel/PHP applications. Use for updating or creating payment checkouts, callback routes, and IPN webhook handlers for one-time payments via bank transfer.'
user-invocable: true
disable-model-invocation: false
---

# SePay PHP Integration

Use this skill when implementing, debugging, or migrating the SePay Payment Gateway (Bank Transfer QR / NAPAS / International Cards) in the current Laravel project. This workflow follows the official SePay integration process ensuring secure payment processing, correct IPN validations, and appropriate frontend callbacks.

## When to Use
- Adding a new payment gateway for checkout flows
- Creating or fixing callback routes for successful, failed, or cancelled payments
- Implementing or debugging Instant Payment Notification (IPN) webhooks (`/payment/ipn`)
- Updating payment credentials for production Go-Live

## Procedure

### 1. Installation & Configuration Validation
- Ensure the package `sepay/sepay-pg` is installed (`composer require sepay/sepay-pg`).
- Validate that standard credential environment variables are expected in `.env`:
  - `SEPAY_MERCHANT_ID`
  - `SEPAY_SECRET_KEY`
  - `SEPAY_ENVIRONMENT` (defaults to `sandbox` locally or `production` for live)

### 2. Generate Checkout Request
When instantiating a checkout form for a specific order:
- Use `SePay\SePayClient` and `SePay\Builders\CheckoutBuilder`.
- Required parameters include: `currency`, `orderInvoiceNumber`, `orderAmount`, `operation: 'PURCHASE'`, and `orderDescription`.
- Define the absolute callback URLs to your application (`successUrl`, `errorUrl`, `cancelUrl`).
- Provide the generated form HTML back to the client or render it in a Blade view.

### 3. Handle Frontend Callbacks (GET Routes)
Expose specific endpoints to handle buyer redirections.
- **Success URL**: Show pending verification/success layout. *(Crucial: Do not perform critical business logic here that assumes the payment is completely finalized; rely on the IPN webhook for actual payment verification.)*
- **Error URL**: Present payment failure message.
- **Cancel URL**: Present cancellation feedback.

### 4. Implement IPN Webhook (POST Route)
Create the secure server-line connection to SePay:
- Must be a `POST` route (`/payment/ipn`) and **exempt from CSRF protection** in Laravel (`VerifyCsrfToken` middleware or `bootstrap/app.php` in Laravel 11).
- Parse the JSON payload `Request $request`.
- Verify `$request->input('notification_type') === 'ORDER_PAID'`.
- Look up the associated order using `$request->input('order.order_invoice_number')`.
- Update the order's fulfillment/payment status to `paid`.
- **CRITICAL**: Always return a `200 OK` response with JSON `['success' => true]` to acknowledge receipt so SePay halts re-sending.

### 5. Production / Go-Live Transition
- Remind stakeholders that production requires transitioning the environment to `production` in `.env` and swapping to Production API (`https://pay.sepay.vn/v1/checkout/init` handles this under the SDK).
- IPN URL must publicly route to the production domain.

## Code Patterns

**Checkout Handler Generation:**
```php
use SePay\SePayClient;
use SePay\Builders\CheckoutBuilder;

$sepay = new SePayClient(env('SEPAY_MERCHANT_ID'), env('SEPAY_SECRET_KEY'), env('SEPAY_ENVIRONMENT', 'sandbox'));

$checkoutData = CheckoutBuilder::make()
    ->currency('VND')
    ->orderInvoiceNumber($order->invoice_number)
    ->orderAmount($order->total_amount)
    ->operation('PURCHASE')
    ->orderDescription("Payment for Order #{$order->invoice_number}")
    ->successUrl(route('payment.success', ['order' => $order->id]))
    ->errorUrl(route('payment.error', ['order' => $order->id]))
    ->cancelUrl(route('payment.cancel', ['order' => $order->id]))
    ->build();

// Return to view or output form HTML
$formHtml = $sepay->checkout()->generateFormHtml($checkoutData);
```

**IPN Handler Scaffold:**
```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::post('/payment/ipn', function (Request $request) {
    if ($request->input('notification_type') === 'ORDER_PAID') {
        $invoiceNumber = $request->input('order.order_invoice_number');
        $amount = $request->input('transaction.transaction_amount');
        
        // Find order, verify amount if needed, and update status
        // e.g., Order::where('invoice_number', $invoiceNumber)->update(['status' => 'paid']);
        Log::info("SePay IPN: Order {$invoiceNumber} marked as paid.");
    }
    
    return response()->json(['success' => true], 200);
});
// Don't forget to exclude /payment/ipn from CSRF checks!
```
