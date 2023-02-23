@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Make Payments| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-sm-6">
                        <h1>Make Payments </h1>

                    </div>
                    <div class="col-sm-4">&nbsp;</div>

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
                                <h3 class="card-title">Make Payment</h3>
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
                                {{-- <div class="row form-group">
                                    <div class="offset-md-1">&nbsp;</div>
                                    <div class="col-md-5">
                                        <h3 class="title-form">Customer information: </h3>
                                    </div>
                                    
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Name :</strong></div>
                                    <div class="col-3">{{ $userData['name'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Email :</strong></div>
                                    <div class="col-3">{{ $userData['email'] }}</div>
                                </div>
                                
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Mobile No :</strong></div>
                                    <div class="col-3">{{ $userData['mobileno'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Zip Code :</strong></div>
                                    <div class="col-3">{{ $userData['zipcode'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Address :</strong></div>
                                    <div class="col-3">{{ $userData['address'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Business Name :</strong></div>
                                    <div class="col-3">{{ $userData['business_name'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Business Email :</strong></div>
                                    <div class="col-3">{{ $userData['business_email'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Business Phone :</strong></div>
                                    <div class="col-3">{{ $userData['business_phone'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Business Address :</strong></div>
                                    <div class="col-3">{{ $userData['business_address'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-1">&nbsp;</div>
                                    <div class="col-10">
                                        <hr>
                                    </div>
                                </div> --}}
                                <form method="get" action="{{ route('make_deliveries_payments',$customer_id) }}">
                                    <input type="hidden" name="action" value="makepayment">
                                    <input type="hidden" name="amount" value="{{$amount}}">
                                    <input type="hidden" name="payment_type" id="input_payment_type" value="{{$payment_type}}">
                                    @php
                                        foreach($po_numbers_arr as $key=>$po_number){
                                           echo '<input type="hidden" name="open_balance_quote_id[]" value="'.$key.'">';
                                        }
                                    @endphp
                                    @csrf
                                    <div class="row form-group">
                                        <div class="col-md-6" style="border: 1px solid #acd6fe; margin:0px 5px 0 0px ">
                                    <div class="row form-group" style="margin-top:12px;">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-11">
                                            <h3 class="title-form">Customer information: </h3>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
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
                                        <div class="col-md-5">
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
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
                                            <span>Billing Email Address</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="billing_email"
                                                    class="form-control @error('billing_email') is-invalid @enderror"
                                                    placeholder="Billing Email Address" value="{{ $userData['billing_email'] }}">
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
                                        <div class="col-md-5">
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
                                        
                                    </div>
                                    
                                    {{-- Business Information --}}
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
                                            <h3 class="title-form">Business information: </h3>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
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
                                        <div class="col-md-5">
                                            <span>Business Address</span>
                                            <div class="input-group mb-3">
                                                <input type="text" id="business_address" name="business_address"
                                                    class="form-control @error('business_address') is-invalid @enderror"
                                                    placeholder="Business address" value="{{ $userData['business_address'] }}">
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
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
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
                                        <div class="col-md-5">
                                            <span>Business Phone</span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="business_phone"
                                                    class="form-control @error('business_phone') is-invalid @enderror"
                                                    placeholder="Business Phone No" value="{{ $userData['business_phone'] }}">
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
                                        
                                    </div>
                                    
                                    
                                    
                                </div>
                                <div class="col-md-5" style="border: 1px solid #acd6fe; margin-left:5px ">
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-11">
                                            <h3 class="title-form">Payment information: </h3>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-3"></div>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <div class="btn-group w-100">
                                                    <button onclick=select_payment_method('credit_card') id="credit_card"  type="button" class="{{($payment_type!='ACH')?'active':''}} btn btn-primary">Credit Card</button>
                                                    <button onclick=select_payment_method('ach')  id="ach" type="button" class="btn btn-primary {{($payment_type=='ACH')?'active':''}}">ACH</button>
                                                  </div>
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    @php
                                        //p($quotes_data_arr['quote_data']); die;
                                    @endphp
                                    @foreach ($quotes_data_arr['quote_data'] as $quoteData)
                                    <div class="row form-group">
                                        <div class="col-md-3">PO Numbers:</div>    
                                        <div class="col-md-3">{{$quoteData['po_number']}}</div>
                                        <div class="col-md-3">Invoice#:</div>
                                        <div class="col-md-3">{{$quoteData['qb_invoice_no']}}</div>
                                        {{-- {!!implode('<br> ',$po_numbers_arr);!!} --}}
                                    </div>
                                    @endforeach
                                    <div id="ach_detail" class="mt-3" style="{{($payment_type=='credit_card')?'display:none':''}}">
                                        <div class="row form-group">
                                            <div class="col-md-12">
                                                <div class="input-group mb-3">                                                    
                                                <input type="text" name="routing"
                                                        class="form-control @error('routing') is-invalid @enderror"
                                                        placeholder="Routing (e.g 021000021)" value="{{ old('routing') }}">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text">
                                                                <span class="fas fa-address-card"></span>
                                                            </div>
                                                        </div>
                                                        @error('routing')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-12">
                                                <div class="input-group mb-3">                                                    
                                                <input type="text" name="account_no"
                                                        class="form-control @error('account_no') is-invalid @enderror"
                                                        placeholder="Account No. (e.g 123456789)" value="{{ old('account_no') }}">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text">
                                                                <span class="fas fa-address-card"></span>
                                                            </div>
                                                        </div>
                                                        @error('account_no')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="credit_card_detail" style="{{($payment_type=='ACH')?'display:none':''}}">
                                    <div class="row form-group">
                                        <div class="col-md-12">
                                            <span style="float:right; margin:0 0 5px 0;">
                                                <img class="" src="{{ url('adminpanel/dist/img/credit/visa.png') }}" width="34" id="visa" alt="Visa">
                                                <img class="" src="{{ url('adminpanel/dist/img/credit/mastercard.png') }}" width="34" id="mc" alt="MasterCard">
                                                <img class="" src="{{ url('adminpanel/dist/img/credit/american-express.png') }}" width="34" id="disc" alt="Discover">
                                                
                                            </span> 
                                            <div class="input-group mb-3">
                                                
                                            <input type="text" name="cardnumber"
                                                    class="form-control @error('cardnumber') is-invalid @enderror"
                                                    placeholder="Card Number (e.g 4111111111111111)" value="{{ old('cardnumber') }}">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span class="fas fa-address-card"></span>
                                                        </div>
                                                    </div>
                                                    @error('cardnumber')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-3">
                                            <span>Expiry month</span>
                                            <div class="input-group mb-3">
                                            <select class="form-control select2bs4" name="expiry_month">
                                                {!!months_option(old('expiry_month'));!!}
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span>Expiry Year </span>
                                            <div class="input-group mb-3">
                                            <select class="form-control select2bs4" name="expiry_year">
                                                {!!years_option(old('expiry_year'));!!}
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <span>CVV <span style="color:#df1616;font-size:11px;"> (CVV is  3 or 4 digit on back of your card.)</span> </span>
                                            <div class="input-group mb-3">
                                                <input type="number" name="cvv"
                                                class="form-control @error('cvv') is-invalid @enderror"
                                                placeholder="CVV (e.g 123)" value="{{ old('cvv') }}">
                                            </div>
                                        </div>
                                    </div>
                                    </div> 
                                    {{-- End Of Credit Card Details --}}
                                    <div class="row form-group">
                                        <div class="offset-md-3">&nbsp;</div>
                                        <div class="col-md-5">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><strong>TOTAL PAY </strong> ${{$amount}}</i></button>
                                        </div>
                                    </div>

                                </div>
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
    <!-- DataTables  & Plugins -->

    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(function() {
            $('.select2').select2();
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });

        });
        // Select Quote Type Single/Multi
        function select_payment_method(is_type){
                if(is_type=='credit_card'){
                    $('#credit_card_detail').show('slow');
                    $('#ach_detail').hide('slow');

                    $('#credit_card').addClass('active');
                    $('#ach').removeClass('active');

                    $('#input_payment_type').val('credit_card');

                }else{
                    $('#credit_card_detail').hide('slow');
                    $('#ach_detail').show('slow');

                    $('#credit_card').removeClass('active');
                    $('#ach').addClass('active');
                    $('#input_payment_type').val('ACH');
                }
                
            }

      
    </script>
@endsection
