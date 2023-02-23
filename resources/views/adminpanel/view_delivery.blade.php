@extends('adminpanel.admintemplate')
@push('title')
    <title>View Delivery | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View Delivery</h1>
                    </div>
                    {{-- <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">View Delivery</li>
                        </ol>
                    </div> --}}
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
                                <h3 class="card-title">View Delivery</h3>
                            </div>
                            <div class="card-body">


                                <!-- /.row -->

                                <div class="row form-group">
                                    <div class="col-12">
                                        <div class="alert alert-info alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">&times;</button>
                                            <h5><i class="icon fa fa-user"></i>
                                                Delivery status!</h5>
                                            {{ quote_status_msg($quotesData['status']) }}

                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Delivery Information</strong>
                                                <div style="width: 120px; float:right;">
                                                @if ($user->group_id==config('constants.groups.admin'))
                                                <a href="{{route('delivery.editform',$id) }}"
                                                class="btn btn-info btn-flat btn-sm"><i class="fas fa-edit"></i>
                                                Edit Delivery</a>  
                                                @endif
                                                
                                                </div>
                                            </div><!-- /.card-header -->
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
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div>
                                                        <div class="row">
                                                            <div class="col-3">&nbsp;</div>
                                                            <div class="col-6">

                                                                <!-- flash-message -->
                                                                <div class="flash-message bg-danger">
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
                                                                                <td>{{ $quote_product['product_name'] }}
                                                                                </td>
                                                                                <td>{{ $quote_product['quantity'] }}</td>
                                                                                <td>{{ $quote_product['size'] }}</td>
                                                                                <td>{{ $quote_product['description'] }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        @if (isset($quotesData['driver']) && empty($quotesData['driver']))
                                                            <div style="height: 100px; width:100%">&nbsp;</div>
                                                            <form
                                                                action="{{ route('quotes.add_to_delivery_save', $quotesData['id']) }}"
                                                                method="post">
                                                                @csrf
                                                                <input type="hidden" name="po_number"
                                                                    value="{{ $quotesData['po_number'] }}">
                                                                <div class="row form-group">
                                                                    <div class="col-6">
                                                                        <label class="col-form-label">Select Driver</label>
                                                                        <div class="input-group mb-3">
                                                                            <select placeholder="select Driver"
                                                                                name="driver_id"
                                                                                class="select2bs4 form-control @error('drivers') is-invalid @enderror">
                                                                                {!! get_drivers_options() !!}
                                                                            </select>
                                                                            @error('driver_id')
                                                                                <div class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2" style="margin-top: 2rem">&nbsp;
                                                                    </div>
                                                                    <div class="col-4" style="margin-top: 2rem">
                                                                        <button type="submit"
                                                                            class=" float-right btn btn-success btn-block btn-lg"><i
                                                                                class="fa fa-plus"></i> Add to
                                                                            Delivery</button>
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
                                        <!-- /.card -->
                                        
                                            @if ($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.customer'))
                                            <div class="card">
                                            <?php if($quotesData['quote_prices']){  $k=1;?>
                                                <div class="card-header p-2">
                                                    <strong> Delivery Cost   </strong>
                                                </div><!-- /.card-header -->
                                            
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <div class="table-responsive">
                                                            <form id="update_delivery_price" method="post">
                                                                @csrf
                                                                <input type="hidden" name="quote_id" value="{{$quotesData['id']}}">
                                                                <input type="hidden" name="action" value="update_delivery_price">
                                                                
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
                                                                @php
                                                                $delivery_cost=0;
                                                                  if ($data['status'] == 1){
                                                                    $delivery_cost=$data['quoted_price']+$data['extra_charges'];
                                                                  }  
                                                                @endphp
                                                                
                                                                @if ($data['status'] == 1 && $user->group_id==config('constants.groups.admin'))
                                                                <input type="hidden" name="invoice_id" value="{{$data['id']}}">
                                                                
                                                                <tr>
                                                                    <td><input type="number" name="quoted_price" value="{{$data['quoted_price']}}"> </td>
                                                                    <td><input type="number" name="extra_charges" value="{{$data['extra_charges'] != '' ? $data['extra_charges'] : 0 }}">
                                                                    </td>
                                                                    <td colspan="2"><input style="width:100%;" type="text" name="reason_for_extra_charges" value="{{ $data['reason_for_extra_charges'] }}"></td>
                                                                    <td colspan="2"><textarea style="width:100%;" name="description">{{ $data['description'] }}</textarea></td>
                                                                    
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2">&nbsp;</td>
                                                                    <td colspan="2"><div onclick=" do_action({{ $quotesData['id'] }},'update_delivery_price')" class="btn btn-primary btn-block btn-sm"><i class="fas fa-save"></i> Save Changes</div></td>
                                                                    <td colspan="2">&nbsp;</td>
                                                                </tr>
                                                                @endif

                                                                <?php }?>
                                                            </table>
                                                            </form>
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
                                            @endif
                                            
                                        @if ($user->group_id == config('constants.groups.admin'))
                                        
                                        @if ($quotesData['status']>0)
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Payment Received </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div class="form-group row">
                                                        <div class="col-5">&nbsp;</div>
                                                        <div class="col-sm-4"><a href="{{route('send.customer.invoice',$id)}}"  class="btn btn-info btn-block btn-flat"><i class="fa fa-upload"></i> Send invoice to Customer</a></div>
                                                        <div class="col-sm-3"><a href="{{route('download.invoice',$id)}}" class="btn btn-info btn-block btn-flat"><i class="fa fa-download"></i> Download invoice</a></div>
                                                    </div>
                                                    
                                                    <?php $recievedAmount=0; if($quotesData['invoices'] && count($quotesData['invoices'])>0){  $k=1;?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-12">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <tr>
                                                                            <th>Sr.No.</th>
                                                                            <th>Payee Name</th>
                                                                            <th>Paid Amount</th>
                                                                            <th>Description</th>
                                                                            <th>Paid date</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                        <?php foreach ($quotesData['invoices'] as $key=>$invoice){ 
                                                                            $recievedAmount=$recievedAmount+$invoice['paid_amount'];?>
                
                                                                        <tr>
                                                                            <td>{{ $k++}}</td>
                                                                            <td>{{ $invoice['payee_name']}}</td>
                                                                            <td>{{ $invoice['paid_amount']}}</td>
                                                                            <td>{{ $invoice['description']}}</td>
                                                                            <td>{{ date(config('constants.date_formate'),strtotime($invoice['created_at'])) }}</td>
                                                                            <td style="width:200px">
                                                                                <span style="width:200px"  class=" btn-success btn-sm">Received</span>
                                                                            </td>
                                                                            <div </tr>
                
                                                                                <?php }?>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <?php }?>
                                                        @if ($errors->any())
                                                        <div class="form-group row">
                                                            <div class="col-3">&nbsp;</div>
                                                        <div class=" col-6 flash-message bg-danger">
                                                            
                                                                {!! implode('', $errors->all('<div>:message</div>')) !!}
                                                           
                                                        </div>
                                                        </div>
                                                        @endif

                                                    <form action="{{route('delivery.add_invoice',$id)}}" method="POST">
                                                        @csrf
                                                        
                                                        <div class="form-group row">
                                                            
                                                            <div class="col-sm-3">
                                                                <label>Payee Name
                                                                </label>
                                                                <div class="input-group mb-2"><input type="text"
                                                                        name="payee_name" value="{{ old('payee_name') }}"
                                                                        placeholder="Name of Payee" class="form-control">
                                                                </div>
                                                            </div>
                                                            {{-- <div class="col-sm-3">
                                                                <label>Payee Phone
                                                                </label>
                                                                <div class="input-group mb-2"><input type="text"
                                                                        name="payee_phone" value="{{ old('payee_phone') }}"
                                                                        placeholder="Phone of Payee" class="form-control">
                                                                </div>
                                                            </div> --}}
                                                            <div class="col-sm-2">
                                                                <label>Amout</label>
                                                                <div class="input-group mb-2"><input type="number"
                                                                        name="paid_amount"
                                                                        value="{{ old('paid_amount') }}"
                                                                        placeholder="Amount" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-7">
                                                                <label>Description</label>
                                                                <div class="input-group mb-2"><textarea
                                                                        name="description"
                                                                        value="{{ old('description') }}"
                                                                        placeholder="Description about Payment" class="form-control"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-4">&nbsp;</div>
                                                            <div class="col-4">
                                                                <button type="submit"
                                                                    class="btn btn-outline-success btn-block btn-lg"><i
                                                                        class="fa fa-save"></i> Add Payment</button>
                                                            </div>
                                                            <div class="col-4">&nbsp;</div>

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                            <div class="card">
                                                <div class="card-header p-2">
                                                    <strong> Delivery Documents (Only for Office) </strong>
                                                </div><!-- /.card-header -->
                                                <div class="row form-group">
                                                    <div class="col-1">&nbsp;</div>
                                                    <div class="col-10">
                                                        <div class="row form-group">
                                                            <?php
                                                     $imagesTypes=array('jpg','jpeg','png','gif');
                                                     $excelTypes=array('xls','xlsx');
                                                     $docTypes=array('doc','docx');
                                                        foreach($quotesData['document_for_delivery'] as $data){
                                                          if(in_array($data['otherinfo'],$imagesTypes))
                                                            $thumb_img=$data['path'];
                                                          else if(in_array($data['otherinfo'],$excelTypes))
                                                            $thumb_img=url('adminpanel/dist/img/xls.jpeg');
                                                          else if(in_array($data['otherinfo'],$docTypes))
                                                            $thumb_img=url('adminpanel/dist/img/doxx.png');
                                                          else if($data['otherinfo']=='pdf')
                                                          $thumb_img=url('adminpanel/dist/img/pdf.png');
                                                            ?>
                                                            <div id="file_{{ $data['id'] }}" class="col-3 text-center"
                                                                style="position: relative;">
                                                                <label class="">{{ $data['name'] }}</label>
                                                                <i onclick="removeFile({{ $data['id'] }})"
                                                                    style="position: absolute; top:15px; right:0px; cursor:pointer"
                                                                    class="fas fa-times"></i>
                                                                <a href="{{ $data['path'] }}" target="_blank"><img
                                                                        class="w-100 shadow-1-strong rounded mb-4 img-thumbnail"
                                                                        src="{{ isset($thumb_img)?$thumb_img:'' }}" width="200" alt="Uploaded Image"></a>
                                                            </div>
                
                
                                                            <?php 
                                                          }
                                                      ?>
                                                        </div>
                                                        <div class="col-1">&nbsp;</div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-1">&nbsp;</div>
                                                    <div class="col-10 card card-default">
                                                      
                                                        <form action="{{ route('delivery.uploade_document_for_driver',$id) }}"
                                                            method="post" enctype="multipart/form-data" id="image-upload"
                                                            class="dropzone ">
                                                            <input type="hidden" name="documents_for" value="document_for_delivery"> 
                                                            @csrf
                                                            <div>
                                                                <h4 class="form-label">Upload Multiple Files By Click On
                                                                    Box</h4>
                                                            </div>
                                                        </form>
                                                        <div class="card-footer">
                                                            You can select multiple files (e.g images, .docx , .xls ,.csv,
                                                            .pdf ) and upload

                                                        </div>
                                                    </div>
                                                    <div class="col-3">&nbsp;</div>

                                                </div>
                                            </div>
                                        
                                            <div class="card">
                                                <div class="card-header p-2">
                                                    <strong> Notes Section for Delivery (Only Admin)</strong>
                                                </div><!-- /.card-header -->
                                                <div class="card-body">
                                                    <div id="submit_comment_crm_replace">
                                                        @php
                                                            // p($quotesData['comments']);
                                                        @endphp
                                                        @foreach ($quotesData['delivery_notes'] as $key => $comment)
                                                            <div class="row border">
                                                                <div class="col-12">
                                                                    <strong>{{ $comment['user']['name'] }}</strong>({{ $comment['slug'] }})
                                                                    {{ date('d/m/Y H:i:s', strtotime($comment['created_at'])) }}<br>
                                                                    {{ $comment['comment'] }}
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                    </div>
                                                    @php
                                                        $userData = get_session_value();
                                                        //p($userData);
                                                    @endphp
                                                    <div class="tab-content">
                                                        <form method="post" id="submit_comment_crm">
                                                            <input type="hidden" name="group_id"
                                                                value="{{ $user->group_id }}">
                                                            <input type="hidden" name="action" value="submit_comment_crm">
                                                            <input type="hidden" name="comment_section" value="delivery_notes_only">
                                                            <input type="hidden" name="slug"
                                                                value="{{ $userData['get_groups']['slug'] }}">
                                                            <input type="hidden" name="user_name"
                                                                value="{{ $userData['name'] }}">
                                                            <div class="form-group">
                                                                <label for="inputDescription">Comment</label>
                                                                <textarea id="comments_crm" name="comment" placeholder="Write comment about the quote" class="form-control"
                                                                    rows="4"></textarea></br>
                                                                <button
                                                                    onclick="do_action({{ $quotesData['id'] }},'submit_comment_crm')"
                                                                    type="button" class="btn btn-success float-right"><i
                                                                        class="far fa-credit-card"></i> Send</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- This section is for upload proof of delivery --}}
                                        @if ($user->group_id == config('constants.groups.customer'))
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Proof of Delivery </strong>
                                            </div><!-- /.card-header -->
                                            <div class="row form-group">
                                                <div class="col-1">&nbsp;</div>
                                                <div class="col-10">
                                                    <div class="row form-group">
                                                        <?php
                                                 $imagesTypes=array('jpg','jpeg','png','gif');
                                                 $excelTypes=array('xls','xlsx');
                                                 $docTypes=array('doc','docx');
                                                 if(count($quotesData['delivery_proof'])>0){
                                                    foreach($quotesData['delivery_proof'] as $data){
                                                      if(in_array($data['otherinfo'],$imagesTypes))
                                                        $thumb_img=$data['path'];
                                                      else if(in_array($data['otherinfo'],$excelTypes))
                                                        $thumb_img=url('adminpanel/dist/img/xls.jpeg');
                                                      else if(in_array($data['otherinfo'],$docTypes))
                                                        $thumb_img=url('adminpanel/dist/img/doxx.png');
                                                      else if($data['otherinfo']=='pdf')
                                                      $thumb_img=url('adminpanel/dist/img/pdf.png');
                                                        ?>
                                                        <div id="file_{{ $data['id'] }}" class="col-3 text-center"
                                                            style="position: relative;">
                                                            <label class="">{{ $data['name'] }}</label>
                                                            <a href="{{ $data['path'] }}" target="_blank"><img
                                                                    class="w-100 shadow-1-strong rounded mb-4 img-thumbnail"
                                                                    src="{{ isset($thumb_img)?$thumb_img:'' }}" width="200" alt="Uploaded Image"></a>
                                                        </div>
            
            
                                                        <?php 
                                                      }
                                                    }
                                                    else{
                                                        echo '<div class="col-12 text-center">No Proof uploaded Yet</div>';
                                                    }
                                                  ?>
            
                                                </div>
                                                    <div class="col-1">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($user->group_id == config('constants.groups.admin') || $user->group_id == config('constants.groups.driver'))
                                            <div class="card">
                                                <div class="card-header p-2">
                                                    <strong> Proof of Delivery </strong>
                                                </div><!-- /.card-header -->
                                                <div class="row form-group">
                                                    <div class="col-1">&nbsp;</div>
                                                    <div class="col-10">
                                                        <div class="row form-group">
                                                            <?php
                                                     $imagesTypes=array('jpg','jpeg','png','gif');
                                                     $excelTypes=array('xls','xlsx');
                                                     $docTypes=array('doc','docx');
                                                        foreach($quotesData['delivery_proof'] as $data){
                                                          if(in_array($data['otherinfo'],$imagesTypes))
                                                            $thumb_img=$data['path'];
                                                          else if(in_array($data['otherinfo'],$excelTypes))
                                                            $thumb_img=url('adminpanel/dist/img/xls.jpeg');
                                                          else if(in_array($data['otherinfo'],$docTypes))
                                                            $thumb_img=url('adminpanel/dist/img/doxx.png');
                                                          else if($data['otherinfo']=='pdf')
                                                          $thumb_img=url('adminpanel/dist/img/pdf.png');
                                                            ?>
                                                            <div id="file_{{ $data['id'] }}" class="col-3 text-center"
                                                                style="position: relative;">
                                                                <label class="">{{ $data['name'] }}</label>
                                                                <i onclick="removeFile({{ $data['id'] }})"
                                                                    style="position: absolute; top:15px; right:0px; cursor:pointer"
                                                                    class="fas fa-times"></i>
                                                                <a href="{{ $data['path'] }}" target="_blank"><img
                                                                        class="w-100 shadow-1-strong rounded mb-4 img-thumbnail"
                                                                        src="{{ isset($thumb_img)?$thumb_img:'' }}" width="200" alt="Uploaded Image"></a>
                                                            </div>
                
                
                                                            <?php 
                                                          }
                                                      ?>
                
                                                    </div>
                                                        <div class="col-1">&nbsp;</div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-1">&nbsp;</div>
                                                    <div class="col-10 card card-default">
                                                      
                                                        <form action="{{ route('delivery.upload_proof',$id) }}"
                                                            method="post" enctype="multipart/form-data" id="image-upload"
                                                            class="dropzone ">
                                                            @csrf
                                                            <div>
                                                                <h4 class="form-label">Upload Multiple Files By Click On
                                                                    Box</h4>
                                                            </div>
                                                        </form>
                                                        <div class="card-footer">
                                                            You can select multiple files (e.g images, .docx , .xls ,.csv,
                                                            .pdf ) and upload

                                                        </div>
                                                    </div>
                                                    <div class="col-3">&nbsp;</div>

                                                </div>
                                            </div>
                                            <div class="card">
                                                @php
                                                if ($user->group_id==config('constants.groups.admin')){
                                                    $documents_for=$quotesData['delivery_documents_for_driver'];
                                                    $only_view_documents=$quotesData['delivery_documents_for_admin'];

                                                    $heading_for_documents1='Driver uploads ';
                                                    $heading_for_documents2='CRM Uploads (for Driver)';
                                                      
                                                  }
                                                  else {
                                                    $documents_for=$quotesData['delivery_documents_for_admin'];
                                                    $only_view_documents=$quotesData['delivery_documents_for_driver'];

                                                    $heading_for_documents1='CRM Uploads '; 
                                                    $heading_for_documents2='Driver Uploads (Record)';
                                                      
                                                  }
                                                  @endphp
                                                <div class="card-header p-2"><strong>{{$heading_for_documents1}} </strong> </div>
                                                <div class="row form-group">
                                                    <div class="col-1">&nbsp;</div>
                                                    <div class="col-10">
                                                        <div class="row form-group">
                                                            <?php
                                                     $imagesTypes=array('jpg','jpeg','png','gif');
                                                     $excelTypes=array('xls','xlsx');
                                                     $docTypes=array('doc','docx');
                                                        foreach($only_view_documents as $data){
                                                          if(in_array($data['otherinfo'],$imagesTypes))
                                                            $thumb_img=$data['path'];
                                                          else if(in_array($data['otherinfo'],$excelTypes))
                                                            $thumb_img=url('adminpanel/dist/img/xls.jpeg');
                                                          else if(in_array($data['otherinfo'],$docTypes))
                                                            $thumb_img=url('adminpanel/dist/img/doxx.png');
                                                          else if($data['otherinfo']=='pdf')
                                                          $thumb_img=url('adminpanel/dist/img/pdf.png');
                                                            ?>
                                                            <div id="file_{{ $data['id'] }}" class="col-3 text-center"
                                                                style="position: relative;">
                                                                <label class="">{{ $data['name'] }}</label>
                                                                @if ($user->group_id==config('constants.groups.admin'))
                                                                <i onclick="removeFile({{ $data['id'] }})"
                                                                    style="position: absolute; top:15px; right:0px; cursor:pointer"
                                                                    class="fas fa-times"></i>
                                                                    @endif
                                                                <a href="{{ $data['path'] }}" target="_blank"><img
                                                                        class="w-100 shadow-1-strong rounded mb-4 img-thumbnail"
                                                                        src="{{ isset($thumb_img)?$thumb_img:'' }}" width="200" alt="Uploaded Image"></a>
                                                            </div>
                
                
                                                            <?php 
                                                          }
                                                      ?>
                
                
                
                                                        </div>
                                                        <div class="col-1">&nbsp;</div>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                            <div class="card">
                                                <div class="card-header p-2">
                                                 
                                                <strong> {{$heading_for_documents2}}   </strong> 
                                                </div><!-- /.card-header -->
                                                <div class="row form-group">
                                                    <div class="col-1">&nbsp;</div>
                                                    <div class="col-10">
                                                        <div class="row form-group">
                                                            <?php
                                                     $imagesTypes=array('jpg','jpeg','png','gif');
                                                     $excelTypes=array('xls','xlsx');
                                                     $docTypes=array('doc','docx');
                                                        foreach($documents_for as $data){
                                                          if(in_array($data['otherinfo'],$imagesTypes))
                                                            $thumb_img=$data['path'];
                                                          else if(in_array($data['otherinfo'],$excelTypes))
                                                            $thumb_img=url('adminpanel/dist/img/xls.jpeg');
                                                          else if(in_array($data['otherinfo'],$docTypes))
                                                            $thumb_img=url('adminpanel/dist/img/doxx.png');
                                                          else if($data['otherinfo']=='pdf')
                                                          $thumb_img=url('adminpanel/dist/img/pdf.png');
                                                            ?>
                                                            <div id="file_{{ $data['id'] }}" class="col-3 text-center"
                                                                style="position: relative;">
                                                                <label class="">{{ $data['name'] }}</label>
                                                                <i onclick="removeFile({{ $data['id'] }})"
                                                                    style="position: absolute; top:15px; right:0px; cursor:pointer"
                                                                    class="fas fa-times"></i>
                                                                <a href="{{ $data['path'] }}" target="_blank"><img
                                                                        class="w-100 shadow-1-strong rounded mb-4 img-thumbnail"
                                                                        src="{{ isset($thumb_img)?$thumb_img:'' }}" width="200" alt="Uploaded Image"></a>
                                                            </div>
                
                
                                                            <?php 
                                                          }
                                                      ?>
                
                
                
                                                        </div>
                                                        <div class="col-1">&nbsp;</div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-1">&nbsp;</div>
                                                    <div class="col-10 card card-default">
                                                      
                                                        <form action="{{ route('delivery.uploade_document_for_driver',$id) }}"
                                                            method="post" enctype="multipart/form-data" id="image-upload"
                                                            class="dropzone ">
                                                            @if ($user->group_id==config('constants.groups.admin'))
                                                            <input type="hidden" name="documents_for" value="document_for_driver">    
                                                            @else
                                                            <input type="hidden" name="documents_for" value="document_for_admin">
                                                            @endif
                                                            
                                                            @csrf
                                                            <div>
                                                                <h4 class="form-label">Upload Multiple Files By Click On
                                                                    Box</h4>
                                                            </div>
                                                        </form>
                                                        <div class="card-footer">
                                                            You can select multiple files (e.g images, .docx , .xls ,.csv,
                                                            .pdf ) and upload

                                                        </div>
                                                    </div>
                                                    <div class="col-3">&nbsp;</div>

                                                </div>
                                            </div>
                                            <div class="card">
                                                <div class="card-header p-2">
                                                    <strong> Notes Section for Driver </strong>
                                                </div><!-- /.card-header -->
                                                <div class="card-body">
                                                    <div id="submit_comment_replace">
                                                        @php
                                                            // p($quotesData['comments']);
                                                        @endphp
                                                        @foreach ($quotesData['comments'] as $key => $comment)
                                                            <div class="row border">
                                                                <div class="col-12">
                                                                    <strong>{{ $comment['user']['name'] }}</strong>({{ $comment['slug'] }})
                                                                    {{ date('d/m/Y H:i:s', strtotime($comment['created_at'])) }}<br>
                                                                    {{ $comment['comment'] }}
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                    </div>
                                                    @php
                                                        $userData = get_session_value();
                                                        //p($userData);
                                                    @endphp
                                                    <div class="tab-content">
                                                        <form method="post" id="submit_comment">
                                                            <input type="hidden" name="group_id"
                                                                value="{{ $user->group_id }}">
                                                            <input type="hidden" name="action" value="submit_comment">
                                                            <input type="hidden" name="slug"
                                                                value="{{ $userData['get_groups']['slug'] }}">
                                                            <input type="hidden" name="user_name"
                                                                value="{{ $userData['name'] }}">
                                                            <div class="form-group">
                                                                <label for="inputDescription">Comment</label>
                                                                <textarea id="comments" name="comment" placeholder="Write comment about the quote" class="form-control"
                                                                    rows="4"></textarea></br>
                                                                <button
                                                                    onclick="do_action({{ $quotesData['id'] }},'submit_comment')"
                                                                    type="button" class="btn btn-success float-right"><i
                                                                        class="far fa-credit-card"></i> Send</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- /.col -->

                                    <div class="col-md-4">
                                      
                                        @if ($user->group_id == config('constants.groups.customer') && $quotesData['driver_id']>0)
                                        
                                        @php
                                        $driver_activities = driver_activities();
                                        
                                         @endphp
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Delivery Status</h3>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <form method="post" id="driver_activity">
                                                        <input type="hidden" name="action"
                                                            value="driver_activity_update">
                                                        <input type="hidden" name="uid"
                                                            value="{{ @$quotesData['driver']['id'] }}">
                                                        <tbody>
                                                            <?php
                                                            
                                                            foreach ($driver_activities as $key => $value) {
                                                                
                                                             ?>
                                                            <tr>
                                                                <th style="width:50%"><label>{{ $value }}
                                                                    </label>&nbsp;
                                                                </th>
                                                                <td>

                                                                    <div class="icheck-primary d-inline">
                                                                        <input type="radio" id="{{ $key }}"
                                                                            value="{{ $key }}"
                                                                            name="{{ $key }}" disabled
                                                                            {{ $quotesData[$key] != '' ? 'checked' : '' }}>
                                                                        <label for="{{ $key }}"></label>
                                                                    </div>
                                                                </td>
                                                                <td id="{{ $key }}_time">{!! $quotesData[$key] != '' ? date('d/m/Y h:i:s', $quotesData[$key]) : '' !!}
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            }
                                                        ?>


                                                        </tbody>
                                                    </form>

                                                </table>
                                            </div>
                                        @endif
                                        
                                        @if (1==2)
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Driver Activities</h3>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                
                                                    <tbody>
                                                        <tr><th class="text-center" colspan="2">Arrived at Pick up</th></tr>
                                                        <form method="post" id="dirver_activity_for_sms">
                                                            <input type="hidden" name="action"
                                                                value="dirver_activity_for_sms">
                                                            <input type="hidden" name="uid"
                                                                value="{{ $quotesData['driver']['id'] }}">
                                                        <tr>
                                                            <td>
                                                                    <div class="input-group date" id="arrived_at_datetime" data-target-input="nearest">
                                                                        <input placeholder="Date & Time" name="arrived_at_pickup" type="text" value="{{ $quotesData['arrived_at_pickup'] }}" class="form-control datetimepicker-input" data-target="#arrived_at_datetime"/>
                                                                        <div class="input-group-append" data-target="#arrived_at_datetime" data-toggle="datetimepicker">
                                                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                        </div>
                                                                    </div>
                                                            </td>
                                                           
                                                            <td>
                                                                <button onclick="do_action({{$quotesData['id']}},'dirver_activity_for_sms')" class=" float-right btn btn-success btn-block btn-sm"><i class="fa fa-save"></i> Save Changes</button>
                                                            </td>
                                                        </tr>
                                                        </form>

                                                        <tr><th class="text-center" colspan="2">Arriving at Drop off</th></tr>
                                                        <form method="post" id="dirver_activity_for_sms">
                                                            <input type="hidden" name="action"
                                                                value="dirver_activity_for_sms">
                                                            <input type="hidden" name="activity"
                                                                value="arriving_at_dropoff">
                                                            <input type="hidden" name="uid"
                                                                value="{{ $quotesData['driver']['id'] }}">
                                                        <tr>
                                                            <td>
                                                                    <div class="input-group date" id="arriving_at_datetime" data-target-input="nearest">
                                                                        <input placeholder="Date & Time" name="arriving_at_dropoff" type="text" value="{{ $quotesData['arriving_at_dropoff'] }}" class="form-control datetimepicker-input" data-target="#arriving_at_datetime"/>
                                                                        <div class="input-group-append" data-target="#arriving_at_datetime" data-toggle="datetimepicker">
                                                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                        </div>
                                                                    </div>
                                                            </td>
                                                           
                                                            <td>
                                                                <button onclick="do_action({{$quotesData['id']}},'dirver_activity_for_sms')" class=" float-right btn btn-success btn-block btn-sm"><i class="fa fa-save"></i> Save Changes</button>
                                                            </td>
                                                        </tr>
                                                        </form>
                                                     
                                                        

                                                    </tbody>
                                                </form>

                                            </table>
                                        </div>
                                        @endif

                                        
                                        @if ( 
                                                //(1==2) && // this is used to comment the whole section
                                                (
                                                $user->group_id == config('constants.groups.admin') ||
                                                ($user->group_id == config('constants.groups.driver') && !empty($quotesData['driver']))
                                                )
                                               // && isset($quotesData['driver'])
                                               // && !empty($quotesData['driver'])
                                            )
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Driver Activities </h3>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <form method="post" id="driver_activity">
                                                        <input type="hidden" name="action"
                                                            value="driver_activity_update">
                                                        <input type="hidden" name="uid"
                                                            value="{{ @$quotesData['driver']['id'] }}">
                                                        <tbody>
                                                            <?php
                                                            $driver_activities=driver_activities();
                                                            //p($driver_activities);

                                                            if(!empty($quotesData['reached_at_pickup']) && !empty($quotesData['delivered']))
                                                            $elapsed_time=elapsed_time($quotesData['reached_at_pickup'],$quotesData['delivered']);

                                                            foreach ($driver_activities as $key => $value) {
                                                                
                                                             ?>
                                                            <tr>
                                                                <th><label>{{ $value }}</label>&nbsp;</th>
                                                                <td>
                                                                    <div class="icheck-primary d-inline">
                                                                        <input
                                                                            onclick="driver_activity('{{ $key }}','dirver_activity')"
                                                                            type="radio" id="{{ $key }}"
                                                                            value="{{ $key }}"
                                                                            name="{{ $key }}"
                                                                            {{ $quotesData[$key] != '' ? 'checked disabled' : '' }}>
                                                                        <label for="{{ $key }}"></label>
                                                                    </div>
                                                                </td>
                                                                @if($key!='reached_at_pickup-ignore' && $key!='on_the_way')
                                                                <td id="{{ $key }}_time">{!! $quotesData[$key] != '' ? date(config('constants.date_and_time'), $quotesData[$key]) : '&nbsp;' !!}
                                                                  @endif

                                                                        @if($key=='reached_at_pickup-ignore' || $key=='on_the_way')
                                                                        <td id="{{ $key }}_time">{!! $quotesData[$key] != '' ? date(config('constants.date_formate'), $quotesData[$key]) : '&nbsp;' !!}
                                                                @php
                                                                // echo '<br>';
                                                                //     echo $date_time= $quotesData[$key] != '' ? date(config('constants.date_formate'), $quotesData[$key]).' 8:16 AM' : '';
                                                                //     echo '<br>';
                                                                //     echo  $str_time_date=strtotime($date_time); 
                                                                //     echo '<br>';
                                                                //     echo date(config('constants.date_and_time'),$str_time_date);
                                                                @endphp
                                                                            <br>
                                                                            <input name="date_of_{{$key}}" type="hidden" value="{!! $quotesData[$key] != '' ? date(config('constants.date_formate'), $quotesData[$key]) : '' !!}">
                                                                            <br>
                                                                        <div class="input-group date" id="timepicker_{{$key}}" data-target-input="nearest">
                                                                           
                                                                        <input placeholder="Time" name="arriving_at_{{$key}}" id="arriving_at_{{$key}}" type="text" value="{{$key=='on_the_way'?$quotesData['arriving_at_dropoff']:$quotesData['arrived_at_pickup']}}" class="form-control datetimepicker-input" data-target="#timepicker_{{$key}}"/>
                                                                                <div class="input-group-append" data-target="#timepicker_{{$key}}" data-toggle="datetimepicker">
                                                                                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                                                                </div>
                                                                        </div>
                                                                        <br>
                                                                        <div onclick="update_time({{$quotesData['id']}},'{{$key}}','dirver_activity_for_time')" class=" float-right btn btn-success btn-block btn-sm"><i class="fa fa-save"></i> Save Changes</div>
                                                                    
                                                                        @endif
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            }
                                                        ?>
                                                        @if (isset($elapsed_time))
                                                        <tr><th>Total Working Hours</th><td colspan="2">{{$elapsed_time['hours']}} Hours and {{$elapsed_time['mins']}} mins</td></tr>
                                                        @endif
                                                            

                                                        </tbody>
                                                    </form>

                                                </table>
                                            </div>
                                            @endif 
                                            {{-- This is end of Driver Activity if --}}
                                            @if(isset($quotesData['sub_id']) && ($quotesData['sub_id']>0) && ($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.sub')))
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Sub Information</h3>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <form method="post" id="change_sub_status">
                                                        <input type="hidden" name="action" value="change_sub_status">
                                                        <input type="hidden" name="sub_id"
                                                            value="{{ $quotesData['sub']['id'] }}">
                                                        <tbody>
                                                            <tr>
                                                                <th style="width:50%">Business Name</th>
                                                                <td>{{ $quotesData['sub']['business_name'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Agreed amount</th>
                                                                <td><span style="color:#db0707; font-weight:700; ">${{ $quotesData['quoted_price_for_sub'] }} </span></td>
                                                            </tr>
                                                            <tr>
                                                                <th> Contact Person Name</th>
                                                                <td>{{ $quotesData['sub']['name'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Email</th>
                                                                <td>{{ $quotesData['sub']['email'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Phone</th>
                                                                <td>{{ $quotesData['sub']['phone'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>City/State/Zipcode</th>
                                                                <td>{{ $quotesData['sub']['city'] }},{{ $quotesData['sub']['state'] }},{{ $quotesData['sub']['zipcode'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Unit/STE</th>
                                                                <td>{{ $quotesData['sub']['business_unit_ste'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Business Address <Address></Address></th>
                                                                <td>{{ $quotesData['sub']['business_address'] }}</td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <th>Business Tax ID</th>
                                                                <td>{{ $quotesData['sub']['business_tax_id'] }} years
                                                                </td>
                                                            </tr>
                                                            {{-- <tr>
                                                                <th>Status</th>
                                                                <td>{{ sub_status_msg($quotesData['sub_status'])}}
                                                                </td>
                                                            </tr> --}}
                                                            <tr>
                                                                <th>Status</th>
                                                                <td>
                                                                    <select name="sub_status" id="sub_action" onchange="do_action({{$quotesData['id']}},'change_sub_status')" class="form-control select2bs4">
                                                                        <option {{$quotesData['sub_status']==0?'selected':''}} value="0"> Pending </option>
                                                                        <option {{$quotesData['sub_status']==1?'selected':''}} value="1"> Approve </option>
                                                                        <option {{$quotesData['sub_status']==2?'selected':''}} value="2">Remove</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            
                                                        </tbody>
                                                    </form>
    
                                                </table>
                                            </div>
                                            @elseif(empty($quotesData['driver_id']) && empty($quotesData['sub_id']))
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Assign Driver/Sub</h3>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-12">
                                            <div class="table-responsive">
                                                <form id="assign_driver_sub" method="post">
                                                    @csrf
                                                    <input type="hidden" name="quote_id" value="{{$quotesData['id']}}">
                                                <table class="table" width="100%" style="width:100%">
                                                    <tr>
                                                    <th width="50%">Assign To</th>
                                                    <td>
                                                          
                                                            <div class="form-group clearfix mt-1">
                                                                <div class="icheck-primary d-inline ml-1">
                                                                  <input onclick="$('#drivers_options').show();$('.subs_options').hide();" type="radio" value="1" id="radioPrimary1" name="assign_to" checked>
                                                                  <label for="radioPrimary1"> Driver</label>
                                                                </div>
                                                                <div class="icheck-primary d-inline ml-3">
                                                                  <input  onclick="$('.subs_options').show();$('#drivers_options').hide();" type="radio" value="2" id="radioPrimary2" name="assign_to">
                                                                  <label for="radioPrimary2"> Sub</label>
                                                                </div>
                                                              </div>
                                                    </td>
                                                    </tr >
                                                    <tr id="drivers_options"><td>Select Driver</td>
                                                        <td>
                                                        <select placeholder="select Driver" name="driver_id" class="select2bs4 form-control">
                                                            {!!get_drivers_options()!!}
                                                        </select>    
                                                    </td>
                                                    </tr>
                                                    <tr class="subs_options" style="display: none;"><td>Select Sub</td>
                                                        <td>
                                                            <select placeholder="select Sub" name="sub_id" class="select2bs4 form-control">
                                                                {!!get_subs_options()!!}
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr class="subs_options" style="display: none;">
                                                        <td>Amount</td>
                                                        <td><input type="number" name="quoted_price_for_sub" required
                                                            value="{{ old('quoted_price_for_sub') }}"
                                                            placeholder="Price for Sub in USD" class="form-control"></td>
                                                    </tr>
                                                    <tr><td>&nbsp;</td><td><div onclick=" do_action({{$quotesData['id']}},'assign_driver_sub')" class="btn btn-primary btn-block btn-sm"><i class="fas fa-save"></i> Save Changes</div></td></tr>
                                                </table>
                                                </form>
                                            </div>
                                        @endif

                                        
                                        
                                        @if ($user->group_id==config('constants.groups.admin') ||
                                        $user->group_id==config('constants.groups.customer')
                                        )
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Payments</h3>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table" width="100%" style="width:100%">
                                                <tbody>
                                                        <tr>
                                                            <th style="width:50%">Delivery Cost</th>
                                                            <td style="width:50%">${{isset($delivery_cost)?$delivery_cost:$delivery_cost=0;}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Paid Amount 1</th>
                                                            <td>${{$recievedAmount=received_amount($quotesData['id'])}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Due amount</th>
                                                            <td>${{$due_amount=$delivery_cost-$recievedAmount}}</td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                

                                            </table>
                                        </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if (isset($quotesData['driver']) && !empty($quotesData['driver']) && $user->group_id!=config('constants.groups.customer'))
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Driver Info</h3>
                                                <div style="width:10ppx; float:right"><button onclick="do_action({{$quotesData['id']}},'remove_quote_driver')" class=" float-right btn btn-danger btn-block btn-sm"><i
                                                    class="fa fa-trash"></i> Remove Driver</button></div>
                                            </div>
                                            @if ($user->group_id==config('constants.groups.admin'))
                                            <form id="change_quote_driver" method="post">
                                                @csrf
                                                <input type="hidden" name="quote_id" value="{{$quotesData['id']}}">
                                                <div class="row form-group">
                                                <div class="col-8">
                                                    <label class="col-form-label">Select Driver</label>
                                                    <div class="input-group mb-3" >
                                                        <select placeholder="select Driver" name="driver_id" class="select2bs4 form-control @error('photographer_expense[]') is-invalid @enderror">
                                                            {!!get_drivers_options($quotesData['driver_id'])!!}
                                                        </select>
                                                        @error('driver_id')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <div class="col-4" style="margin-top: 42px">
                                                    <button onclick="do_action({{$quotesData['id']}},'change_quote_driver')" class=" float-right btn btn-success btn-block btn-sm"><i
                                                            class="fa fa-plus"></i> Save Changes</button>
                                                </div>
                                                </div>
                                            </form>
                                            @endif
                                            <div class="table-responsive">
                                                <table class="table">
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
                                                            <th>Billing Email</th>
                                                            <td>{{ $quotesData['customer']['billing_email'] }}</td>
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
                                                            <td>
                                                                @php
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
            //Date and time picker
            $('#arrived_at_datetime').datetimepicker({ icons: { time: 'far fa-clock' } });
            $('#arriving_at_datetime').datetimepicker({ icons: { time: 'far fa-clock' } });
            //Timepicker
            $('#timepicker_reached_at_pickup').datetimepicker({format: 'LT'});
            $('#timepicker_on_the_way').datetimepicker({format: 'LT'});

        });

 
function update_time(id,key,action_name) {
    if (confirm('Are you sure? you want to add time?')) 
    {
        date_of_booking= $('#date_of_'+key).val();
        time_of_booking= $('#arriving_at_'+key).val();
            var sendInfo = {
                action: action_name,
                key: key,
                date_of_booking: date_of_booking,
                time_of_booking: time_of_booking,
                id: id
            };

        $.ajax({
            url: "{{ route('quotes.ajaxcall',$id) }}",
            data: sendInfo,
            contentType: 'application/json',
            error: function() {
                alert('There is Some Error, Please try again !');
            },
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error == 'No') {
                   
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
}
        function driver_activity(activity, action_name = '') {
            id = {{ $quotesData['id'] }};
            var sendInfo = {
                activity: activity,
                action: action_name,
                id: id
            };
            alertMsg = 'Are you sure you want to log this activity?';
            if (confirm(alertMsg)) {
                $('#_loader').show();
                $.ajax({
                    url: "{{ route('quotes.ajaxcall',$quotesData['id']) }}?time={{time()}}",
                    data: sendInfo,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {

                        $('#' + activity + '_time').html(data._datetime);
                       
                        if (data.error == 'Yes'){
                            $("#" + activity).prop('checked', false);
                            alert(data.title);
                            $('#_loader').hide();
                        }
                        

                        if (data.error == 'No') {
                            $('#file_' + id).remove();
                            $(document).Toasts('create', {
                                class: 'bg-success',
                                title: data.title,
                                subtitle: 'record',
                                body: data.msg
                            });
                            window.location = "";

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
            } else {
                $("#" + activity).prop('checked', false);
            }

        }

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
            else if (action_name == 'submit_comment_crm') {
                if ($('#comments_crm').val() == '')
                    return false;
            }
            $('#_loader').show();
            $.ajax({
                url: "{{ route('quotes.ajaxcall',$quotesData['id']) }}?time={{time()}}" ,
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again.. !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                   

                    $('#' + action_name + '_replace').append(data.response);
                    $('#comments').val('');
                    $('#comments_crm').val('');
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

                    if(action_name=='change_quote_driver' || action_name=='update_delivery_price' || data.reload=='yes')
                    window.location='';
                    
                }
            });

            

        }
    </script>
@endsection
