<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        @if (! Auth::guest())
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->full_name }}</p>
                    <!-- Status -->
                    <a href="{{ route('admin.profile.edit') }}"><i class="fa fa-circle text-success"></i> Editar Perfil</a>
                </div>
            </div>
    @endif

    <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">MENU PRINCIPAL</li>
            <!-- Optionally, you can add icons to the links -->
            <li class="{{ request()->is('/') ? 'active' : '' }}">
                <a  href="/">
                    <i class='fa fa-home'></i> <span>Inicio</span>
                </a>
            </li>
            <li class="{{ request()->is('customer/memberships') ? 'active' : '' }}">
                <a  href="{{ route('customer.memberships.index') }}">
                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                    <span>Mis Membresías</span>
                </a>
            </li>
            <li class="{{ request()->is('customer/payments') ? 'active' : '' }}">
                <a  href="{{ route('customer.payments.index') }}">
                    <i class='fa fa-money'></i>
                    <span>Mis Pagos</span>
                </a>
            </li>
            <li class="{{ request()->is('customer/attendances') ? 'active' : '' }}">
                <a  href="{{ route('customer.attendances.index') }}">
                    <i class='fa fa-calendar'></i> <span>Mis Asistencias</span>
                </a>
            </li>
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
