@extends('adminpanel.admintemplate')
@push('title')
    <title>Add Delivery | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add new Delivery</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add new Delivery</li>
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
                                <h3 class="card-title">Add new Delivery</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-6">
                                        <!-- flash-message -->
                                        <div class="flash-message">
                                            @if($errors->any())
                                                {!! implode('', $errors->all('<div style="padding:5px" class="alert-danger mb-3">:message</div>')) !!}
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
                                <form method="POST" id="add_delivery_form" action="{{ route('delivery.save_delivery_data',$customer_id) }}">
                                    @csrf
                                    <input type="hidden" name="quote_type" value="single" id="quote_type">

                                    <div class="row form-group">
                                        <div class="offset-md-3"></div>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <div class="btn-group w-100">
                                                    <button onclick=select_quote_type('single') id="single"  type="button" class="active btn btn-primary">Single Unit</button>
                                                    <button onclick=select_quote_type('multi')  id="multi" type="button" class="btn btn-primary">Multi Unit</button>
                                                  </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div style="display: none;" id="multi_unit_data">
                                    
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="input-group mb-3">
                                                <div class="form-group clearfix">
                                                    <label>Delivery type </label>&nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{phpslug('curbside')}}" type="radio" id="business_type1" name="business_type"
                                                            checked>
                                                        <label for="business_type1">Curbside </label>
                                                    </div> &nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{phpslug('Distribution')}}" type="radio" id="business_type2" name="business_type">
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
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-3">&nbsp;</div>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input value="{{ old('po_number') }}" placeholder="Please enter PO Number" type="text" name="po_number" required
                                                    class=" form-control @error('po_number') is-invalid @enderror">
                                                    @error('po_number')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                                {{-- <span style="margin-top: 15px;" for="PO"> <strong>NOTE<sup>*</sup>
                                                        :</strong> If you have multiple pickup addresses and delivery
                                                    addresses, please submit them as separate POs.</span> --}}
                                            </div>
                                        </div>
                                        
                                    </div>


                                    {{-- End --}}
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
                                            <label>Pickup Address</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="Pickup street address" type="text"
                                                    name="pickup_street_address1" id="pickup_street_address1" required value="{{ old('pickup_street_address1') }}"
                                                    class=" form-control @error('pickup_street_address1') is-invalid @enderror">
                                                @error('pickup_street_address1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-md-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input  value="{{ old('pickup_unit1') }}" placeholder="Unit/STE"  type="text" name="pickup_unit1"
                                                    class=" form-control @error('pickup_unit1') is-invalid @enderror">
                                                @error('pickup_unit1')
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
                                            <div class="input-group mb-3">
                                                <input value="{{ old('pickup_zipcode1') }}" placeholder="Zip Code"  type="text" name="pickup_zipcode1"
                                                    class=" form-control @error('pickup_zipcode1') is-invalid @enderror">
                                                @error('pickup_zipcode1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Contact No.(e.g +18633335555)" type="text" name="pickup_contact_number1" value="{{ old('pickup_contact_number1') }}"
                                                    class=" form-control @error('pickup_contact_number1') is-invalid @enderror">
                                                @error('pickup_contact_number1')
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
                                            <div class="input-group date" id="pick_up_reservationdate1" data-target-input="nearest">
                                                <input type="text" id="pickup_date1" value="{{ old('pickup_date1') }}" required name="pickup_date1" placeholder="Pick Up date" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                                                <div class="input-group-append" data-target="#pick_up_reservationdate1" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                    {{-- List of items --}}
                                   <?php
                                   $total_items=0;
                                      foreach($customer_products as $key=>$data){
                                        ?>
                                        <div class="row form-group">
                                          <div class="offset-md-1">&nbsp;</div>
                                          <div class="col-md-10 text-center card-header alert-secondary ">
                                            <label>{{$data['name']}}</label>
                                          </div>
                                        </div>
                                        <?php 
                                        $total_items=$total_items+count($data['products']);
                                        foreach($data['products'] as $k=>$proData){ ?>
                                        <div id="item_row1_{{$proData['id']}}">
                                         <div class="row form-group">
                                          <div class="offset-md-1">&nbsp;</div>
                                          <div class="col-md-4">
                                              <div class="form-group clearfix">
                                                  <div class="icheck-primary d-inline">
                                                      <input type="hidden" name="product_details1[{{$proData['id']}}][cat_id][]" value="{{$data['id']}}">
                                                      <input type="hidden" value="{{$proData['id']}}" name="product_details1[{{$proData['id']}}][product_id][]">
                                                      <input type="checkbox" value="{{$proData['name']}}" name="product_details1[{{$proData['id']}}][product_name][]" id="{{$proData['slug']}}_{{$proData['id']}}">
                                                      <label for="{{$proData['slug']}}_{{$proData['id']}}">
                                                          {{$proData['name']}}
                                                      </label>
                                                  </div>
                                              </div>
  
                                          </div>
                                          <div class="col-md-1">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Quantity" value="1" type="number" name="product_details1[{{$proData['id']}}][item_quantity][]" class=" form-control" required>
                                              </div>
                                          </div>
                                          <div class="col-md-1">
                                              <div class="input-group mb-3">
                                                  <select name="product_details1[{{$proData['id']}}][product_sizes][]"  class="form-control">@php echo get_product_sizes($proData['sizes']); @endphp</select>
                                              </div>
                                          </div>
                                          <div class="col-md-3">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Description" type="text" name="product_details1[{{$proData['id']}}][item_description][]"  class=" form-control @error('delivery_type') is-invalid @enderror">
                                              </div>
                                          </div>
                                          <div class="col-md-1"><div style="width: 90px; float:right;" onclick="addmore_items1({{$proData['id']}},'{{$data['slug']}}')"
                                            class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add
                                            more</div></div>
                                      </div>
                                    </div>
                                      <div id="duplicate_row1_{{$proData['id']}}"></div>
                                      <?php }?>
                                      
                                   @php
                                      }
                                    @endphp
                                
                                

{{--  New Stop Of listing --}}
                                   @for ($i=2; $i<4; $i++)
                                   
                                   <div id="new_pickup_{{$i}}" style="display: none;">
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
                                            <label>Pickup Address {{$i}}</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="Pickup street address" type="text"
                                                id="pickup_street_address{{$i}}" name="pickup_street_address{{$i}}" value="{{ old('pickup_street_address'.$i) }}"
                                                    class=" form-control @error('pickup_street_address'.$i) is-invalid @enderror">
                                                @error('pickup_street_address'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-md-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input value="{{ old('pickup_unit'.$i) }}" placeholder="Unit/STE"  type="text" name="pickup_unit{{$i}}"
                                                    class=" form-control @error('pickup_unit'.$i) is-invalid @enderror">
                                                @error('pickup_unit'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                                    {{-- <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input value="{{ old('pickup_city'.$i) }}" placeholder="City Name"  type="text" name="pickup_city{{$i}}"
                                                    class=" form-control @error('pickup_city'.$i) is-invalid @enderror">
                                                @error('pickup_city'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input value="{{ old('pickup_state'.$i) }}" placeholder="State Name"  type="text" name="pickup_state{{$i}}"
                                                    class=" form-control @error('pickup_state'.$i) is-invalid @enderror">
                                                @error('pickup_state'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        
                                        
                                    </div> --}}
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input value="{{ old('pickup_zipcode'.$i) }}" placeholder="Zip Code"  type="text" name="pickup_zipcode{{$i}}"
                                                    class=" form-control @error('pickup_zipcode'.$i) is-invalid @enderror">
                                                @error('pickup_zipcode')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Contact No.(e.g +18633335555)" type="text" name="pickup_contact_number{{$i}}" value="{{ old('pickup_contact_number'.$i) }}"
                                                    class=" form-control @error('pickup_contact_number'.$i) is-invalid @enderror">
                                                @error('pickup_contact_number'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1"></div>
                                        
                                        {{-- <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input value="{{ old('pickup_email'.$i) }}" placeholder="Email"  type="text" name="pickup_email{{$i}}"
                                                    class=" form-control @error('pickup_email'.$i) is-invalid @enderror">
                                                @error('pickup_email'.$i)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div class="col-md-5">
                                            <div class="input-group date" id="pick_up_reservationdate{{$i}}" data-target-input="nearest">
                                                <input type="text" value="{{ old('pickup_date'.$i) }}" id="pickup_date{{$i}}" name="pickup_date{{$i}}" placeholder="Pick Up date" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                                                <div class="input-group-append" data-target="#pick_up_reservationdate{{$i}}" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    {{-- List of items --}}
                                   <?php
                                   $total_items=0;
                                      foreach($customer_products as $key=>$data){
                                        ?>
                                        <div class="row form-group">
                                          <div class="offset-md-1">&nbsp;</div>
                                          <div class="col-md-10 p-1 rounded pt-2 text-center card-header alert-secondary ">
                                            <label>{{$data['name']}}</label>
                                          </div>
                                        </div>
                                        <?php 
                                        $total_items=$total_items+count($data['products']);
                                        foreach($data['products'] as $k=>$proData){ ?>
                                        <div id="item_row{{$i}}_{{$proData['id']}}">
                                         <div class="row form-group">
                                          <div class="offset-md-3">&nbsp;</div>
                                          <div class="col-md-4">
                                              <div class="form-group clearfix">
                                                  <div class="icheck-primary d-inline">
                                                      <input type="hidden" name="product_details{{$i}}[{{$proData['id']}}][cat_id][]" value="{{$data['id']}}">
                                                      <input type="hidden" value="{{$proData['id']}}" name="product_details{{$i}}[{{$proData['id']}}][product_id][]">
                                                      <input type="checkbox" value="{{$proData['name']}}" name="product_details{{$i}}[{{$proData['id']}}][product_name][]" id="{{$proData['slug']}}_{{$proData['id']}}">
                                                      <label for="{{$proData['slug']}}_{{$proData['id']}}">
                                                          {{$proData['name']}}
                                                      </label>
                                                  </div>
                                              </div>
  
                                          </div>
                                          <div class="col-md-1">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Quantity" value="1" type="number" name="product_details{{$i}}[{{$proData['id']}}][item_quantity][]" class=" form-control">
                                              </div>
                                          </div>
                                          <div class="col-md-1">
                                              <div class="input-group mb-3">
                                                  <select name="product_details{{$i}}[{{$proData['id']}}][product_sizes][]"  class="form-control">@php echo get_product_sizes($proData['sizes']); @endphp</select>
                                              </div>
                                          </div>
                                          <div class="col-md-3">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Description" type="text" name="product_details{{$i}}[{{$proData['id']}}][item_description][]"  class=" form-control @error('delivery_type') is-invalid @enderror">
                                              </div>
                                          </div>
                                          <div class="col-md-1"><div style="width: 90px; float:right;" onclick="addmore_items{{$i}}({{$proData['id']}},'{{$data['slug']}}')"
                                            class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add
                                            more</div></div>
                                      </div>
                                    </div>
                                      <div id="duplicate_row{{$i}}_{{$proData['id']}}"></div>
                                      <?php }
                                }
                                ?>
                                                        
                               

                                   </div>
                                   @endfor
{{-- END --}}
                                    <div id="add_new_pickup_btn" class="row form-group">
                                        <div class="offset-md-4">&nbsp;</div>
                                        <div class="col-md-4 mt-3">
                                            <span onclick="add_new_pickup()" class="btn btn-outline-success btn-block btn-lg"><i class="fa fa-plus"></i> Add Stop</span>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-5">
                                            <label>Drop off Address</label>
                                            <div class="input-group mb-3">
                                                <input required placeholder="Drop off address" type="text" id="drop_off_street_address" name="drop_off_street_address" value="{{ old('drop_off_street_address') }}"
                                                    class=" form-control @error('drop_off_street_address') is-invalid @enderror">
                                                @error('drop_off_street_address')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-md-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input placeholder="Unit/STE" type="text" name="drop_off_unit" value="{{ old('drop_off_unit') }}"
                                                    class=" form-control @error('drop_off_unit') is-invalid @enderror">
                                                @error('drop_off_unit')
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
                                            <div class="input-group mb-3">
                                                <input  placeholder="Zip Code" type="text" name="drop_off_zipcode" value="{{ old('drop_off_zipcode') }}"
                                                class=" form-control @error('drop_off_zipcode') is-invalid @enderror">
                                                <div id="otherzipcode2"></div>
                                                @error('drop_off_zipcode')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input  placeholder="Contact No.(e.g +18633335555)" value="{{ old('drop_off_contact_number') }}" type="text" name="drop_off_contact_number"
                                                    class=" form-control @error('drop_off_contact_number') is-invalid @enderror">
                                                @error('drop_off_contact_number')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        {{-- <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input  placeholder="Email" value="{{ old('drop_off_email') }}" type="text" name="drop_off_email"
                                                    class=" form-control @error('drop_off_email') is-invalid @enderror">
                                                @error('drop_off_email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-10">
                                            <div class="input-group mb-3">
                                                <textarea placeholder="Special pickup or delivery instructions "  name="drop_off_instructions"
                                                    class=" form-control @error('drop_off_instructions') is-invalid @enderror">{{ old('drop_off_instructions') }}</textarea>
                                                @error('drop_off_instructions')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-2">
                                            <span class="col-form-label">Select Delivery Date</span>
                                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                <input required type="text" value="{{ old('drop_off_date') }}" name="drop_off_date" placeholder="Delivery date" class="form-control datetimepicker-input" data-target="#reservationdate"/>
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
                                        <div class="col-md-2 align-center text-center">
                                            <span class="col-form-label ">Assign to Driver/Sub</span>
                                            <div class="form-group clearfix mt-1">
                                                <div class="icheck-primary d-inline ml-1">
                                                  <input onclick="$('#drivers_options').show();$('#subs_options').hide();" type="radio" value="1" id="radioPrimary1" name="assign_to" checked>
                                                  <label for="radioPrimary1"> Driver</label>
                                                </div>
                                                <div class="icheck-primary d-inline ml-3">
                                                  <input  onclick="$('#subs_options').show();$('#drivers_options').hide();" type="radio" value="2" id="radioPrimary2" name="assign_to">
                                                  <label for="radioPrimary2"> Sub</label>
                                                </div>
                                                
                                              </div>

                                        </div>
                                        <div id="drivers_options" class="col-md-4">
                                            <span class="col-form-label">Select Driver</span>
                                            <div class="input-group mb-3" >
                                                <select placeholder="select Driver" name="driver_id" class="select2bs4 form-control">
                                                    {!!get_drivers_options()!!}
                                                </select>
                                                @error('driver_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="subs_options" class="col-md-6" style="display: none">
                                            <span class="col-form-label">Select Subs</span>
                                            <div class="input-group mb-3" >
                                                <select placeholder="select Sub" name="sub_id" class="select2bs4 form-control">
                                                    {!!get_subs_options()!!}
                                                </select>
                                                &nbsp;<input type="number" name="quoted_price_for_sub" required
                                                value="{{ old('quoted_price_for_sub') }}"
                                                placeholder="Price for Sub in USD" class="form-control">
                                                @error('quoted_price_for_sub')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="offset-md-1">&nbsp;</div>
                                        <div class="col-md-3">
                                            <label>Delivery Cost</label>
                                            <input type="number" name="quoted_price" required
                                                value="{{ old('quoted_price') }}"
                                                placeholder="Total Cost in USD" class="form-control @error('quoted_price') is-invalid @enderror"">
                                            @error('quoted_price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label>Extra Charges
                                            </label>
                                            <div class="input-group mb-2"><input type="number"
                                                    name="extra_charges"
                                                    value="{{ old('extra_charges') }}"
                                                    placeholder="Any Extra Charges"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Reason for Extra Charge</label>
                                            <div class="input-group mb-2"><input type="text"
                                                    name="reason_for_extra_charges"
                                                    value="{{ old('reason_for_extra_charges') }}"
                                                    placeholder="Reason for extra Charge"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                   
                                    <div class="row form-group">
                                        <div class="offset-md-4">&nbsp;</div>
                                        <div class="col-md-4">
                                            <button type="submit" onclick="$('#add_delivery_form').submit()" class="btn btn-outline-success btn-block btn-lg"><i class="fa fa-save"></i> Save</button>
                                        </div>
                                        

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

            let _counter=2;
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
                    
                    multi_unit_html ='<div class="row form-group"><div class="offset-md-3"></div><div class="col-md-5"><div class="input-group mb-2"><div class="form-group clearfix"><label>Is there an elevator? </label>&nbsp;<div class="icheck-primary d-inline"><input type="radio" id="elevator1" value="1" name="elevator" checked><label for="elevator1">Yes </label></div> &nbsp;<div class="icheck-primary d-inline"><input type="radio" value="0" id="elevator2" name="elevator"><label for="elevator2">No</label></div></div></div></div></div>';
                    multi_unit_html +='<div class="row form-group"><div class="offset-md-3">&nbsp;</div><div class="col-md-6"><label>How Many Appartments?</label><div class="input-group mb-2"><input type="number" name="no_of_appartments" value="0" class="form-control"></div></div></div>';
                    multi_unit_html +='<div class="row form-group"><div class="offset-md-3">&nbsp;</div><div class="col-md-6"><div id="listof_floors"><label>List All Floors</label><div class="input-group mb-2"><input type="text" name="list_of_floors[]" placeholder="Floor?" class="form-control"></div></div><div style="width: 90px; float:right;" onclick="addmore_floors()" class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add more</div></div> </div>';
                    
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
                '" class="input-group mb-2"><input type="text" name="list_of_floors[]" placeholder="Floor?" class="form-control">' +
                removeBtn + '</div>';
            $('#listof_floors').append(listof_floors);
        }

        // Change City function
        function changeCity1() {
            selectOption = $('#city1 option:selected').text();

            if (selectOption == 'Other') {
                otherCity =
                    '<input  type="text" name="other_pickup_city_id" class="form-control" placeholder="Please enter City" required>';
                $('#othercity1').html(otherCity);
            } else {
                $('#othercity1').html('');
            }
        }; // End of Change City Function

        // Change Zip Code function
        function changezipcode1() {
            selectOption = $('#zipcode1 option:selected').text();

            if (selectOption == 'Other') {
                otherZipCode =
                    '<input  type="text" name="other_pickup_zipcode_id" class="form-control" placeholder="Please enter Zip Code" required>';
                $('#otherzipcode1').html(otherZipCode);
            } else {
                $('#otherzipcode1').html('');
            }
        }; // End of Zip Code Function
        function changestates1() {
            selectOption = $('#states1 option:selected').text();

            if (selectOption == 'Other') {
                otherstates =
                    '<input  type="text" name="other_pickup_state_id" class="form-control" placeholder="State Name*" required>';
                $('#otherstates1').html(otherstates);
            } else {
                $('#otherstates1').html('');
            }
        }; // End of Zip Code Function
        // Change City function
        function changeCity2() {
            selectOption = $('#city2 option:selected').text();

            if (selectOption == 'Other') {
                otherCity =
                    '<input  type="text" name="other_drop_off_city_id" class="form-control" placeholder="Please enter City" required>';
                $('#othercity2').html(otherCity);
            } else {
                $('#othercity2').html('');
            }
        }; // End of Change City Function

        // Change Zip Code function
        function changezipcode2() {
            selectOption = $('#zipcode2 option:selected').text();

            if (selectOption == 'Other') {
                otherZipCode =
                    '<input  type="text" name="other_drop_off_zipcode_id" class="form-control" placeholder="Please enter Zip Code" required>';
                $('#otherzipcode2').html(otherZipCode);
            } else {
                $('#otherzipcode2').html('');
            }
        }; // End of Zip Code Function
        function changestates2() {
            selectOption = $('#states2 option:selected').text();

            if (selectOption == 'Other') {
                otherstates =
                    '<input  type="text" name="other_drop_off_state_id" class="form-control" placeholder="State Name*" required>';
                $('#otherstates2').html(otherstates);
            } else {
                $('#otherstates2').html('');
            }
        }; // End of Zip Code Function
    </script>
@endsection
