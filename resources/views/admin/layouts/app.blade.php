<!DOCTYPE html>
<html lang="vi" class="h-full overflow-hidden bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') — KingExpressBus</title>
    @vite(['resources/css/admin.css'])
    @include('admin.layouts._cdn-assets')
</head>
<body
    class="h-full overflow-hidden font-sans antialiased"
    x-data
    x-init="$store.toast.init()"
>
    {{-- Single scroll owner: <main>. html/body/shell stay overflow-hidden + h-full
         so the document never grows a second scrollbar. min-h-0 on flex children is
         required — without it flex items refuse to shrink below content height. --}}
    <div class="flex h-full">
        @include('admin.partials.sidebar')

        <div class="flex flex-1 flex-col min-h-0 min-w-0">
            @include('admin.partials.topbar')

            <main class="flex-1 min-h-0 overflow-y-auto p-6">
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

    @stack('scripts')
</body>
</html>
