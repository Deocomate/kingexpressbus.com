<!DOCTYPE html>
<html lang="vi" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Đăng nhập') — KingExpressBus</title>
    @vite(['resources/css/admin.css'])
    @include('admin.layouts._cdn-assets')
</head>
<body class="h-full font-sans antialiased" x-data>
    @yield('content')
</body>
</html>
