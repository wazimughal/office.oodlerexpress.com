@extends('adminpanel.admintemplate')
@push('title')
    <title>Edit User | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit User</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Edit User</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Edit User</h3>
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-6">
                                 <!-- flash-message -->
                                <div class="flash-message">
                                  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                    @if(Session::has('alert-' . $msg))
                              
                                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                    @endif
                                  @endforeach
                                </div> <!-- end .flash-message -->
                                </div>
                                <div class="col-3">&nbsp;</div>
                              </div>
                                <form method="POST" action="{{ route('admin.update_user_data',$id) }}">
                                    @csrf
                                   
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                          <div class="input-group mb-3">
                                            <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror"
                                                placeholder="First Name" value="{{ $userData['firstname'] }}">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                            @error('firstname')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                    
                                        </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                          <div class="input-group mb-3">
                                            <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror"
                                                placeholder="Last name" value="{{ $userData['lastname'] }}">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                            @error('lastname')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                    
                                        </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            {{ $userData['email'] }}
                                        <div class="input-group mb-3">
                                          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                              placeholder="Email (Leave empty if you don't want to change)" >
                                          <div class="input-group-append">
                                              <div class="input-group-text">
                                                  <span class="fas fa-envelope"></span>
                                              </div>
                                          </div>
                                          @error('email')
                                              <div class="invalid-feedback">
                                                  {{ $message }}
                                              </div>
                                          @enderror
                                      </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                        <div class="input-group mb-3">
                                          <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                              placeholder="Phone" value="{{ $userData['phone'] }}">
                                          <div class="input-group-append">
                                              <div class="input-group-text">
                                                  <span class="fas fa-phone"></span>
                                              </div>
                                          </div>
                                          @error('phone')
                                              <div class="invalid-feedback">
                                                  {{ $message }}
                                              </div>
                                          @enderror
                                      </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    
                                    
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <label>Password</label>
                                          <div class="input-group mb-3">
                                            <input type="password" name="password" placeholder="Leave it empty if you don't want to change"
                                                class="form-control @error('password') is-invalid @enderror" >
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    
                                    {{-- New Row Button --}}
                                    <div class="row form-group">
                                        <div class="col-5">&nbsp;</div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i
                                                    class="fa fa-save"></i> Save Changes</button>
                                        </div>
                                        <div class="col-5">&nbsp;</div>

                                    </div>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>

                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
   
@endsection

@section('head-js-css')
  <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('footer-js-css')
<!-- Select2 -->
<script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
    
<script>
    $(function() {
        $('.select2bs4').select2({
            theme: 'bootstrap4'
            });
        });
</script>
@endsection