@extends('adminpanel.admintemplate')
@push('title')
    <title>Add to Customer Lead | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add to Customer</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add to Customer</li>
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
                                <h3 class="card-title">Add to Customer</h3>
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
                                                        {{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                                                            data-dismiss="alert" aria-label="close">&times;</a></p>
                                                @endif
                                            @endforeach
                                        </div> <!-- end .flash-message -->
                                    </div>
                                    <div class="col-3">&nbsp;</div>
                                </div>
                                @php
                                    // p($userData);
                                @endphp
                                <form method="POST" action="{{ route('admin.save_add_to_customer', $id) }}">
                                    <input type="hidden" name="email" value="{{$userData['email']}}">
                                    @csrf
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <h3 class="title-form">Customer information: </h3>
                                        </div>

                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <span>Frist Name</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="firstname"
                                                    class="form-control @error('firstname') is-invalid @enderror"
                                                    placeholder="First name" value="{{ $userData['firstname'] }}">
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
                                        <div class="col-5">
                                            <span>Last Name</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="lastname"
                                                    class="form-control @error('lastname') is-invalid @enderror"
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
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <span>Email</span>
                                            <div class="input-group mb-3">
                                                <input type="email" readonly disabled name="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="Email" value="{{ $userData['email'] }}">
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
                                        <div class="col-5">
                                            <span>Customer Password</span>
                                            <div class="input-group mb-3">
                                                <input type="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Customer Password" value="">
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
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <span>Cell No.</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="mobileno"
                                                    class="form-control @error('mobileno') is-invalid @enderror"
                                                    placeholder="Cell No." value="{{ $userData['mobileno'] }}">
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
                                        <div class="col-5">
                                            <span>Position in Business</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="designation"
                                                    class="form-control @error('designation') is-invalid @enderror"
                                                    placeholder="Position in Business"
                                                    value="{{ $userData['designation'] }}">
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
                                        <div class="col-1">&nbsp;</div>
                                    </div>

                                    {{-- Business Information --}}
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <h3 class="title-form">Business information: </h3>
                                        </div>

                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <span>Business Name</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="business_name"
                                                    class="form-control @error('business_name') is-invalid @enderror"
                                                    placeholder="Business Name" value="{{ $userData['business_name'] }}">
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
                                        <div class="col-5">
                                            <span>Business Address</span>
                                            <div class="input-group mb-3">
                                                <input type="text" id="business_address" name="business_address"
                                                    class="form-control @error('business_address') is-invalid @enderror"
                                                    placeholder="business_address"
                                                    value="{{ $userData['business_address'] }}">
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
                                        <div class="col-1">&nbsp;</div>
                                    </div>


                                    {{-- <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <span>City</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="city"
                                                    class="form-control @error('city') is-invalid @enderror"
                                                    placeholder="City Name" value="{{ $userData['city'] }}">
                                                
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
                                        <div class="col-5">
                                            <span>State</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="state"
                                                class="form-control @error('state') is-invalid @enderror"
                                                placeholder="State Name" value="{{ $userData['state'] }}">
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
                                        <div class="col-1">&nbsp;</div>
                                    </div> --}}
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <span>Zip Code</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="zipcode"
                                                class="form-control @error('zipcode') is-invalid @enderror"
                                                placeholder="Zip Code" value="{{ $userData['zipcode'] }}">
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
                                        <div class="col-5">
                                            <span>Business Email</span>
                                            <div class="input-group mb-3">
                                                <input type="email" name="business_email"
                                                    class="form-control @error('business_email') is-invalid @enderror"
                                                    placeholder="Business Email"
                                                    value="{{ $userData['business_email'] }}">
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
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <span>Business Phone</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="business_phone"
                                                    class="form-control @error('business_phone') is-invalid @enderror"
                                                    placeholder="Business Phone No"
                                                    value="{{ $userData['business_phone'] }}">
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
                                        <div class="col-5">
                                            <span>How many years in Business?</span>
                                            <div class="input-group mb-3">
                                                <input type="number" name="years_in_business"
                                                    class="form-control @error('years_in_business') is-invalid @enderror"
                                                    placeholder="How Many Years in Business"
                                                    value="{{ $userData['years_in_business'] }}">
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
                                        
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <span>What do you Ship?</span>
                                            <div class="input-group mb-3">
                                                    <select name="shipping_cat[]" id="shipping_cat" class="form-control select2bs4 @error('shipping_cat') is-invalid @enderror" multiple="multiple" data-placeholder="What do you Ship" style="width: 100%;">
                                                    {!! getProductCatOptions($userData['shipping_cat'])!!}
                                                </select>
                                                @error('shipping_cat')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <span>How often you ship?</span>
                                            <div class="input-group mb-3">
                                                <select name="how_often_shipping"
                                                    class="form-control select2bs4 @error('how_often_shipping') is-invalid @enderror"
                                                    placeholder="How Often do you ship"
                                                    value="{{ $userData['how_often_shipping'] }}">
                                                    <option {{ $userData['how_often_shipping'] == 'daily' ? 'selected' : '' }}
                                                        value="daily">Daily</option>
                                                    <option {{ $userData['how_often_shipping'] == 'weekly' ? 'selected' : '' }}
                                                        value="weekly">Weekly</option>
                                                    <option
                                                        {{ $userData['how_often_shipping'] == 'monthly' ? 'selected' : '' }}
                                                        value="monthly">Monthly</option>
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
                                        <div class="col-5">&nbsp;</div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i
                                                    class="fa fa-save"></i> Save</button>
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
    {{-- Google API --}}
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key={{config('constants.google_api_key')}}"></script> 

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

        $(document).ready(function () {
        var autocomplete;
        
        autocomplete = new google.maps.places.Autocomplete((document.getElementById('business_address')), {
            types: ['geocode']
           
        });  
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var near_place = autocomplete.getPlace();
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
