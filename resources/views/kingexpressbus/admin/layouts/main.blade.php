<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield("title")</title>
    @include('kingexpressbus.admin.components.styles')
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
    @include('kingexpressbus.admin.components.navbar')

    @include('kingexpressbus.admin.components.sidebar')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@yield('title')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">@yield('title')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @yield("content")
            </div>
        </section>
    </div>
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 3.2.0
        </div>
        <strong>Copyright &copy; 2014-2021 <a href="#">{{config("app.name")}}</a>.</strong>
    </footer>
    <aside class="control-sidebar control-sidebar-dark">
    </aside>
</div>

<!-- CK Editor and CK Finder set up -->
@include('ckfinder::setup')
@include('kingexpressbus.admin.components.scripts')
@stack('scripts')
</body>
</html>
