@extends('adminpanel.admintemplate')
@push('title')
    <title>
        quotes| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <h1>View {{ $list_title }} </h1>

                    </div>
                    <div class="col-sm-4">
                        @if ($user->group_id != config('constants.groups.admin'))
                            <a style="width:60%" href="{{ route('quotes.request_quotes_form') }}"
                                class="btn btn-block btn-success btn-lg">Request New Quote <i class="fa fa-plus"></i></a>
                    </div>
                    @endif

                    <div class="col-sm-2">
                        {{-- <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol> --}}
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
                                <h3 class="card-title">{{ $list_title }}</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form id="search_form" method="GET" action="{{ route('admin.quote.types', $type) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="search_form">
                                    <input type="hidden" id="export_xls" name="export" value="noexport">
                                    @if (isset($_GET['page']) && $_GET['page'] > 0)
                                        <input type="hidden" name="page" value="{{ $_GET['page'] + 1 }}">
                                    @endif

                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td>
                                                <label>From</label>
                                                <div class="input-group date" id="from_date" data-target-input="nearest">
                                                    <input id="input_from_date" type="text"
                                                        value="{{ isset($_GET['from_date']) ? $_GET['from_date'] : '' }}"
                                                        name="from_date" placeholder="From date"
                                                        class="form-control datetimepicker-input"
                                                        data-target="#from_date" />
                                                    <div class="input-group-append" data-target="#from_date"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                    @error('from_date')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </td>
                                            <td>
                                                <label>To</label>
                                                <div class="input-group date" id="to_date" data-target-input="nearest">
                                                    <input id="input_to_date" type="text"
                                                        value="{{ isset($_GET['to_date']) ? $_GET['to_date'] : '' }}"
                                                        name="to_date" placeholder="To Date"
                                                        class="form-control datetimepicker-input" data-target="#to_date" />
                                                    <div class="input-group-append" data-target="#to_date"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                    @error('to_date')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </td>
                                            @if ($user->group_id == config('constants.groups.admin'))
                                                <td>
                                                    <label>Select Customer</label>
                                                    <select id="customer_ids" name="customer_id[]"
                                                        class="form-control select2bs4" multiple="multiple"
                                                        data-placeholder="Select Customer" style="width: 100%;">
                                                        {!! get_customers_options($customer_ids) !!}
                                                    </select>
                                                </td>
                                                {{-- <td>
                                            <label>Select Driver</label>
                                            <select name="driver_id[]" class="form-control select2bs4" multiple="multiple" data-placeholder="Select Driver" style="width: 100%;">
                                                {!!get_drivers_options($driver_ids)!!}
                                            </select>
                                        </td> --}}
                                            @endif
                                            <td><button onclick="$('#search_form').submit()" style="margin-top: 32px;"
                                                    type="button" class="btn btn-block btn-primary"><i
                                                        class="fa fa-search"></i>Search</button></td>
                                            <td><a href="{{ route('admin.quote.types', $type) }}" style="margin-top: 32px;"
                                                    type="button" class="btn btn-block btn-secondary"><i
                                                        class="fa fa-undo"></i> Cancel</a></td>
                                        </tr>

                                    </table>
                                </form>
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-4">
                                        <input class="form-control" onkeyup="search_quote()" type="text" id="qsearch"
                                            name="qsearch" placeholder="Type PO Number to search">
                                    </div>
                                </div>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Quote Type</th>
                                            <th>Customer Business</th>
                                            <th>PO Number</th>
                                            <th>Pick-up Street Address</th>
                                            <th>Pick-up Phone</th>
                                            <th>Drop-off Street Address</th>
                                            <th>Drop-off Phone</th>

                                            <th>Move to</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            //p($quotesData); die;
                                            foreach ($quotesData as $data){
                                                
                                                    $view_document='';
                                                    //p($data['document_for_request_quote']); die;
                                                    if(count($data['document_for_request_quote'])>0){
                                                        $view_document='<a href="'.route('quote_requested_documents',$data['id']).'"
                                                    class="btn btn-secondary btn-block btn-sm"><i class="fas fa-eye"></i> Docs</a>';
                                                    }
                                            ?>
                                        <tr id="row_{{ $data['id'] }}">
                                            <td><strong
                                                    id="quote_type_{{ $data['id'] }}">{{ $data['quote_type'] }}</strong>
                                            </td>
                                            <td id="business_name_{{ $data['id'] }}">
                                                {{ $data['customer']['business_name'] }}</td>
                                            <td id="po_number_{{ $data['id'] }}">
                                                {{ $data['po_number'] }}</td>
                                            <td id="pickup_street_address_{{ $data['id'] }}">
                                                {{ $data['pickup_street_address'] }}</td>
                                            <td id="pickup_contact_number_{{ $data['id'] }}">
                                                {{ $data['pickup_contact_number'] }}
                                            </td>
                                            <td id="drop_off_street_address_{{ $data['id'] }}">
                                                {{ $data['drop_off_street_address'] }}
                                            </td>
                                            <td id="drop_off_contact_number_{{ $data['id'] }}">
                                                {{ $data['drop_off_contact_number'] }}
                                            </td>
                                            {{-- <td id="status{{ $data['id'] }}">
                                                @if ($data['status'] == config('constants.quote_status.pedning'))
                                                    <span class="btn btn-info btn-block btn-sm"><i
                                                            class="fas fa-chart-line"></i>
                                                        Pending</span>
                                                @elseif($data['status'] == config('constants.quote_status.quote_submitted'))
                                                    <span class="btn btn-success btn-block btn-sm"><i
                                                            class="fas fa-chart-line"></i>
                                                        New</span>
                                                @elseif($data['status'] == config('constants.quote_status.declined'))
                                                    <span class="btn btn-warning btn-block btn-sm"><i
                                                            class="fas fa-chart-line"></i>
                                                        Cancelled</span>
                                                @elseif($data['status'] == config('constants.quote_status.approved'))
                                                    <span class="btn btn-success btn-block btn-sm"><i
                                                            class="fas fa-chart-line"></i>
                                                        Approved</span>
                                                @endif

                                            </td> --}}
                                            <td>
                                                <select id="current_status_{{ $data['id'] }}"
                                                    onchange="do_change({{ $data['id'] }},'change_status',{{ $counter }})"
                                                    name="status" class="form-control select2bs4">
                                                    @php
                                                        echo get_quote_status_options($data['status']);
                                                    @endphp
                                                </select>
                                            </td>

                                            <td>
                                                @if ($data['status'] == config('constants.quote_status.approved'))
                                                    <a href="{{ route('quotes.add_to_delivery_form', $data['id']) }}"
                                                        class="btn btn-success btn-block btn-sm"><i
                                                            class="fas fa-plus"></i> Delivery</a>
                                                @endif

                                                <a href="{{ route('quotes.send_quote_form', $data['id']) }}"
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-upload"></i>
                                                    Send </a>
                                                {{-- {!! $view_document !!}
                                                <a href="{{ route('quotes.quoteeditform', $data['id']) }}"
                                                    class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</a> --}}
                                                @if ($data['is_active'] == 1)
                                                    <button
                                                        onClick="do_action({{ $data['id'] }},'delete',{{ $counter }})"
                                                        type="button" class="btn btn-danger btn-block btn-sm"><i
                                                            class="fas fa-trash"></i>
                                                        Delete</button>
                                                @elseif ($data['is_active'] == 2)
                                                    <button
                                                        onClick="do_action({{ $data['id'] }},'restore',{{ $counter }})"
                                                        type="button" class="btn btn-primary btn-block btn-sm"><i
                                                            class="fas fa-trash"></i>
                                                        Restore</button>
                                                @endif
                                            </td>

                                            </td>

                                        </tr>
                                        <?php 
                                                
                                            
                                            $counter ++;
                                        }
                                        ?>




                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Quote Type</th>
                                            <th>Customer Business</th>
                                            <th>PO Number</th>
                                            <th>Pick-up Street Address</th>
                                            <th>Pick-up Phone</th>
                                            <th>Drop-off Street Address</th>
                                            <th>Drop-off Phone</th>
                                            <th>Move to</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="9">
                                                <div class="text-right"> {{ $quotesData->links() }}</div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                {{-- Pagination --}}

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <div class="modal fade" id="modal-xl-lead">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"> Quote Panel</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="responseData">
                            This is the Body of modal
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
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
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
            $('#from_date').datetimepicker({
                format: 'L'
            });
            $('#to_date').datetimepicker({
                format: 'L'
            });
        });

        function search_quote() {

            searchval = $('#qsearch').val();
            if (searchval.length < 4 && searchval.length > 0) {
                return false;
            }

            $('#input_from_date').val('');
            $('#input_to_date').val('');

            var sendInfo = {
                action: 'qsearch_quote',
                qsearch: searchval,
                quote_type: "{{ $type }}",
            };

            $.ajax({
                url: "{{ route('quotes.ajaxcall', 1) }}",
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        $('#example1').html(data.response);


                    } else {
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });
                    }

                    //alert('i am here');
                }
            });

        }

        function do_action(id, action_name, counter_id) {

            if (action_name == 'restore')
                alertMsg = 'Are you sure you want to Restore this?';
            else if (action_name == 'delete')
                alertMsg = 'Are you sure you want to Delete this?';

            if (confirm(alertMsg)) {

                var sendInfo = {
                    action: action_name,
                    counter: counter_id,
                    id: id
                };

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
                        console.log(data);
                        if (data.error == 'No') {
                            $('#row_' + id).remove();
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
                        console.log(data);
                    }
                });
            }
        }

        function do_change(id, action_name, counter_id) {

            alertMsg = 'Are you sure you want to change this?';

            if (confirm(alertMsg)) {
                $('#_loader').show();
                current_status = $('#current_status_' + id).val();

                var sendInfo = {
                    action: action_name,
                    counter: counter_id,
                    id: id,
                    current_status: current_status
                };
                console.log(sendInfo);
                $.ajax({
                    url: "{{ route('quotes.ajaxcall') }}/" + id + "?time={{ time() }}",
                    data: sendInfo,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data.requesdata);
                        if (data.error == 'No') {
                            $('#row_' + id).remove();
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
                        $('#_loader').hide();
                        //window.location="?time={{ time() }}"
                        console.log(data);
                    }
                });
            }
        }
    </script>
@endsection
