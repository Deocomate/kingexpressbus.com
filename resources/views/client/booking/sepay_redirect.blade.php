<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('client.booking.sepay.redirect_title') }}</title>
    <style>
        body {
            align-items: center;
            background: #f8fafc;
            color: #111827;
            display: flex;
            font-family: Arial, sans-serif;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .loader {
            max-width: 420px;
            padding: 32px;
            text-align: center;
        }

        .spinner {
            animation: spin 0.9s linear infinite;
            border: 4px solid #fde68a;
            border-top-color: #d97706;
            border-radius: 999px;
            height: 48px;
            margin: 0 auto 24px;
            width: 48px;
        }

        form {
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <main class="loader">
        <div class="spinner" aria-hidden="true"></div>
        <h1>{{ __('client.booking.sepay.redirect_heading') }}</h1>
        <p>{{ __('client.booking.sepay.redirect_message', ['code' => $booking->booking_code]) }}</p>
        {!! $htmlForm !!}
    </main>

    <script>
        window.addEventListener('load', function () {
            const form = document.getElementById('sepay-checkout-form') || document.querySelector('form');

            if (form) {
                form.submit();
            }
        });
    </script>
</body>
</html>
