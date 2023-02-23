@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Customers| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1>View Customers </h1>

                    </div>
                    <div class="col-sm-3">
                        @if ($user->group_id == config('constants.groups.admin'))
                            <a href="{{ route('admin.customersaddform') }}"
                                class="btn  btn-success ">Add New <i class="fa fa-plus"></i></a>
                        @endif

                    </div>
                    
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
                                <h3 class="card-title">Customers</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-lg-4 col-sm-8 col-xs-12">
                                        <input class="form-control" onkeyup="search_customers()" type="text"
                                            id="qsearch" name="qsearch"
                                            placeholder="Type email or customer or business  name to search">
                                    </div>
                                </div>
                                <table id="example1" class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Business Name</th>
                                            <th>Business Address</th>
                                            <th>Business Phone</th>
                                            <th>Lead By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            foreach ($customersData as $data){
                                            ?>
                                        <tr id="row_{{ $data['id'] }}">
                                            <td><strong id="name_{{ $data['id'] }}">{{ $data['name'] }}</strong>
                                            </td>
                                            <td id="email_{{ $data['id'] }}">{{ $data['email'] }}</td>
                                            <td id="mobileno_{{ $data['id'] }}">
                                                {{ $data['mobileno'] }}</td>
                                            <td id="business_name_{{ $data['id'] }}">
                                                {{ $data['business_name'] }}</td>
                                            <td id="business_address_{{ $data['id'] }}">
                                                {{ $data['business_address'] }}
                                            </td>
                                            <td id="business_phone_{{ $data['id'] }}">
                                                {{ $data['business_phone'] }}
                                            </td>
                                            <td id="status{{ $data['id'] }}">
                                                @if ($data['lead_by'] == 0)
                                                    <a @disabled(true)
                                                        class="btn btn-success btn-flat btn-sm"><i
                                                            class="fas fa-chart-line"></i> Office</a>
                                                @else
                                                    <a @disabled(true)
                                                        class="btn bg-gradient-secondary btn-flat btn-sm"><i
                                                            class="fas fa-chart-line"></i> Website</a>
                                                @endif
                                            </td>
                                            <td>


                                                @if ($user->group_id == config('constants.groups.admin'))
                                                    <a href="{{ route('delivery.add_delivery_form', $data['id']) }}"
                                                        class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Delivery</a>
                                                    <a href="{{ route('customer.quotes', $data['id']) }}"
                                                        class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i> Quotes</a>

                                                    @if ($data['is_active'] == 2)
                                                        <button
                                                            onClick="do_action({{ $data['id'] }},'restore',{{ $counter }})"
                                                            type="button" class="btn btn-info btn-block btn-sm"><i
                                                                class="fas fa-chart-line"></i>
                                                            Restore</button>
                                                    @else
                                                        <a href="{{ route('admin.customerseditform', $data['id']) }}"
                                                            class="btn btn-info btn-block btn-sm"><i
                                                                class="fas fa-edit"></i>
                                                            Edit</a>
                                                        <button
                                                            onClick="do_action({{ $data['id'] }},'delete',{{ $counter }})"
                                                            type="button" class="btn btn-danger btn-block btn-sm"><i
                                                                class="fas fa-trash"></i>
                                                            Delete</button>
                                                    @endif
                                                @else
                                                    <a href="{{ route('admin.customerseditform', $data['id']) }}"
                                                        class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                        Edit</a>
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
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Business Name</th>
                                            <th>Business Address</th>
                                            <th>Business Phone</th>
                                            <th>Lead By</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-right"> {{ $customersData->links() }}</div>
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
    <!-- DataTables  & Plugins -->
    <script src="{{ url('adminpanel/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
            // $("#example1").DataTable({
            //     "responsive": true,
            //     "lengthChange": false,
            //     "paging": false,
            //     "autoWidth": false,
            //     "info": false,
            //     "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            // }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        });

        function search_customers() {

            searchval = $('#qsearch').val();
            if (searchval.length < 4 && searchval.length > 0) {
                return false;
            }


            var sendInfo = {
                action: 'qsearch_customer',
                qsearch: searchval,
            };

            $.ajax({
                url: "{{ route('admin.customers.ajaxcall', 1) }}/",
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
                        //alert('i am here');
                    }
                });
            }
        }
    </script>
@endsection
