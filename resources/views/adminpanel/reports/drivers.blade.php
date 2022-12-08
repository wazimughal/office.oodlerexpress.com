@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Drivers| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-2">
                        <h1>View Drivers </h1>

                    </div>
                    <div class="col-sm-3">&nbsp;</div>
                    <div class="col-sm-1">&nbsp;</div>
                    <div class="col-sm-6">
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
                                <h3 class="card-title">Drivers</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form id="search_form" method="GET" action="{{ route('driver.report_driver_working_hours') }}">
                                    @csrf
                                    <input type="hidden" name="action" value="search_form">
                                    <input type="hidden" id="export_xls" name="export" value="noexport">
                                    <input type="hidden" name="driver_id" value=""> 
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-4">
                                        <label>Type and Search</label>
                                        <input class="form-control" onkeyup="search_driver()" type="text"
                                            id="qsearch" name="qsearch"
                                            placeholder="Type email or name of driver to search">
                                    </div>
                                    
                                </div>
                            </form>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>License No.</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            $driver_id_array=array();
                                            foreach ($driversData as $data){
                                                $driver_id_array[]=$data['id'];
                                            ?>
                                        <form id="download_working_hours_{{$data['id']}}" method="GET" action="{{ route('driver.report_driver_working_hours') }}">
                                        @csrf
                                        <input type="hidden" name="action" value="download_working_hours">
                                        <input type="hidden" name="driver_id" value="{{$data['id']}}">    
                                        
                                        <tr id="row_{{ $data['id'] }}">
                                            <td><strong id="name_{{ $data['id'] }}">{{ $data['name'] }}</strong>
                                            </td>
                                            <td id="email_{{ $data['id'] }}">{{ $data['email'] }}</td>
                                            <td id="mobileno_{{ $data['id'] }}">
                                                {{ $data['mobileno'] }}</td>
                                            <td id="address_{{ $data['id'] }}">
                                                {{ $data['address'] }}</td>
                                            <td id="license_no_{{ $data['id'] }}">
                                                {{ $data['license_no'] }}
                                            </td>
                                            <td id="row_from_date_{{ $data['id'] }}">
                                                <div class="input-group date" id="from_date_{{ $data['id'] }}" data-target-input="nearest">
                                                    <input id="input_from_date_{{ $data['id'] }}"  type="text"  name="from_date" placeholder="From date" class="form-control datetimepicker-input" data-target="#from_date_{{ $data['id'] }}"/>
                                                    <div class="input-group-append" data-target="#from_date_{{ $data['id'] }}" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>  
                                            </td>
                                            <td id="row_to_date_{{ $data['id'] }}">
                                                <div class="input-group date" id="to_date_{{ $data['id'] }}" data-target-input="nearest">
                                                    <input id="input_to_date_{{ $data['id'] }}" type="text"  name="to_date" placeholder="To Date" class="form-control datetimepicker-input" data-target="#to_date_{{ $data['id'] }}"/>
                                                    <div class="input-group-append" data-target="#to_date_{{ $data['id'] }}" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div> 
                                            </td>
                                            
                                            <td><button onclick="$('#download_working_hours_{{$data['id']}}').submit()" type="button" class="btn btn-block btn-primary"><i class="fa fa-download"></i> Working Hours Excel</button></td>
                                            

                                            </td>

                                        </tr>
                                        </form>
                                        <?php 
                                                $counter ++;
                                        }
                                        ?>




                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>License No.</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-right"> {{ $driversData->links() }}</div>
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
                        <h3 class="card-title"> Leads Panel</h3>
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
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });

            @foreach ($driver_id_array as $val)
            $('#from_date_{{$val}}').datetimepicker({
                format: 'L'
            });
            $('#to_date_{{$val}}').datetimepicker({
                format: 'L'
            });    
            @endforeach
            
            // $("#example1").DataTable({
            //     "responsive": true,
            //     "lengthChange": false,
            //     "paging": false,
            //     "autoWidth": false,
            //     "info": false,
            //     "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            // }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        });

        function search_driver() {

            searchval = $('#qsearch').val();
            if (searchval.length < 4 && searchval.length > 0) {
                return false;
            }


            var sendInfo = {
                action: 'qsearch_driver',
                qsearch: searchval,
            };

            $.ajax({
                url: "{{ route('drivers.ajaxcall', 1) }}/",
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    if (data.error == 'No') {
                        console.log(data.sql);
                        $('#example1').html(data.response);


                    } else {
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });
                    }
                    driver_ids=data.drivers_id;
                    driver_idsArray = driver_ids.split(',');
                        for (let i = 0; i < driver_idsArray.length; i++) {
                            $('#to_date_'+driver_idsArray[i]).datetimepicker({
                                format: 'L'
                            }); 
                            $('#from_date_'+driver_idsArray[i]).datetimepicker({
                                format: 'L'
                            });
                        }
                       
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
                    url: "{{ url('/admin/customers/ajaxcall/') }}/" + id,
                    data: sendInfo,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
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
    </script>
@endsection
