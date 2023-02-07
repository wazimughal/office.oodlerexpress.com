@extends('adminpanel.admintemplate')
@push('title')
    <title>Request quote | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Delivery </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Edit Delivery </li>
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
                                <h3 class="card-title">Edit Delivery </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-6">
                                        <!-- flash-message -->
                                        <div class="flash-message">
                                            @if($errors->any())
                                                {{ implode('', $errors->all('<div>:message</div>')) }}
                                            @endif

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
                                <form method="POST" action="{{ route('delivery.save_delivery_edit',$id) }}">
                                    @csrf
                                    <input type="hidden" name="quote_type" value="{{$quotesData['quote_type']}}" id="quote_type">

                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <div class="btn-group">
                                                    <button onclick=select_quote_type('single') id="single"  type="button" style="width: 300px" class="{{($quotesData['quote_type']=='single')?'active':''}} btn btn-primary">Single Unit</button>
                                                    <button onclick=select_quote_type('multi')  id="multi" type="button" style="width: 300px" class="{{($quotesData['quote_type']=='multi')?'active':''}} btn btn-primary">Multi Unit</button>
                                                  </div>
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    
                                    <div style="{{($quotesData['quote_type']=='single')?'display: none;':''}}" id="multi_unit_data">
                                        @if($quotesData['quote_type']=='multi')
                                        <div class="row form-group"><div class="col-4">&nbsp;</div><div class="col-3"><div class="input-group mb-2"><div class="form-group clearfix"><label>Is there an elevator? </label>&nbsp;<div class="icheck-primary d-inline"><input type="radio" id="elevator1" value="1" name="elevator" {{($quotesData['elevator']==1)?'checked':''}}><label for="elevator1">Yes </label></div> &nbsp;<div class="icheck-primary d-inline"><input type="radio" value="0" id="elevator2" name="elevator" {{($quotesData['elevator']==0)?'checked':''}}><label for="elevator2">No</label></div></div></div></div><div class="col-3">&nbsp;</div></div>
                                        <div class="row form-group"><div class="col-4">&nbsp;</div><div class="col-3"><label>How Many Appartments?</label><div class="input-group mb-2"><input type="number" name="no_of_appartments" value="{{$quotesData['no_of_appartments']}}" required class="form-control"></div></div><div class="col-3">&nbsp;</div></div>
                                        <div class="row form-group"><div class="col-4">&nbsp;</div><div class="col-3">
                                            <div id="listof_floors"><label>List All Floors</label>
                                        @php
                                            $floors=json_decode($quotesData['list_of_floors'],true);
                                           
                                            foreach ($floors as $key => $floor) {
                                               echo '<div class="input-group mb-2"><input type="text" value="'.$floor.'" name="list_of_floors[]" placeholder="Floor?" required class="form-control"></div>'; 
                                            }
                                        @endphp
                                        </div><div style="width: 90px; float:right;" onclick="addmore_floors()" class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add more</div></div> <div class="col-3">&nbsp;</div></div>   
                                        @endif
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">&nbsp;</div>
                                        <div class="col-4">
                                            <div class="input-group mb-3">
                                                <div class="form-group clearfix">
                                                    <label>Business type  </label>&nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{phpslug('curbside')}}" type="radio" id="business_type1" name="business_type"
                                                           {{($quotesData['business_type']=='curbside')?'checked':''}} >
                                                        <label for="business_type1">Curbside </label>
                                                    </div> &nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{phpslug('distribution')}}" type="radio" id="business_type2" name="business_type" {{($quotesData['business_type']=='distribution')?'checked':''}}>
                                                        <label for="business_type2">Distribution</label>
                                                    </div>

                                                </div>
                                                @error('business_type')
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
                                                <input value="{{ $quotesData['po_number'] }}" placeholder="Please enter PO Number" type="text" name="po_number" required
                                                    class=" form-control @error('po_number') is-invalid @enderror">
                                                    @error('po_number')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                <span style="margin-top: 15px;" for="PO"> <strong>NOTE<sup>*</sup>
                                                        :</strong> If you have multiple pickup addresses and delivery
                                                    addresses, please submit them as separate POs.</span>
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>

                                    @php
                                        //p($quotesData);
                                    @endphp
                                    {{-- End --}}
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <label>Pickup Address</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="Pickup street address" type="text"
                                                    name="pickup_street_address1" id="pickup_street_address1" required value="{{ $quotesData['pickup_street_address'] }}"
                                                    class=" form-control @error('pickup_street_address1') is-invalid @enderror">
                                                @error('pickup_street_address1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input  value="{{ $quotesData['pickup_unit'] }}" placeholder="Unit/STE"  type="text" name="pickup_unit1"
                                                    class=" form-control @error('pickup_unit1') is-invalid @enderror">
                                                @error('pickup_unit1')
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
                                            <div class="input-group mb-3">
                                                <input required value="{{ $quotesData['pickup_city'] }}" placeholder="City Name"  type="text" name="pickup_city1"
                                                    class=" form-control @error('pickup_city1') is-invalid @enderror">
                                                @error('pickup_city1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input required value="{{ $quotesData['pickup_state'] }}" placeholder="State Name"  type="text" name="pickup_state1"
                                                    class=" form-control @error('pickup_state1') is-invalid @enderror">
                                                @error('pickup_state1')
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
                                            <div class="input-group mb-3">
                                                <input required value="{{ $quotesData['pickup_zipcode'] }}" placeholder="Zip Code"  type="text" name="pickup_zipcode1"
                                                    class=" form-control @error('pickup_zipcode1') is-invalid @enderror">
                                                @error('pickup_zipcode1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Contact No.(e.g +18633335555)" type="text" name="pickup_contact_number1" required value="{{ $quotesData['pickup_contact_number'] }}"
                                                    class=" form-control @error('pickup_contact_number1') is-invalid @enderror">
                                                @error('pickup_contact_number1')
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
                                            <div class="input-group date" id="pick_up_reservationdate1" data-target-input="nearest">
                                                <input type="text" id="pickup_date1" value="{{ $quotesData['pickup_date'] }}" required name="pickup_date1" placeholder="Pick Up date" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                                                <div class="input-group-append" data-target="#pick_up_reservationdate1" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Pick up Email" type="text" name="pickup_email1" required value="{{ $quotesData['pickup_email'] }}"
                                                    class=" form-control @error('pickup_email1') is-invalid @enderror">
                                                @error('pickup_email1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    {{-- List of items --}}
                                   <?php
                                   $total_items=0;
                                  
                                      foreach($customer_products as $key=>$data){
                                        ?>
                                        <div class="row form-group">
                                          <div class="col-1">&nbsp;</div>
                                          <div class="col-10 text-center card-header alert-secondary ">
                                            <label>{{$data['name']}}</label>
                                          </div>
                                        </div>
                                        <?php 
                                        $total_items=$total_items+count($data['products']);
                                        foreach($data['products'] as $k=>$proData){ 

                                          $product=get_selected_product($proData['id'],$id,1,$proData);
                                          echo $product['product_list'];
                                        }
                                      
                                      }
                                    
                                   ?>
                                
                                

{{--  New Stop Of listing --}}

                                   @for ($i=2; $i<4; $i++)
                                   @php
                                   
                                   $product_pickup_dropoff['id'] = '';
                                   $product_pickup_dropoff['pickup_street_address'] = '';
                                   $product_pickup_dropoff['pickup_unit'] = '';
                                   $product_pickup_dropoff['pickup_state'] = '';
                                   $product_pickup_dropoff['pickup_city'] = '';
                                   $product_pickup_dropoff['pickup_zipcode'] = '';
                                   $product_pickup_dropoff['pickup_contact_number'] = '';
                                   $product_pickup_dropoff['pickup_date'] = '';
            
                                   $product_address=get_product_pickup_dropoff($id,$i);
                                   
                                   if(isset($product_address['pickup_dropoff_address']) && count($product_address['pickup_dropoff_address'])>0)
                                      $product_pickup_dropoff=$product_address['pickup_dropoff_address'];
                              
                                      

                                        $style='display: none';
                                       if($pickup_dropoff2_flag && $i==2)
                                       $style='display:block';

                                       if($pickup_dropoff3_flag && $i==3)
                                       $style='display: block';

                                   @endphp
                                   <div id="new_pickup_{{$i}}" style="{{$style}}">
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <label>Pickup Address {{$i}}</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="Pickup street address" type="text"
                                                    name="pickup_street_address{{$i}}" id="pickup_street_address{{$i}}" value="{{ $product_pickup_dropoff['pickup_street_address'] }}"
                                                    class=" form-control @error('pickup_street_address'.$i) is-invalid @enderror">
                                                @error('pickup_street_address'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input value="{{ $product_pickup_dropoff['pickup_unit'] }}" placeholder="Unit/STE"  type="text" name="pickup_unit{{$i}}"
                                                    class=" form-control @error('pickup_unit'.$i) is-invalid @enderror">
                                                @error('pickup_unit'.$i)
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
                                            <div class="input-group mb-3">
                                                <input value="{{ $product_pickup_dropoff['pickup_city']}}" placeholder="City Name"  type="text" name="pickup_city{{$i}}"
                                                    class=" form-control @error('pickup_city'.$i) is-invalid @enderror">
                                                @error('pickup_city'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input value="{{ $product_pickup_dropoff['pickup_state']}}" placeholder="State Name"  type="text" name="pickup_state{{$i}}"
                                                    class=" form-control @error('pickup_state'.$i) is-invalid @enderror">
                                                @error('pickup_state'.$i)
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
                                            <div class="input-group mb-3">
                                                <input value="{{ $product_pickup_dropoff['pickup_zipcode']}}" placeholder="Zip Code"  type="text" name="pickup_zipcode{{$i}}"
                                                    class=" form-control @error('pickup_zipcode'.$i) is-invalid @enderror">
                                                @error('pickup_zipcode')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Contact No.(e.g +18633335555)" type="text" name="pickup_contact_number{{$i}}" value="{{ $product_pickup_dropoff['pickup_contact_number']}}"
                                                    class=" form-control @error('pickup_contact_number'.$i) is-invalid @enderror">
                                                @error('pickup_contact_number'.$i)
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
                                            <div class="input-group date" id="pick_up_reservationdate{{$i}}" data-target-input="nearest">
                                                <input type="text" value="{{ $product_pickup_dropoff['pickup_date']}}" id="pickup_date{{$i}}" name="pickup_date{{$i}}" placeholder="Pick Up date" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                                                <div class="input-group-append" data-target="#pick_up_reservationdate{{$i}}" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Pick up Email" type="text" name="pickup_email{{$i}}" value="{{isset($product_pickup_dropoff['pickup_email'])?$product_pickup_dropoff['pickup_email']:''}}"
                                                    class=" form-control @error('pickup_email'.$i) is-invalid @enderror">
                                                @error('pickup_email'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    {{-- List of items --}}
                                   <?php
                                   $total_items=0;
                                   
                                      foreach($customer_products as $key=>$data){
                                        ?>
                                        <div class="row form-group">
                                          <div class="col-1">&nbsp;</div>
                                          <div class="col-10 text-center card-header alert-secondary ">
                                            <label>{{$data['name']}}</label>
                                          </div>
                                        </div>
                                        <?php 
                                        $total_items=$total_items+count($data['products']);
                                        foreach($data['products'] as $k=>$proData){ 
                                            
                                            $product=get_selected_product($proData['id'],$id,$i,$proData);
                                            echo $product['product_list'];
                                         }
                                }
                                ?>
                                                        
                               

                                   </div>
                                   @endfor
{{-- END --}}
                                @if(!$pickup_dropoff3_flag)
                                    <div id="add_new_pickup_btn" class="row form-group">
                                        <div class="col-5">&nbsp;</div>
                                        <div class="col-2">
                                            <span onclick="add_new_pickup()" class="btn btn-outline-success btn-block btn-lg"><i class="fa fa-plus"></i> Add Stop</span>
                                        </div>
                                        <div class="col-5">&nbsp;</div>
                                    </div>
                                    @endif
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <label>Drop off Address</label>
                                            <div class="input-group mb-3">
                                                <input required placeholder="Drop off address" type="text" name="drop_off_street_address" id="drop_off_street_address" value="{{ $quotesData['drop_off_street_address'] }}"
                                                    class=" form-control @error('drop_off_street_address') is-invalid @enderror">
                                                @error('drop_off_street_address')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input placeholder="Unit/STE" type="text" name="drop_off_unit" value="{{ $quotesData['drop_off_unit'] }}"
                                                    class=" form-control @error('drop_off_unit') is-invalid @enderror">
                                                @error('drop_off_unit')
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
                                            <div class="input-group mb-3">
                                                <input required placeholder="State" type="text" name="drop_off_state" value="{{ $quotesData['drop_off_state'] }}"
                                                class=" form-control @error('drop_off_state') is-invalid @enderror">
                                                @error('drop_off_state')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input required placeholder="City Name" type="text" name="drop_off_city" value="{{ $quotesData['drop_off_city'] }}"
                                                class=" form-control @error('drop_off_city') is-invalid @enderror">
                                                @error('drop_off_city')
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
                                            <div class="input-group mb-3">
                                                <input required placeholder="Zip Code" type="text" name="drop_off_zipcode" value="{{ $quotesData['drop_off_zipcode'] }}"
                                                class=" form-control @error('drop_off_zipcode') is-invalid @enderror">
                                                <div id="otherzipcode2"></div>
                                                @error('drop_off_zipcode')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input required placeholder="Contact No.(e.g +18633335555)" value="{{ $quotesData['drop_off_contact_number'] }}" type="text" name="drop_off_contact_number"
                                                    class=" form-control @error('drop_off_contact_number') is-invalid @enderror">
                                                @error('drop_off_contact_number')
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
                                        <div class="col-10">
                                            <div class="input-group mb-3">
                                                <textarea placeholder="Special pickup or delivery instructions "  name="drop_off_instructions"
                                                    class=" form-control @error('drop_off_instructions') is-invalid @enderror">{{ $quotesData['drop_off_instructions'] }}</textarea>
                                                @error('drop_off_instructions')
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
                                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                <input required type="text" value="{{ $quotesData['drop_off_date'] }}" name="drop_off_date" placeholder="Delivery date" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                                @error('drop_off_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input required placeholder="Drop off Email" value="{{ $quotesData['drop_off_email'] }}" type="text" name="drop_off_email"
                                                    class=" form-control @error('drop_off_email') is-invalid @enderror">
                                                @error('drop_off_email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    
                                   
                                    <div class="row form-group">
                                        <div class="col-5">&nbsp;</div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i class="fa fa-save"></i> Save</button>
                                        </div>
                                        <div class="col-5">&nbsp;</div>

                                    </div>
                                </form>
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
    <!-- date-range-picker -->
    <script src="{{ url('adminpanel/plugins/daterangepicker/daterangepicker.js') }}"></script>
    {{-- For google Address --}}
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key={{config('constants.google_api_key')}}"></script> 

    <script>
         @php
        $address=['pickup_street_address1','pickup_street_address2','pickup_street_address3','drop_off_street_address']   
        @endphp
    $(document).ready(function () {
        var autocomplete;
        @foreach ($address as $key=>$addr )
        autocomplete = new google.maps.places.Autocomplete((document.getElementById('{{$addr}}')), {
            types: ['geocode']
           
        });  
        @endforeach
      
    
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var near_place = autocomplete.getPlace();
        });
    });

            function addmore_items1(id, slug){
            row_html=$('#item_row1_'+id).html();
            $('#duplicate_row1_'+id).append(row_html);

            }
            function addmore_items2(id, slug){
            row_html=$('#item_row2_'+id).html();
            $('#duplicate_row2_'+id).append(row_html);

            }
            function addmore_items3(id, slug){
            row_html=$('#item_row3_'+id).html();
            $('#duplicate_row3_'+id).append(row_html);

            }

            @php
                
                if($pickup_dropoff2_flag)
                echo 'let _counter=3;';
                else
                echo 'let _counter=2;';

            @endphp
            
            
            function add_new_pickup(){
            
            $('#new_pickup_'+_counter).show('slow');
            if(_counter==3)
            $('#add_new_pickup_btn').html('');
            _counter++;

            }

        
        var ctr = 1;
        var counter = {{$total_items}};
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
              //Date picker
            $('#pick_up_reservationdate1').datetimepicker({
                format: 'L'
            });
            $('#pick_up_reservationdate2').datetimepicker({
                format: 'L'
            });
            $('#pick_up_reservationdate3').datetimepicker({
                format: 'L'
            });
            $('#reservationdate').datetimepicker({
                format: 'L'
            });
        });
            // Select Quote Type Single/Multi
            function select_quote_type(is_type){
                if(is_type=='single'){
                    $('#multi_unit_data').hide('slow');
                    $('#multi_unit_data').html('');
                    $('#quote_type').val('single');
                }else{
                    
                    multi_unit_html ='<div class="row form-group"><div class="col-4">&nbsp;</div><div class="col-3"><div class="input-group mb-2"><div class="form-group clearfix"><label>Is there an elevator? </label>&nbsp;<div class="icheck-primary d-inline"><input type="radio" id="elevator1" value="1" name="elevator" checked><label for="elevator1">Yes </label></div> &nbsp;<div class="icheck-primary d-inline"><input type="radio" value="0" id="elevator2" name="elevator"><label for="elevator2">No</label></div></div></div></div><div class="col-3">&nbsp;</div></div>';
                    multi_unit_html +='<div class="row form-group"><div class="col-4">&nbsp;</div><div class="col-3"><label>How Many Appartments?</label><div class="input-group mb-2"><input type="number" name="no_of_appartments" value="0" required class="form-control"></div></div><div class="col-3">&nbsp;</div></div>';
                    multi_unit_html +='<div class="row form-group"><div class="col-4">&nbsp;</div><div class="col-3"><div id="listof_floors"><label>List All Floors</label><div class="input-group mb-2"><input type="text" name="list_of_floors[]" placeholder="Floor?" required class="form-control"></div></div><div style="width: 90px; float:right;" onclick="addmore_floors()" class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add more</div></div> <div class="col-3">&nbsp;</div></div>';
                    
                    $('#multi_unit_data').html(multi_unit_html);
                    $('#multi_unit_data').show('slow');
                    $('#single').removeClass('active');
                    $('#quote_type').val('multi');
                }
                
            }
           

        function addmore_floors() {
            ctr++;
            var removeBtn = '<div onclick=$("#floorinput_' + ctr +
                '").remove()  style="width:20px; cursor:pointer; padding:10px; color:red;"><i class="fas fa-minus"></i></div>';
            var listof_floors = '<div id="floorinput_' + ctr +
                '" class="input-group mb-2"><input type="text" required name="list_of_floors[]" placeholder="Floor?" class="form-control">' +
                removeBtn + '</div>';
            $('#listof_floors').append(listof_floors);
        }

    </script>
@endsection
