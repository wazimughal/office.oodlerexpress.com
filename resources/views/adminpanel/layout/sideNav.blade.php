  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('admin/dashboard')}}" class="brand-link">
      {{-- {{config('constants.app_name')}} --}}
      <span class="brand-text font-weight-light">{{config('constants.app_name')}}</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{url('adminpanel/dist/img/avatar.png')}}" class="img-circle elevation-2" alt="User Image">
        </div>
      
        <div class="info">
          <a href="#" class="d-block">{{ $user->name}}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      {{-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> --}}

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('admin/dashboard')}}" class="nav-link">
                  <i class="far fa-building nav-icon"></i>
                  <p>Dashboard </p>
                </a>
              </li>
             
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-building"></i>
              <p>
                Leads
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/leads')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>All Lead List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/lead/pending')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Pending Lead</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/lead/approved')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Aproved Lead</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/lead/cancelled')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Cancelled Lead</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/leads/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Lead</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Customers
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/customers')}}" class="nav-link">
                  <i class="fa fa-user"></i>
                  <p> Customer List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/customers/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Customer</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-images"></i>
              <p>
                Drivers
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/drivers')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p> Drivers List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/drivers/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Drivers</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-users"></i>
              <p>
                USERS
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('admin/users')}}" class="nav-link">
                  <i class="far fa-user nav-icon"></i>
                  <p>User</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/users/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                
                     <p>Add User</p>
                </a>
              </li>
              
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-images"></i>
              <p>
                Activities
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/activity-log')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p> Activity Log</p>
                </a>
              </li>
             
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{url('/admin/logout')}}" class="nav-link">
              <i class="nav-icon fa fa-user"></i>
              <p>
               Logout
              </p>
            </a>
            
          </li>


        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>