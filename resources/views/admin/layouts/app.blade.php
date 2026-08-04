<!DOCTYPE html>
<html lang="vi" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') — KingExpressBus</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body
    class="h-full font-sans antialiased"
    x-data
    x-init="$store.toast.init()"
>
    <div class="flex h-full min-h-screen">
        @include('admin.partials.sidebar')

        <div class="flex flex-1 flex-col min-w-0">
            @include('admin.partials.topbar')

            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.store('toast').show('{{ e(session('success')) }}', 'success');
                        });
                    </script>
                @endif
                @if(session('error'))
                    <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.store('toast').show('{{ e(session('error')) }}', 'error');
                        });
                    </script>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @include('admin.partials.toast-host')
</body>
</html>
