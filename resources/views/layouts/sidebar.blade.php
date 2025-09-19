<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: 0.8" />
        <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" />
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search" />
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item menu-open">
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href={{ route('dashboard.index') }} class="nav-link">
                                <i class="fas fa-home nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>
                            Master Data
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href={{ route('datauser.index') }} class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>User</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('role.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Role</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href={{ route('company.index') }} class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Company</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-id-badge"></i>
                        <p>
                            Employee Data
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('Employee.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Employee</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('attandance.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Attendence</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('offrequest.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Off Request</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item menu-open">
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href={{ route('payroll.index') }} class="nav-link">
                                <i class="fas fa-wallet nav-icon"></i>
                                <p>Payroll</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item menu-open">
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href={{ route('recruitment.index') }} class="nav-link">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Recruitment</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item menu-open">
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('events.index') }}" class="nav-link">
                                <i class="fas fa-calendar nav-icon"></i>
                                <p>Event</p>
                            </a>
                        </li>
                    </ul>
                </li>
=======
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon fas fa-folder"></i>
                                 <p>
                                     Master Data
                                     <i class="fas fa-angle-left right"></i>
                                 </p>
                             </a>
                             <ul class="nav nav-treeview">
                                 <li class="nav-item">
                                     <a href={{ route('datauser.index') }} class="nav-link">
                                         <i class="far fa-circle nav-icon"></i>
                                         <p>User</p>
                                     </a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('role.index') }}" class="nav-link">
                                         <i class="far fa-circle nav-icon"></i>
                                         <p>Role</p>
                                     </a>
                                 </li>
                                 <li class="nav-item">
                                     <a href={{ route('company.index') }} class="nav-link">
                                         <i class="far fa-circle nav-icon"></i>
                                         <p>Company</p>
                                     </a>
                                 </li>
                             </ul>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon fas fa-id-badge"></i>
                                 <p>
                                     Employee Data
                                     <i class="fas fa-angle-left right"></i>
                                 </p>
                             </a>
                             <ul class="nav nav-treeview">
                                 <li class="nav-item">
                                     <a href="{{ route('Employee.index') }}" class="nav-link">
                                         <i class="far fa-circle nav-icon"></i>
                                         <p>Employee</p>
                                     </a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('attandance.index') }}" class="nav-link">
                                         <i class="far fa-circle nav-icon"></i>
                                         <p>Attendance</p>
                                     </a>
                                 </li>
                                 {{-- <li class="nav-item">
                                     <a  href="{{ route('attandance.scanView') }}" class="nav-link">
                                         <i class="far fa-circle nav-icon"></i>
                                         <p>Attendence Scan</p>
                                     </a>
                                 </li> --}}
                                 <li class="nav-item">
                                     <a href="{{ route('offrequest.index') }}" class="nav-link">
                                         <i class="far fa-circle nav-icon"></i>
                                         <p>Off Request</p>
                                     </a>
                                 </li>
                             </ul>
                         </li>
                         <li class="nav-item menu-open">
                             <ul class="nav nav-treeview">
                                 <li class="nav-item">
                                     <a href={{ route('payroll.index') }} class="nav-link">
                                         <i class="fas fa-wallet nav-icon"></i>
                                         <p>Payroll</p>
                                     </a>
                                 </li>
                             </ul>
                         </li>
                         <li class="nav-item menu-open">
                             <ul class="nav nav-treeview">
                                 <li class="nav-item">
                                     <a href={{ route('payroll.index') }} class="nav-link">
                                         <i class="fas fa-wallet nav-icon"></i>
                                         <p>Payroll</p>
                                     </a>
                                 </li>
                             </ul>
                         </li>
                         <li class="nav-item menu-open">
                             <ul class="nav nav-treeview">
                                 <li class="nav-item">
                                     <a href={{ route('recruitment.index') }} class="nav-link">
                                         <i class="fas fa-users nav-icon"></i>
                                         <p>Recruitment</p>
                                     </a>
                                 </li>
                             </ul>
                         </li>


                <li class="nav-item menu-open">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="nav-link">
                        <i class="fas fa-sign-out-alt nav-icon"></i>
                        <p>{{ __('Log Out') }}</p>
                    </a>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                        @csrf
                    </form>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
