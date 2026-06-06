<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('client.booking.sepay.redirect_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            align-items: center;
            background:
                linear-gradient(135deg, rgba(4, 17, 31, 0.94), rgba(4, 17, 31, 0.82)),
                #04111f;
            color: #111827;
            display: flex;
            font-family: "Be Vietnam Pro", system-ui, sans-serif;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .loader {
            background: #fff;
            border: 1px solid rgba(245, 158, 11, 0.24);
            border-radius: 24px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.24);
            max-width: 420px;
            padding: 32px;
            text-align: center;
        }

        h1 {
            font-family: "Manrope", "Be Vietnam Pro", system-ui, sans-serif;
            font-size: 1.45rem;
            margin: 0 0 12px;
        }

        p {
            color: #64748b;
            line-height: 1.65;
            margin: 0;
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
