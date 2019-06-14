<style>
    #bar-nav:hover {
      color: #ffffff;
      background: #FF6C00;
    }
</style>
<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ url('/') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>F</b>P</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>Fit</b>Project </span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <!-- <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <i class="fas fa-bars"></i>
            <span class="sr-only">{{ trans('adminlte_lang::message.togglenav') }}</span>
        </a> -->
        <a href="" id="bar-nav">
            <i class="fas fa-bars" data-toggle="offcanvas" style="color: #000000; font-size: 1.2rem; margin-top: 15px; margin-left: 15px;"></i>
        </a>
        
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">{{ trans('adminlte_lang::message.login') }}</a></li>
                @else
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <img src="{{ Storage::url(Auth::user()->avatar) }}" class="user-image" alt="User Image"/>
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">{{ Auth::user()->fullName }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header tw-bg-grey-lighter">
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" class="img-circle" alt="User Image" />
                                <p>
                                    <span>{{ Auth::user()->full_name }}</span>
                                    <small>Miembro desde: {{ Auth::user()->created_at->diffForHumans() }}</small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="col-xs-12 text-center">
                                    <a href="#">FitProject</a>
                                </div>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{ route('admin.profile.edit') }}" class="vg-button tw-bg-indigo tw-py-2 tw-border-indigo">
                                        {{ trans('adminlte_lang::message.profile') }}
                                    </a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/logout') }}"
                                       class="vg-button tw-text-grey-dark hover:tw-text-black tw-py-2 tw-border"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ trans('adminlte_lang::message.signout') }}
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>

<script>
    jQuery(document).ready(function($) {
        $("#bar-nav").hover(function() {
           $(".fa-bars").attr('style', 'color: #ffffff; font-size: 1.2rem; margin-top: 15px; margin-left: 15px;');
        }, function() {
           $(".fa-bars").attr('style', 'color: #000000; font-size: 1.2rem; margin-top: 15px; margin-left: 15px;');
        });
    });
</script>
