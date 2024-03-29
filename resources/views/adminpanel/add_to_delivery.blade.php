@extends('adminpanel.admintemplate')
@push('title')
    <title>Add Quote | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View Quote</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">View Quote</li>
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
                                <h3 class="card-title">View Quote</h3>
                            </div>
                            <div class="card-body">


                                <!-- /.row -->

                                <div class="row form-group">
                                    <div class="col-12">
                                        <div class="alert alert-info alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">&times;</button>
                                            <h5><i class="icon fa fa-user"></i>
                                                Quote Status!</h5>
                                                {{quote_status_msg($quotesData['status'])}}

                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Quote Information</strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div>
                                                        <div class="row" style="margin-top: 20px;">
                                                            <div class="col-4">&nbsp;</div>
                                                            <div class="col-4"><strong>PO Number: {{$quotesData['po_number']}}</strong></div>
                                                        </div>
                                                        @if (isset($quotesData['quote_type']) && $quotesData['quote_type']=='multi')
                                                                    <div class="row" style="margin-top: 10px;">
                                                                        <div class="col-4">&nbsp;</div>
                                                                        <div class="col-4">
                                                                            Type: {{$quotesData['quote_type']}}<br>
                                                                            Business Type: {{$quotesData['business_type']}}<br>
                                                                            Elevator: {{($quotesData['elevator']==1)?'YES':'NO';}}<br>
                                                                            Appartments: {{$quotesData['no_of_appartments']}}<br>
                                                                            List of Floors: {{ implode(',',json_decode($quotesData['list_of_floors'],true)) }}<br>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                        <div class="row">
                                                            <div class="col-3">&nbsp;</div>
                                                            <div class="col-6">

                                                               
                                                                <!-- flash-message -->
                                                                <div class="flash-message alert-danger">
                                                                    @if ($errors->any())
                                                                        {!! implode('', $errors->all('<div>:message</div>')) !!}
                                                                    @endif

                                                                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                                                        @if (Session::has('alert-' . $msg))
                                                                            <p class="alert alert-{{ $msg }}">
                                                                                {{ Session::get('alert-' . $msg) }} <a
                                                                                    href="#" class="close"
                                                                                    data-dismiss="alert"
                                                                                    aria-label="close">&times;</a></p>
                                                                        @endif
                                                                    @endforeach
                                                                </div> <!-- end .flash-message -->
                                                            </div>
                                                            <div class="col-3">&nbsp;</div>
                                                        </div>

                                                        
                                                        @php
                                                        //p($quotesData['quote_products']);
                                                        $pickup_dropoff_address=array();
                                                        @endphp
                                                        <div class="card" style="margin-top: 25px;">
                                                            <div class="card-header p-2">
                                                                <strong> Products List</strong>
                                                            </div><!-- /.card-header -->
                                                       
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <tbody>
                                                                        @if (isset($quotesData['quote_products']) && empty($quotesData['quote_products']))
                                                                        <tr>
                                                                         <td colspan="2">
                                                                             <strong>Pick Up Detail </strong> <br>
                                                                             Date : {{ $quotesData['pickup_date'] }}<br>
                                                                             Street Address
                                                                             :{{ $quotesData['pickup_street_address'] }}<br>
                                                                             Unit :{{ $quotesData['pickup_unit'] }}<br>
                                                                             Contact No. :{{ $quotesData['pickup_contact_number'] }}<br>
                                                                         </td>
                                                                         <td colspan="2">
                                                                             <strong>Drop-Off Detail </strong> <br>
                                                                             Date : {{ $quotesData['drop_off_date'] }}<br>
                                                                             Street Address
                                                                             :{{ $quotesData['drop_off_street_address'] }}<br>
                                                                             Unit :{{ $quotesData['drop_off_unit'] }}<br>
                                                                             Contact No.
                                                                             :{{ $quotesData['drop_off_contact_number'] }}<br>
                                                                         </td>
                                                                         
                                                                     </tr>   
                                                                        @endif
                                                                        @foreach ($quotesData['quote_products'] as $quote_product)
                                                                        @if (!in_array($quote_product['pickup_dropoff_order_number'],$pickup_dropoff_address))
                                                                        <tr>
                                                                            <td colspan="2">
                                                                                <strong>Pick Up Detail </strong> <br>
                                                                                Date : {{ $quote_product['pickup_dropoff_address']['pickup_date'] }}<br>
                                                                                Street Address
                                                                                :{{ $quote_product['pickup_dropoff_address']['pickup_street_address'] }}<br>
                                                                                Unit :{{ $quote_product['pickup_dropoff_address']['pickup_unit'] }}<br>
                                                                                Contact No. :{{ $quote_product['pickup_dropoff_address']['pickup_contact_number'] }}<br>
                                                                            </td>
                                                                            <td colspan="2">
                                                                                <strong>Drop-Off Detail </strong> <br>
                                                                                Date : {{ $quote_product['pickup_dropoff_address']['drop_off_date'] }}<br>
                                                                                Street Address
                                                                                :{{ $quote_product['pickup_dropoff_address']['drop_off_street_address'] }}<br>
                                                                                Unit :{{ $quote_product['pickup_dropoff_address']['drop_off_unit'] }}<br>
                                                                                Contact No.
                                                                                :{{ $quote_product['pickup_dropoff_address']['drop_off_contact_number'] }}<br>
                                                                            </td>
                                                                            
                                                                        </tr> 
                                                                        <tr>
                                                                            <th>Prodcut Name</th>
                                                                            <th>Quantity</th>
                                                                            <th>Size</th>
                                                                            <th>Description</th>
                                                                        </tr>
                                                                        @php
                                                                            $pickup_dropoff_address[]=$quote_product['pickup_dropoff_order_number'];
                                                                        @endphp

                                                                        @endif
                                                                        <tr>
                                                                            <td>{{ $quote_product['product_name'] }}</td>
                                                                            <td>{{ $quote_product['quantity'] }}</td>
                                                                            <td>{{ $quote_product['size'] }}</td>
                                                                            <td>{{ $quote_product['description'] }}</td>
                                                                        </tr> 
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header p-2">
                                                                <strong> Quotes Sent </strong>
                                                            </div><!-- /.card-header -->
                                                        <?php if($quotesData['quote_prices']){  $k=1;?>
                                                            <div class="form-group row">
                                                                <div class="col-sm-12">
                                                                    <div class="table-responsive">
                                                                        <table class="table">
                                                                            <tr>
                                                                                <td>Price</td>
                                                                                <td>Extra</td>
                                                                                <td>Reason</td>
                                                                                <td>Description</td>
                                                                                <td>Sent On</td>
                                                                                <td>Status</td>
                                                                            </tr>
                                                                            <?php foreach ($quotesData['quote_prices'] as $key=>$data){ ?>
                                                                            <tr>
                                                                                <td>${{ $data['quoted_price'] }}</td>
                                                                                <td>${{ $data['extra_charges'] != '' ? $data['extra_charges'] : 0 }}
                                                                                </td>
                                                                                <td>{{ $data['reason_for_extra_charges'] }}</td>
                                                                                <td>{{ $data['description'] }}</td>
                                                                                <td>{{ date('d/m/Y H:i:s', strtotime($data['created_at'])) }}
                                                                                </td>
                                                                                <td>
                                                                                    @if ($data['status'] == 1)
                                                                                        <span
                                                                                            class="btn btn-success btn-block btn-sm"><i
                                                                                                class="fas fa-chart-line"></i>
                                                                                            Active</span>
                                                                                    @else
                                                                                        <span
                                                                                            class="btn btn-primary btn-block btn-sm"><i
                                                                                                class="fas fa-chart-line"></i>
                                                                                            Previous</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
        
                                                                            <?php }?>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
        
                                                            <?php }else{?>
                                                            <div class="form-group row">
                                                                <div class="col-sm-12 text-center">
                                                                    No quote sent yet!
                                                                </div>
                                                            </div>
                                                            <?php }?>
                                                        </div>
                                                        @if (empty($quotesData['driver']))
                                                        <div style="height: 100px; width:100%">&nbsp;</div>
                                                        <div class="row">
                                                            <div class="col-3">&nbsp;</div>
                                                            <div class="col-6">
                                                             <!-- flash-message -->
                                                             @if ($errors->any())
                                                                    {!! implode('', $errors->all('<div class="alert alert-warning">:message</div>')) !!}
                                                                @endif
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
                                                        <form action="{{route('quotes.add_to_delivery_save',$quotesData['id'])}}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="po_number" value="{{$quotesData['po_number']}}">
                                                            <div class="row form-group">
                                                                <div class="col-md-3 align-center text-center">
                                                                    <label class="col-form-label ">Assign to Driver/Sub</label>
                                                                    <div class="form-group clearfix mt-1">
                                                                        <div class="icheck-primary d-inline ml-1">
                                                                          <input onclick="$('#drivers_options').show();$('.subs_options').hide();" type="radio" value="1" id="radioPrimary1" name="assign_to" {{($quotesData['assign_to']==1|| $quotesData['assign_to']=='')?'checked':''}} >
                                                                          <label for="radioPrimary1"> Driver</label>
                                                                        </div>
                                                                        <div class="icheck-primary d-inline ml-3">
                                                                          <input  onclick="$('.subs_options').show();$('#drivers_options').hide();" type="radio" value="2" id="radioPrimary2"  name="assign_to" {{($quotesData['assign_to']==2)?'checked':''}}>
                                                                          <label for="radioPrimary2"> Sub</label>
                                                                        </div>
                                                                      </div>
                                                                </div>
                                                            <div id="drivers_options" class="col-md-4" {{($quotesData['assign_to']==2)?'style=display:none':''}}>
                                                                <label class="col-form-label">Select Driver</label>
                                                                <div class="input-group mb-3" >
                                                                    <select placeholder="select Driver" name="driver_id" class="select2bs4 form-control @error('photographer_expense[]') is-invalid @enderror">
                                                                        {!!get_drivers_options()!!}
                                                                    </select>
                                                                    @error('driver_id')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 subs_options" {{($quotesData['assign_to']==1|| $quotesData['assign_to']=='')?'style=display:none':''}}>
                                                                <label class="col-form-label">Select Sub</label>
                                                                <div class="input-group mb-3" >
                                                                    <select placeholder="select Sub" name="sub_id" class="select2bs4 form-control @error('photographer_expense[]') is-invalid @enderror">
                                                                        {!!get_subs_options()!!}
                                                                    </select>
                                                                    @error('sub_id')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 subs_options" {{($quotesData['assign_to']==1|| $quotesData['assign_to']=='')?'style=display:none':''}}>
                                                                <label class="col-form-label">Amount</label>
                                                                <input type="number" name="quoted_price_for_sub" 
                                                                    value="{{ $quotesData['quoted_price_for_sub'] }}"
                                                                    placeholder="Price for Sub in USD" class="form-control">
                                                                    @error('quoted_price_for_sub')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                            </div>
                                                            
                                                            <div class="col-md-2" style="margin-top: 2rem">
                                                                <button type="submit"
                                                                    class=" float-right btn btn-success btn-block btn-lg"><i
                                                                        class="fa fa-plus"></i>Delivery</button>
                                                            </div>
                                                            </div>
                                                        </form>
                                                        @endif


                                                    </div>
                                                    <!-- /.tab-pane -->

                                                </div>
                                                <!-- /.tab-content -->
                                            </div><!-- /.card-body -->
                                        </div>
                                        

                                    </div>

                                    <!-- /.col -->

                                    <div class="col-md-4">
                                        @if (isset($quotesData['driver']) && !empty($quotesData['driver']))
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Driver Info</h3>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <form method="post" id="customer_update">
                                                    <input type="hidden" name="action" value="driver_update">
                                                    <input type="hidden" name="uid"
                                                        value="{{ $quotesData['driver']['id'] }}">
                                                    <tbody>
                                                        <tr>
                                                            <th style="width:50%">Name</th>
                                                            <td>{{ $quotesData['driver']['firstname'] }}
                                                                {{ $quotesData['driver']['lastname'] }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email</th>
                                                            <td>{{ $quotesData['driver']['email'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone</th>
                                                            <td>{{ $quotesData['driver']['phone'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>License No</th>
                                                            <td>{{ $quotesData['driver']['license_no'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Address</th>
                                                            <td>{{ $quotesData['driver']['address'] }}</td>
                                                        </tr>
                                                    </tbody>
                                                </form>

                                            </table>
                                        </div>
                                        @endif
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Customer/Business Info</h3>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <form method="post" id="customer_update">
                                                    <input type="hidden" name="action" value="customer_update">
                                                    <input type="hidden" name="uid"
                                                        value="{{ $quotesData['customer']['id'] }}">
                                                    <tbody>
                                                        <tr>
                                                            <th style="width:50%">Name</th>
                                                            <td>{{ $quotesData['customer']['firstname'] }}
                                                                {{ $quotesData['customer']['lastname'] }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email</th>
                                                            <td>{{ $quotesData['customer']['email'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone</th>
                                                            <td>{{ $quotesData['customer']['phone'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Business Name</th>
                                                            <td>{{ $quotesData['customer']['business_name'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Designation</th>
                                                            <td>{{ $quotesData['customer']['designation'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Business Email</th>
                                                            <td>{{ $quotesData['customer']['business_email'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Business Mobile</th>
                                                            <td>{{ $quotesData['customer']['business_mobile'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Business Phone</th>
                                                            <td>{{ $quotesData['customer']['business_phone'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Business Age</th>
                                                            <td>{{ $quotesData['customer']['years_in_business'] }} years
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>How Often Shiping</th>
                                                            <td>{{ $quotesData['customer']['how_often_shipping'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Shiping </th>
                                                            <td>@php
                                                            $car_names=cat_name_by_ids(json_decode($quotesData['customer']['shipping_cat'],true)) ;   
                                                            
                                                            echo implode('<br>',$car_names);
                                                            @endphp
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </form>

                                            </table>
                                        </div>


                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->




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
    <!-- dropzonecss -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.css') }}">
@endsection
@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('adminpanel/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- dropzonejs -->
    <script src="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });
        function do_action(id, action_name = '') {
            var formData = ($('#' + action_name).formToJson());

            var sendInfo = {
                data: formData,
                action: action_name,
                id: id
            };

            if (action_name == 'submit_comment') {
                if ($('#comments').val() == '')
                    return false;
            }
            $('#_loader').show();
            $.ajax({
                url: "{{ url('/admin/quotes/ajaxcall/') }}/" + id,
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    $('#' + action_name + '_replace').append(data.response);
                    $('#comments').val('');
                    $('#_loader').hide();
                    //console.log('result :'+action_name);
                    if (data.error == 'No') {
                        $('#file_' + id).remove();
                        $(document).Toasts('create', {
                            class: 'bg-success',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });
                    } else {
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });
                    }
                }
            });

        }
    </script>
@endsection
