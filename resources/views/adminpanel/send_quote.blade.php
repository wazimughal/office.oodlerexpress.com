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
                                                        <div class="row">
                                                            <div class="col-3">&nbsp;</div>
                                                            <div class="col-6">

                                                                @if ($errors->any())
                                                                    {{ implode('', $errors->all('<div>:message</div>')) }}
                                                                @endif
                                                                <!-- flash-message -->
                                                                <div class="flash-message">
                                                                    @if ($errors->any())
                                                                        {{ implode('', $errors->all('<div>:message</div>')) }}
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

                                                        <div class="row invoice-info">

                                                            <!-- /.col -->
                                                            <div class="col-sm-4 invoice-col">
                                                                <strong>Pick Up Detail </strong> <br>
                                                                Date : {{ $quotesData['pickup_date'] }}<br>
                                                                Time
                                                                :{{ $quotesData['pickup_at_time'] == 1 ? 'AM' : 'PM' }}<br>
                                                                Street Address
                                                                :{{ $quotesData['pickup_street_address'] }}<br>
                                                                Unit :{{ $quotesData['pickup_unit'] }}<br>
                                                                Contact No. :{{ $quotesData['pickup_contact_number'] }}<br>
                                                            </div>
                                                            <div class="col-sm-4 invoice-col">&nbsp;</div>
                                                            <div class="col-sm-4 invoice-col">

                                                                <strong>Drop-Off Detail </strong> <br>
                                                                Date : {{ $quotesData['drop_off_date'] }}<br>
                                                                Time
                                                                :{{ $quotesData['drop_off_at_time'] == 1 ? 'AM' : 'PM' }}<br>
                                                                Street Address
                                                                :{{ $quotesData['drop_off_street_address'] }}<br>
                                                                Unit :{{ $quotesData['drop_off_unit'] }}<br>
                                                                Contact No.
                                                                :{{ $quotesData['drop_off_contact_number'] }}<br>
                                                            </div>

                                                        </div>


                                                    </div>
                                                    <!-- /.tab-pane -->

                                                </div>
                                                <!-- /.tab-content -->
                                            </div><!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->

                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Quotes Sent </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div class="row">
                                                            <div class="col-3">&nbsp;</div>
                                                            <div class="col-6">

                                                                @if ($errors->any())
                                                                    {{ implode('', $errors->all('<div>:message</div>')) }}
                                                                @endif
                                                                <!-- flash-message -->
                                                                <div class="flash-message">
                                                                    @if ($errors->any())
                                                                        {{ implode('', $errors->all('<div>:message</div>')) }}
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
                                                                        <td>${{ $data['extra_charges']!=''?$data['extra_charges']:0 }}</td>
                                                                        <td>{{ $data['reason_for_extra_charges'] }}</td>
                                                                        <td>{{ $data['description'] }}</td>
                                                                        <td>{{ date('d/m/Y H:i:s', strtotime($data['created_at'])) }}</td>
                                                                        <td>
                                                                            @if($data['status']==1)
                                                                            <span class="btn btn-success btn-block btn-sm"><i class="fas fa-chart-line"></i>
                                                                                Active</span>
                                                                                @else
                                                                                <span class="btn btn-primary btn-block btn-sm"><i class="fas fa-chart-line"></i>
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
                                                    <form action="{{ route('quotes.send_quote_data', $id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" value="{{ $quotesData['id'] }}"
                                                            name="quote_id">
                                                        <div class="form-group row">
                                                            <div class="col-sm-3">
                                                                <label>TotaL Cost</label>
                                                                <input type="number"
                                                                        name="quoted_price" required value="{{ old('quoted_price') }}"
                                                                        placeholder="Total Cost in USD" class="form-control">
                                                                        @error('quoted_price')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                            </div>

                                                            <div class="col-sm-3">
                                                                <label>Extra Charges
                                                                </label>
                                                                <div class="input-group mb-2"><input type="number"
                                                                        name="extra_charges" value="{{ old('extra_charges') }}"
                                                                        placeholder="Any Extra Charges" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label>Reason for Extra Charge</label>
                                                                <div class="input-group mb-2"><input type="text"
                                                                        name="reason_for_extra_charges" value="{{ old('reason_for_extra_charges') }}"
                                                                        placeholder="Reason for extra Charge" class="form-control">
                                                                </div>
                                                            </div>
                                                           
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-sm-12">
                                                                <label>Description</label>
                                                                <div class="input-group mb-2">
                                                                    <textarea name="description" value="{{ old('description') }}" placeholder="Description about Payment"
                                                                        class="form-control"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-4">&nbsp;</div>
                                                            <div class="col-4">
                                                                <button type="submit"
                                                                    class="btn btn-outline-success btn-block btn-lg"><i
                                                                        class="fa fa-save"></i> Send Quote</button>
                                                            </div>
                                                            <div class="col-4">&nbsp;</div>

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        {{-- This section is for Comments --}}
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Notes Section </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div id="submit_comment_replace">
                                                    @php
                                                        // p($quotesData['comments']);
                                                    @endphp
                                                    @foreach ($quotesData['comments'] as $key => $comment)
                                                        <div class="row border">
                                                            <div class="col-12">
                                                                <strong>({{ $comment['slug'] }}) </strong>
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
                                                            value="{{ $userData['name']}}">
                                                        <div class="form-group">
                                                            <label for="inputDescription">Comment</label>
                                                            <textarea id="comments" name="comment" placeholder="Write comment about the Booking" class="form-control"
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

                                    </div>

                                    <!-- /.col -->

                                    <div class="col-md-4">
                                        
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
                                                            <td>{{ $quotesData['customer']['shipping_cat'] }}</td>
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
