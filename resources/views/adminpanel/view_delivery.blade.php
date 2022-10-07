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
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item active">View Delivery</li>
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
                                                                                class="select2bs4 form-control @error('photographer_expense[]') is-invalid @enderror">
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


                                        {{-- This section is for Comments --}}
                                        @if ($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.driver'))
                                            
                                        
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
                                                            <textarea id="comments" name="comment" placeholder="Write comment about the quote" class="form-control" rows="4"></textarea></br>
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
                                        @php
                                            $driver_activities=driver_activities();
                                            
                                        @endphp
                                        @if ($user->group_id==config('constants.groups.customer'))
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Delivery Status</h3>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <form method="post" id="driver_activity">
                                                    <input type="hidden" name="action" value="driver_activity_update">
                                                    <input type="hidden" name="uid"
                                                        value="{{ $quotesData['driver']['id'] }}">
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
                                                            <td id="{{ $key }}_time">{!! $quotesData[$key] != '' ? date('d/m/Y h:i:s',$quotesData[$key]) : '' !!}</td>
                                                        </tr>
                                                        <?php
                                                            }
                                                        ?>


                                                    </tbody>
                                                </form>

                                            </table>
                                        </div>
                                        @endif

                                        @if ($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.driver') && !empty($quotesData['driver']))
                                       
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Driver Activities</h3>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <form method="post" id="driver_activity">
                                                    <input type="hidden" name="action" value="driver_activity_update">
                                                    <input type="hidden" name="uid"
                                                        value="{{ $quotesData['driver']['id'] }}">
                                                    <tbody>
                                                        <?php
                                                            $driver_activities=driver_activities();
                                                            foreach ($driver_activities as $key => $value) {
                                                                
                                                             ?>
                                                        <tr>
                                                            <th style="width:50%"><label>{{ $value }}
                                                                </label>&nbsp;
                                                            </th>
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
                                                            <td id="{{ $key }}_time">{!! $quotesData[$key] != '' ? date('d/m/Y h:i:s',$quotesData[$key]) : '&nbsp;' !!}</td>
                                                        </tr>
                                                        <?php
                                                            }
                                                        ?>


                                                    </tbody>
                                                </form>

                                            </table>
                                        </div>
                                        @endif
                                        @if (isset($quotesData['driver']) && !empty($quotesData['driver']))
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Driver Info</h3>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <form method="post" id="driver_update">
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
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });

        function driver_activity(activity, action_name = '') {
            id={{ $quotesData['id'] }};
            var sendInfo = {
                activity: activity,
                action: action_name,
                id: id
            };
            alertMsg = 'Are you sure you want to log this activity?';
        if(confirm(alertMsg)){
            $('#_loader').show();
            $.ajax({
                url: "{{ url('/admin/quotes/ajaxcall/') }}/" +id,
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    $('#' + activity + '_time').html(data._datetime);

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
                    window.location = "";
                }
            });
        }else{
            $("#"+activity).prop('checked', false);;
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
