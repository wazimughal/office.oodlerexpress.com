
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a style="background: #fff;" href="{{url('admin/dashboard')}}" class="brand-link">
    <img src="{{url('adminpanel/dist/img/logo_oodler.png')}}" alt="OodlerExpress CRM" width="100%">
    </a>
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

   
@php
  $record_count=get_record_count();
@endphp
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
               @if ($user->group_id==config('constants.groups.admin'))
          <li class="nav-item menu-open {{ request()->segment(2) == 'dashboard' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->segment(2) == 'dashboard' ? 'active' : '' }}">
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

          <li class="nav-item {{ request()->segment(2) == 'reports' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-images"></i>
              <p>Reports<i class="right fas fa-angle-left"></i> </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('quotes.deliveries')}}"  class="nav-link {{ request()->segment(2) == 'reports' ? 'active' : '' }}">
                  <i class="far fa-copy"></i>
                  <p>Quotes/Deliveries</p>
                </a>
              </li>
              
            </ul>
          </li>

         
          <li class="nav-item {{ request()->segment(2) == 'lead' || request()->segment(2) == 'leads' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-building"></i>
              <p>
                Leads
                <i class="right fas fa-angle-left"></i>
                <span class="badge badge-info right">{{$record_count['total_leads']}}</span>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('admin/leads')}}"  class="nav-link {{ request()->segment(2) == 'leads' ? 'active' : '' }}">
                  <i class="fa fa-hospital"></i>
                  <p>All Lead List
                    <span class="badge badge-info right">{{$record_count['total_leads']}}</span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/lead/office')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Office Leads<span class="badge badge-info right">{{$record_count['office']}}</span></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/lead/web')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Web Lead <span class="badge badge-info right">{{$record_count['web']}}</span></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/lead/trashed')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Trash Lead</p>
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
          @endif
          @if ($user->group_id==config('constants.groups.admin'))
          <li class="nav-item {{ request()->segment(2) == 'customers' || request()->segment(2) == 'customer' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Customers
                <i class="right fas fa-angle-left"></i>
                <span class="badge badge-info right">{{$record_count['customer']}}</span>
              </p>
            </a>
           
            <ul class="nav nav-treeview">
              {{-- {{ request()->segment(2) == 'customers' ? 'active' : '' }} --}}
              <li class="nav-item">
                <a href="{{url('admin/customers')}}"  class="nav-link ">
                  <i class="fa fa-user"></i>
                  <p> Customer List <span class="badge badge-info right">{{$record_count['customer']}}</span></p>
                </a>
              </li>
              
              <li class="nav-item">
                <a href="{{route('admin.customersaddform') }}"  class="nav-link ">
                  <i class="fa fa-plus"></i>
                  <p>Add Customer</p>
                </a>
              </li>
             
              <li class="nav-item">
                <a href="{{route('admin.customer','trash') }}"  class="nav-link ">
                  <i class="fa fa-trash"></i>
                  <p>Trashed Customer</p>
                </a>
              </li>
            </ul>
          
          </li>
          @endif

          @if ($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.customer'))
          {{-- This portion is only for admin of the customer --}}
          <li class="nav-item {{ (request()->segment(2) == 'quotes' || request()->segment(2) == 'quote') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-images"></i>
              <p>
                Quotes
                <i class="right fas fa-angle-left"></i> <span class="badge badge-info right">{{$record_count['total_quotes']}}</span>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('admin.quote.types','requested')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p>Requested quotes <span class="badge badge-info right">{{$record_count['pending_quotes']}}</span></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin.quote.types','new')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p>New quotes <span class="badge badge-info right">{{$record_count['submitted_quotes']}}</span></p>
                </a>
              </li>
              @if ($user->group_id==config('constants.groups.admin'))
              <li class="nav-item">
                <a href="{{route('admin.quote.types','approved')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p>Approved quotes <span class="badge badge-info right">{{$record_count['approved_quotes']}}</span></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin.quote.types','cancelled')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p>Cancelled quotes <span class="badge badge-info right">{{$record_count['declined_quotes']}}</span></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin.quote.types','trash')}}" class="nav-link">
                  <i class="fa fa-trash"></i>
                  <p>Trashed <span class="badge badge-info right">{{$record_count['trashed_quotes']}}</span></p>
                </a>
              </li>
              @endif
            </ul>
          </li>
          @endif

          @if ($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.driver'))
          <li class="nav-item {{ (request()->segment(2) == 'deliveries' || request()->segment(2) == 'delivery') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-images"></i>
              <p>
                Deliveries
                <i class="right fas fa-angle-left"></i>
                <span class="badge badge-info right">{{($record_count['total_deliverable']+$record_count['total_delivered'])}}</span>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('admin.deliveries')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p>All deliveries <span class="badge badge-info right">{{($record_count['total_deliverable']+$record_count['total_delivered'])}}</span></p>
                </a>
              </li>
             </ul>
          </li>
          
          
          @endif
          @if ($user->group_id==config('constants.groups.admin'))
          <li class="nav-item {{ request()->segment(2) == 'drivers' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-images"></i>
              <p>
                Drivers
                
                <i class="right fas fa-angle-left"></i>
                <span class="badge badge-info right">{{($record_count['driver'])}}</span>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/drivers')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p> Drivers List <span class="badge badge-info right">{{($record_count['driver'])}}</span></p>
                </a>
              </li>
              
              <li class="nav-item">
                <a href="{{url('admin/drivers/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Drivers</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('drivers.trashed','trashed')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p> Trashed</p>
                </a>
              </li>
            
              
            </ul>
          </li>
          <li class="nav-item {{ request()->segment(2) == 'products' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-images"></i>
              <p>
                Products
                <i class="right fas fa-angle-left"></i>
                <span class="badge badge-info right">{{($record_count['total_products'])}}</span>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/products')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p> Products List <span class="badge badge-info right">{{($record_count['total_products'])}}</span></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/products/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Products</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/products/categories')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Categories <span class="badge badge-info right">{{($record_count['total_product_categories'])}}</span></p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item {{ request()->segment(2) == 'users' ? 'menu-open' : '' }}">
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
            <a href="{{url('/admin/colors')}}" class="nav-link">
              <i class="nav-icon far fa-calendar-alt"></i>
              <p>
                Color Management
              </p>
            </a>
          </li>
          <li class="nav-item {{ request()->segment(2) == 'activity-log' ? 'menu-open' : '' }}">
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
          @endif
          <li class="nav-item {{ request()->segment(2) == 'calender' ? 'menu-open' : '' }}">
            <a href="{{route('user.calender')}}" class="nav-link">
              <i class="nav-icon far fa-calendar-alt"></i>
              <p>
                Calendar
               
              </p>
            </a>
          </li>
          
          
          <li class="nav-item">
            <a href="{{route('admin.logout')}}" class="nav-link">
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