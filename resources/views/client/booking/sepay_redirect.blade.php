<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('client.booking.sepay.redirect_title') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            align-items: center;
            background: #fffdf7;
            color: #0f172a;
            display: flex;
            font-family: "Be Vietnam Pro", system-ui, sans-serif;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .loader {
            background: #fff;
            border: 1px solid #ede4d3;
            border-radius: 2px;
            box-shadow: 0 24px 60px -34px rgba(4, 17, 31, 0.38);
            max-width: 420px;
            padding: 32px;
            text-align: center;
        }

        h1 {
            font-family: "Be Vietnam Pro", system-ui, sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            margin: 0 0 12px;
        }

        p {
            color: #64748b;
            line-height: 1.65;
            margin: 0;
        }

        .spinner {
            animation: spin 0.9s linear infinite;
            border: 4px solid #ffefbf;
            border-top-color: #ff9b00;
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

        @media (prefers-reduced-motion: reduce) {
            .spinner {
                animation-duration: 1.8s;
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
