<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{asset("/admin/dist/img/AdminLTELogo.png")}}" alt="AdminLTE Logo"
             class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">{{config("app.name")}}</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            @include("kingexpressbus.admin.components.menu")
        </nav>
    </div>
</aside>

@push('scripts')
    <script>
        let url = window.location;

        $('ul.nav-sidebar a').filter(function () {
            if (this.href) {
                return this.href == url
            }
        }).addClass('active');

        $('ul.nav-treeview a').filter(function () {
            if (this.href) {
                return this.href == url
            }
        }).parentsUntil(" .nav-sidebar> .nav-treeview").addClass('menu-open').prev('a').addClass('active');
    </script>
@endpush
