@extends('adminpanel.admintemplate')
@push('title')
    <title>Edit quote | {{ config('constants.app_name')}}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit quote</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                            <li class="breadcrumb-item active">Edit quote</li>
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
                                <h3 class="card-title">Edit quote</h3>
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
                                <form method="POST" action="{{ route('quotes.save_quote_edit',$id)}}">
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

                                    {{-- End --}}
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <label>Pickup Address</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="Pickup street address" type="text"
                                                    name="pickup_street_address" required value="{{ $quotesData['pickup_street_address'] }}"
                                                    class=" form-control @error('pickup_street_address') is-invalid @enderror">
                                                @error('pickup_street_address')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input required value="{{ $quotesData['pickup_unit'] }}" placeholder="Unit/STE"  type="text" name="pickup_unit"
                                                    class=" form-control @error('pickup_unit') is-invalid @enderror">
                                                @error('pickup_unit')
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
                                            <div class="input-group mb-3">
                                                <select id="states1" onChange="changestates1()" name="pickup_state_id"
                                                    class="form-control select2bs4"
                                                    placeholder="Select State">@php echo getStatesOptions($quotesData['pickup_state_id']); @endphp</select>
                                                <div id="otherstates1"></div>
                                                @error('pickup_state_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <select id="city1" onChange="changeCity1()" name="pickup_city_id"
                                                    class="form-control select2bs4 @error('city1') is-invalid @enderror"
                                                    placeholder="Select City">@php echo getCitiesOptions($quotesData['pickup_city_id']); @endphp</select>
                                                 <div id="othercity1"></div>
                                                @error('pickup_city_id')
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
                                            <div class="input-group mb-3">
                                                <select id="zipcode1" onChange="changezipcode1()" name="pickup_zipcode_id"
                                                    class="form-control select2bs4"
                                                    placeholder="Select Zip COde">@php echo getZipCodeOptions($quotesData['pickup_zipcode_id']); @endphp</select>
                                                <div id="otherzipcode1"></div>
                                                @error('pickup_zipcode_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Contact Number" type="text" name="pickup_contact_number" required value="{{ $quotesData['pickup_contact_number']}}"
                                                    class=" form-control @error('pickup_contact_number') is-invalid @enderror">
                                                @error('pickup_contact_number')
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
                                            <div class="input-group date" id="pick_up_reservationdate" data-target-input="nearest">
                                                <input type="text" value="{{ $quotesData['pickup_date']}}" required name="pickup_date" placeholder="Pick Up date" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                                                <div class="input-group-append" data-target="#pick_up_reservationdate" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3">
                                            <div class="input-group mb-2">
                                                <div class="form-group clearfix">
                                                    <label>Pick Up at :  </label>&nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input type="radio" id="pickup_at_time1" value="1" name="pickup_at_time" {{$quotesData['pickup_at_time']==1?'checked':''}} >
                                                        <label for="pickup_at_time1">AM </label>
                                                    </div> &nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input type="radio" id="pickup_at_time2" value="2" name="pickup_at_time" {{$quotesData['pickup_at_time']==2?'checked':''}}>
                                                        <label for="pickup_at_time2">PM</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <label>Drop off Address</label>
                                            <div class="input-group mb-3">
                                                <input required placeholder="Drop off address" type="text" name="drop_off_street_address" value="{{ $quotesData['drop_off_street_address']}}"
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
                                                <input placeholder="Unit/STE" type="text" name="drop_off_unit" value="{{ $quotesData['drop_off_unit']}}"
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
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <select id="states2" onChange="changestates2()" name="drop_off_state_id"
                                                    class="form-control select2bs4"
                                                    placeholder="Select State">@php echo getStatesOptions($quotesData['drop_off_state_id']); @endphp</select>
                                                <div id="otherstates2"></div>
                                                @error('drop_off_state_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <select id="city2" onChange="changeCity2()" name="drop_off_city_id"
                                                    class="form-control select2bs4 @error('drop_off_city_id') is-invalid @enderror"
                                                    placeholder="Select City">@php echo getCitiesOptions($quotesData['drop_off_city_id']); @endphp</select>
                                                <div id="othercity2"></div>
                                                @error('drop_off_city_id')
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
                                            <div class="input-group mb-3">
                                                <select id="zipcode2" onChange="changezipcode2()" name="drop_off_zipcode_id"
                                                    class="form-control select2bs4"
                                                    placeholder="Select Zip COde">@php echo getZipCodeOptions($quotesData['drop_off_zipcode_id']); @endphp</select>
                                                <div id="otherzipcode2"></div>
                                                @error('drop_off_zipcode_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input required placeholder="Contact Number" value="{{ $quotesData['drop_off_contact_number']}}" type="text" name="drop_off_contact_number"
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
                                                    class=" form-control @error('drop_off_instructions') is-invalid @enderror">{{ $quotesData['drop_off_instructions']}}</textarea>
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
                                                <input required type="text" value="{{ $quotesData['drop_off_date']}}" name="drop_off_date" placeholder="Delivery date" class="form-control datetimepicker-input" data-target="#reservationdate"/>
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
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-3">
                                            <div class="input-group mb-2">
                                                <div class="form-group clearfix">
                                                    <label>Drop Of at :  </label>&nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input type="radio" id="drop_off_at_time1" value="1" name="drop_off_at_time" {{$quotesData['drop_off_at_time']==1?'checked':''}}>
                                                        <label for="drop_off_at_time1">AM </label>
                                                    </div> &nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input type="radio" id="drop_off_at_time2" value="2" name="drop_off_at_time" {{$quotesData['drop_off_at_time']==2?'checked':''}}>
                                                        <label for="drop_off_at_time2">PM</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    {{-- List of items --}}
                                   <?php
                                   $total_items=0;
                                   $collection = collect($quotesData['quote_products']);
                                //    $result = $collection->where('product_id',3); 
                                //    if(count($result)==0)
                                //    echo 'Not Found';
                                //    else {
                                //     echo 'Found';
                                //    }
                                //    foreach ($result as $key => $data) {
                                //     echo 'pname:'.$data['product_name'];
                                //    }
                                //    p($result);
                                   
                                      foreach($products as $key=>$data){
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

                                            $result = $collection->where('product_id',$proData['id']); 
                                            ?>
                                            @if (count($result)>0)
                                            
                                            @foreach ($result as $key=>$selected_product )
                                            <div class="row form-group">
                                                <div class="col-1">&nbsp;</div>
                                                <div class="col-4">
                                                    <div class="form-group clearfix">
                                                        <div class="icheck-primary d-inline">
                                                            <input type="hidden" name="product_details[{{$proData['id']}}][cat_id][]" value="{{$selected_product['cat_id']}}">
                                                            <input type="hidden" value="{{$selected_product['product_id']}}" name="product_details[{{$proData['id']}}][product_id][]">
                                                            <input checked type="checkbox" value="{{$selected_product['product_name']}}" name="product_details[{{$proData['id']}}][product_name][]" id="{{$proData['slug']}}_{{$proData['id']}}">
                                                            <label for="{{$proData['slug']}}_{{$proData['id']}}">
                                                                {{$selected_product['product_name']}}
                                                            </label>
                                                        </div>
                                                    </div>
        
                                                </div>
                                                <div class="col-1">
                                                    <div class="input-group mb-3">
                                                        <input placeholder="Quantity" value="{{$selected_product['quantity']}}" type="number" name="product_details[{{$proData['id']}}][item_quantity][]" class=" form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-1">
                                                    <div class="input-group mb-3">
                                                        <input placeholder="Size" type="number" value="{{$selected_product['size']}}"  name="product_details[{{$proData['id']}}][item_size][]" class=" form-control">
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-1">
                                                    <div class="input-group mb-3">
                                                        <select name="product_details[{{$proData['id']}}][item_size_unit][]"  class="form-control select2bs4">@php echo getItemSizeUnitsOptions($selected_product['size_unit']); @endphp</select>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="input-group mb-3">
                                                        <input placeholder="Description" type="text" value="{{$selected_product['description']}}"  name="product_details[{{$proData['id']}}][item_description][]"  class=" form-control @error('delivery_type') is-invalid @enderror">
                                                    </div>
                                                </div>
                                                <div class="col-1">&nbsp;</div>
                                            </div>
                                            @endforeach



                                            @else
                                            
                                         <div class="row form-group">
                                          <div class="col-1">&nbsp;</div>
                                          <div class="col-4">
                                              <div class="form-group clearfix">
                                                  <div class="icheck-primary d-inline">
                                                      <input type="hidden" name="product_details[{{$proData['id']}}][cat_id][]" value="{{$data['id']}}">
                                                      <input type="hidden" value="{{$proData['id']}}" name="product_details[{{$proData['id']}}][product_id][]">
                                                      <input type="checkbox" value="{{$proData['name']}}" name="product_details[{{$proData['id']}}][product_name][]" id="{{$proData['slug']}}_{{$proData['id']}}">
                                                      <label for="{{$proData['slug']}}_{{$proData['id']}}">
                                                          {{$proData['name']}}
                                                      </label>
                                                  </div>
                                              </div>
  
                                          </div>
                                          <div class="col-1">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Quantity" value="1" type="number" name="product_details[{{$proData['id']}}][item_quantity][]" class=" form-control" required>
                                              </div>
                                          </div>
                                          <div class="col-1">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Size" type="number" name="product_details[{{$proData['id']}}][item_size][]" class=" form-control">
                                                  
                                              </div>
                                          </div>
                                          <div class="col-1">
                                              <div class="input-group mb-3">
                                                  <select name="product_details[{{$proData['id']}}][item_size_unit][]"  class="form-control select2bs4">@php echo getItemSizeUnitsOptions(); @endphp</select>
                                              </div>
                                          </div>
                                          <div class="col-3">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Description" type="text" name="product_details[{{$proData['id']}}][item_description][]"  class=" form-control @error('delivery_type') is-invalid @enderror">
                                              </div>
                                          </div>
                                          <div class="col-1">&nbsp;</div>
                                      </div>
                                      @endif

                                      <?php }?>
                                      <div id="{{$proData['slug']}}"></div>
                                      <div class="row form-group">
                                          <div class="col-11">&nbsp;</div>
                                          <div class="col-1">
                                              <div style="width: 90px; float:right;" onclick="addmore_items({{$proData['id']}},'{{$proData['slug']}}')"
                                                  class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add
                                                  more</div>
                                          </div>
                                      </div>
                                   @php
                                      }
                                    @endphp
                                   
                                   
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
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js')}}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('adminpanel/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        var ctr = 1;
        var counter = {{$total_items}};
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
              //Date picker
            $('#pick_up_reservationdate').datetimepicker({
                format: 'L'
            });
            $('#reservationdate').datetimepicker({
                format: 'L'
            });
        });
            // Select Quote Type Single/Multi
            function select_quote_type(is_type){
                $('#single').removeClass('active');
                $('#multi').removeClass('active');
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
                    $('#quote_type').val('multi');
                }
                
            }
            // Add more Items manually
        function addmore_items(cat_id, cat_slug) {
            counter++;
            itemHTML = '<div class="row form-group"><div class="col-1">&nbsp;</div>';
            itemHTML += '<input value="'+cat_id+'" placeholder="Category ID" type="hidden" name="product_details['+counter+'][cat_id][]" required  class=" form-control" >';
            itemHTML +=
                '<div class="col-4"><div class="input-group mb-3"><input placeholder="Item Name" type="text" name="product_details['+counter+'][product_name][]" required  class=" form-control" ></div></div>';
            itemHTML +=
                '<div class="col-1"><div class="input-group mb-3"><input placeholder="Quantity" type="number" value=1 name="product_details['+counter+'][item_quantity][]" required  class=" form-control" ></div></div>';
            itemHTML +=
                '<div class="col-1"><div class="input-group mb-3"><input placeholder="Size" type="number" name="product_details['+counter+'][item_size][]"  class=" form-control" ></div></div>';
            itemHTML +=
                '<div class="col-1"><div class="input-group mb-3"><select name="product_details['+counter+'][item_size_unit][]" class="form-control select2HTML">@php echo getItemSizeUnitsOptions(); @endphp</select></div></div>';
            itemHTML +=
                '<div class="col-3"><div class="input-group mb-3"><input placeholder="Description" type="text" name="product_details['+counter+'][item_description][]"  class=" form-control" ></div></div>';
            itemHTML +=
                '<div class="col-1"><div style="width:20px; cursor:pointer; padding:10px; color:red;"><i onclick=$("#manual_item_' +
                counter + '").remove() class="fas fa-minus"></i></div></div></div>';
                
            $('#' + cat_slug).append('<div id="manual_item_' + counter + '">' + itemHTML + '</div>');
            
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
