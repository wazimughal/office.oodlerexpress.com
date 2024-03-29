@extends('adminpanel.admintemplate')
@push('title')
    <title>Add Customer | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add New Customer</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add New Customer</li>
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
                                <h3 class="card-title">Add New Customer</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-6">
                                        @if ($errors->any())
<div class="alert alert-danger">
    <ul class="list-unstyled">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
                                        <!-- flash-message -->
                                        <div class="flash-message">
                                            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                                @if (Session::has('alert-' . $msg))
                                                    <p class="alert alert-{{ $msg }}">
                                                        {!! Session::get('alert-' . $msg) !!} <a href="#" class="close"
                                                            data-dismiss="alert" aria-label="close">&times;</a></p>
                                                @endif
                                            @endforeach
                                        </div> <!-- end .flash-message -->
                                    </div>
                                    <div class="col-3">&nbsp;</div>
                                </div>
                                <form method="POST" action="{{ route('admin.customers.save') }}">
                                    @csrf
                                    <div class="row form-group">
                                        
                                        <div class="offset-lg-1 col-lg-5">
                                            <h3 class="title-form">Customer information: </h3>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        
                                        <div class="offset-lg-1 col-lg-5">
                                            <span>Frist Name</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="firstname"
                                                    class="form-control @error('firstname') is-invalid @enderror"
                                                    placeholder="First name" value="{{ old('firstname') }}">
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
                                        <div class="col-lg-5">
                                            <span>Last Name</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="lastname"
                                                    class="form-control @error('lastname') is-invalid @enderror"
                                                    placeholder="Last name" value="{{ old('lastname') }}">
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
                                        
                                    </div>
                                    <div class="row form-group">
                                       
                                        <div class="offset-lg-1 col-lg-5">
                                            <span>Email</span>
                                            <div class="input-group mb-3">
                                                <input type="email" name="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="Email" value="{{ old('email') }}">
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
                                        <div class="col-lg-5">
                                            <span>Customer Password</span>
                                            <div class="input-group mb-3">
                                                <input type="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Password " value="{{ old('password') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-lg-5 offset-lg-1">
                                            <span>Billing Email</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="billing_email"
                                                    class="form-control @error('billing_email') is-invalid @enderror"
                                                    placeholder="Billing Email Address" value="{{ old('billing_email') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('billing_email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <span>Cell No.</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="mobileno"
                                                    class="form-control @error('mobileno') is-invalid @enderror"
                                                    placeholder="Cell No." value="{{ old('mobileno') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('mobileno')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-lg-5 offset-lg-1">
                                            <span>Position in Business</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="designation"
                                                    class="form-control @error('designation') is-invalid @enderror"
                                                    placeholder="Position in Business" value="{{ old('designation') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('designation')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Business Information --}}
                                    <div class="row form-group">
                                        
                                        <div class="offset-lg-1 col-lg-5">
                                            <h3 class="title-form">Business information: </h3>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        
                                        <div class="offset-lg-1 col-lg-5">
                                            <span>Business Name</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="business_name"
                                                    class="form-control @error('business_name') is-invalid @enderror"
                                                    placeholder="Business Name" value="{{ old('business_name') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-envelope"></span>
                                                    </div>
                                                </div>
                                                @error('business_name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-5">
                                            <span>Business Address</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="business_address"
                                                    class="form-control @error('business_address') is-invalid @enderror"
                                                    placeholder="Business Address" value="{{ old('business_address') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('business_address')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        
                                    </div>


                                    <div class="row form-group">
                                        
                                        <div class="offset-lg-1 col-lg-5">
                                            <span>City</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="city"
                                                    class="form-control @error('city') is-invalid @enderror"
                                                    placeholder="City" value="{{ old('city') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('city')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <span>State</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="state"
                                                    class="form-control @error('state') is-invalid @enderror"
                                                    placeholder="State" value="{{ old('state') }}">
                                               
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('state')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                    <div class="row form-group">
                                        
                                        <div class="offset-lg-1 col-lg-5">
                                            <span>Zip Code</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="zipcode"
                                                    class="form-control @error('zipcode') is-invalid @enderror"
                                                    placeholder="Zip Code" value="{{ old('zipcode') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>

                                                @error('zipcode')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                
                                            </div>

                                        </div>
                                        <div class="col-lg-5">
                                            <span>Business Email</span>
                                            <div class="input-group mb-3">
                                                <input type="email" name="business_email"
                                                    class="form-control @error('business_email') is-invalid @enderror"
                                                    placeholder="Business Email" value="{{ old('business_email') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-envelope"></span>
                                                    </div>
                                                </div>
                                                @error('business_email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                    <div class="row form-group">
                                        
                                        <div class="offset-lg-1 col-lg-5">
                                            <span>Business Phone</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="business_phone"
                                                    class="form-control @error('business_phone') is-invalid @enderror"
                                                    placeholder="Business Phone No" value="{{ old('business_phone') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('business_phone')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <span>How many years in Business?</span>
                                            <div class="input-group mb-3">
                                                <input type="number" name="years_in_business"
                                                    class="form-control @error('years_in_business') is-invalid @enderror"
                                                    placeholder="How Many Years in Business" value="{{ old('years_in_business') }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-envelope"></span>
                                                    </div>
                                                </div>
                                                @error('years_in_business')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        
                                        <div class="offset-lg-1 col-lg-5">
                                            <span>What do you Ship?</span>
                                            <div class="input-group mb-3">
                                                <select name="shipping_cat[]"  class="form-control select2bs4 @error('shipping_cat') is-invalid @enderror" multiple="multiple" data-placeholder="What do you Ship" >
                                                    {!! getProductCatOptions()!!}
                                                </select>
                                                
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-address-card"></span>
                                                    </div>
                                                </div>
                                                @error('shipping_cat')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <span>How often you ship?</span>
                                            <div class="input-group mb-3">
                                                <select  name="how_often_shipping"
                                                    class="form-control select2bs4 @error('how_often_shipping') is-invalid @enderror"
                                                    placeholder="How Often do you ship" value="{{ old('how_often_shipping') }}">
                                                    <option value="daily">Daily</option>
                                                    <option value="weekly">Weekly</option>
                                                    <option value="monthly">Monthly</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-envelope"></span>
                                                    </div>
                                                </div>
                                                @error('how_often_shipping')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    {{-- New Row Button --}}
                                    <div class="row form-group">
                                        <div class="offset-lg-3 col-lg-6">
                                            <button type="submit" class="btn btn-success btn-block"><i
                                                    class="fa fa-save"></i> Submit</button>
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
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function() {
            $('.select2').select2();

            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });

        // Shorthand for $( document ).ready()
        function changeCity() {

            selectOption = $('#city option:selected').text();
            $('#cityname').val(selectOption);

            console.log('option' + selectOption);
            if (selectOption == 'Other') {
                otherCity = '<input  type="text" name="othercity" class="form-control" placeholder="City Name" required> ';
                $('#othercity').html(otherCity);
            } else {
                $('#othercity').html('');
            }
        };

        function change_state() {

            selectOption = $('#state_id option:selected').text();
            $('#statename').val(selectOption);

            console.log('option' + selectOption);
            if (selectOption == 'Other') {
                otherState =
                    '<input  type="text" name="otherstate" class="form-control" placeholder="State Name" required> ';
                $('#otherstate').html(otherState);
            } else {
                $('#otherstate').html('');
            }
        };

        function changezipcode() {
            selectOption = $('#zipcode_id option:selected').text();
            $('#zipcode_no').val(selectOption);

            if (selectOption == 'Other') {
                otherZipCode =
                    '<input  type="text" name="otherzipcode" class="form-control" placeholder="Please enter Zip Code" required>';
                $('#otherzipcode').html(otherZipCode);
            } else {
                $('#otherzipcode').html('');
            }
        };
        function change_shiping_cat() {
         
            selectOption = $('#shipping_cat option:selected').text();
            //$('#other_shipping').val(selectOption);
            if (selectOption == 'Other') {
                othershipping =
                    '<input  type="text" name="othershipping" class="form-control" placeholder="Shiping?" required>';
                $('#othershipping').html(othershipping);
            } else {
                $('#othershipping').html('');
            }
        };
    </script>
@endsection
