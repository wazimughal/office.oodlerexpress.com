@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Patients| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1>View Patient Reports </h1>

                    </div>
                    <div class="col-sm-2"><a style="width:90%" href="{{ url('/admin/patient-reports/create') }}"
                            class="btn btn-block btn-primary btn-lg">Add Report <i class="fa fa-plus"></i></a></div>
                    <div class="col-sm-2">&nbsp;</div>
                    <div class="col-sm-5">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">View</li>
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
                                <h3 class="card-title">Users</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>

                                            <th>Name </th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Gender</th>
                                            <th>Advised Tests</th>
                                            <th>OPD NO</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $counter = 1;
                                            p($usersData);
                                            die();
                                        @endphp
                                        @foreach ($PatientWithAdvisedTests as $data)
                                            <tr id="row_{{ $data['id'] }}">

                                                <td><strong
                                                        id="name_{{ $data['id'] }}">{{ $data['name'] . ' S/W ' . $data['gaudian_name'] }}</strong>
                                                </td>

                                                <td id="phone_{{ $data['id'] }}">
                                                    {{ $data['phone'] }}</td>
                                                <td id="address_{{ $data['id'] }}">
                                                    {{ $data['address'] }}</td>
                                                <td id="gender_{{ $data['id'] }}">
                                                    {{ $data['gender'] }}</td>
                                                <td id="advised_tests_{{ $data['id'] }}">
                                                    @php
                                                        
                                                        if (empty($data['get_advised_tests'])) {
                                                            $res = [];
                                                            $data['get_advised_tests'][0]['advised_tests'] = '';
                                                            $data['get_advised_tests'][0]['opdno'] = '';
                                                            $data['get_advised_tests'][0]['prescription_date'] = time();
                                                            $data['get_advised_tests'][0]['prescription_srno'] = '';
                                                            $data['get_advised_tests'][0]['patient_type'] = '';
                                                            $data['get_advised_tests'][0]['advised_by'] = '';
                                                            $data['get_advised_tests'][0]['status'] = 0;
                                                            $data['get_advised_tests'][0]['id'] = '';
                                                        }
                                                        if (!empty($data['get_advised_tests'][0]['advised_tests']) && is_array()) {
                                                            $res = getAdvisedTestsNames(json_decode($data['get_advised_tests'][0]['advised_tests'], true));
                                                        }
                                                        echo implode('<br> ', array_column($res, 'test_name'));
                                                        //p($data);
                                                    @endphp
                                                </td>
                                                <td id="opdno_{{ $data['id'] }}">
                                                    {{ $data['get_advised_tests'][0]['opdno'] }}</td>
                                                <td id="status_{{ $data['id'] }}">
                                                    @if ($data['get_advised_tests'][0]['status'] == 1)
                                                        <span @disabled(true)
                                                            class="btn btn-success btn-flat btn-sm"><i
                                                                class="fas fa-chart-line"></i>Completed</span>
                                                    @else
                                                        <span @disabled(true)
                                                            class="btn bg-gradient-secondary btn-flat btn-sm"><i
                                                                class="fas fa-chart-line"></i>Awaited</span>
                                                    @endif
                                                </td>

                                                <td><a class="btn btn-info btn-block btn-sm"
                                                        href="{{ url('/admin/patient-reports') . '/' . $data['get_advised_tests'][0]['id'] }}"><i
                                                            class="fas fa-edit"></i> Edit</a><a data-toggle="modal"
                                                        data-target="#modal-lg-{{ $counter }}"
                                                        class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                        View</a>
                                                    <button
                                                        onClick="deleteReport({{ $data['get_advised_tests'][0]['id'] }},{{ $counter }})"
                                                        type="button" class="btn btn-danger btn-block btn-sm"><i
                                                            class="fas fa-trash"></i>
                                                        Delete</button>
                                                    <div style="margin-top: 5px;"
                                                        id="status_action_btn_{{ $data['id'] }}">
                                                        @if ($data['get_advised_tests'][0]['status'] == 1)
                                                            <button
                                                                onClick="changeStatus({{ $data['id'] }},{{ $counter }},'deactivate')"
                                                                type="button" class="btn btn-warning btn-block btn-sm"><i
                                                                    class="fas fa-chart-line"></i>
                                                                Awaited</button>
                                                        @else
                                                            <button
                                                                onClick="changeStatus({{ $data['id'] }},{{ $counter }},'activate')"
                                                                type="button" class="btn btn-success btn-block btn-sm"><i
                                                                    class="fas fa-chart-line"></i>
                                                                Complete</button>
                                                        @endif
                                                    </div>
                                                </td>
                                                {{-- Here is View Modal --}}
                                                <div class="modal fade" id="modal-lg-{{ $counter++ }}">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="card card-success">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">
                                                                        {{ $data['name'] }}</h3>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="container">
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5">
                                                                                <strong>Gaurdian Name</strong>
                                                                            </div>
                                                                            <div class="col-5">
                                                                                {{ $data['gaudian_name'] }}</div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5">
                                                                                <strong>CNIC #:</strong>
                                                                            </div>
                                                                            <div class="col-5">
                                                                                {{ $data['cnic'] }}
                                                                            </div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5">
                                                                                <strong>Phone</strong>
                                                                            </div>
                                                                            <div class="col-5">
                                                                                {{ $data['phone'] }}
                                                                            </div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5"><strong>Address</strong>
                                                                            </div>
                                                                            <div class="col-5">
                                                                                {{ $data['address'] }}
                                                                            </div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5">
                                                                                <strong>Gender</strong>
                                                                            </div>
                                                                            <div class="col-5">
                                                                                {{ $data['gender'] == 'm' ? 'Male' : 'Female' }}
                                                                            </div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5">
                                                                                <strong>Advised Test</strong>
                                                                            </div>
                                                                            <div class="col-5">{!! implode('<br> ', array_column($res, 'test_name')) !!}
                                                                            </div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5">
                                                                                <strong>Advised By</strong>
                                                                            </div>
                                                                            <div class="col-5">
                                                                                {{ $data['get_advised_tests'][0]['advised_by'] }}
                                                                            </div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5"><strong>Patient
                                                                                    Type</strong></div>
                                                                            <div class="col-5">
                                                                                {{ $data['get_advised_tests'][0]['patient_type'] }}
                                                                            </div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1">&nbsp;</div>
                                                                            <div class="col-5"><strong>Visit Date</strong>
                                                                            </div>
                                                                            <div class="col-5">
                                                                                {{ date('d/m/Y', $data['get_advised_tests'][0]['prescription_date']) }}
                                                                            </div>
                                                                            <div class="col-1">&nbsp;</div>
                                                                        </div>


                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default"
                                                                        data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                </div>
                                                <!-- /.modal -->

                                                </td>

                                            </tr>
                                            @php
                                                $counter++;
                                            @endphp
                                        @endforeach



                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Name </th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Gender</th>
                                            <th>Advised Tests</th>
                                            <th>OPD NO</th>
                                            <th>Status</th>
                                            <th>Action</th>
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
@endsection

@section('head-js-css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ url('adminpanel/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
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
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
        // Ajax to Update user Data
        function updateUser(id, counter_id = 1) {
            var formData = ($('#EditUser_' + id).formToJson());
            // console.log(formData);
            $.ajax({
                url: "{{ url('/admin/users/update') }}/" + id,
                data: formData,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        $('#name_' + data.id).html(data.name);
                        $('#group_title_' + data.id).html(data.group_title);
                        $('#group_role_' + data.id).html(data.group_role);
                        // Close modal and success Message

                        $('#modal-xl-' + counter_id).modal('toggle');
                        $(document).Toasts('create', {
                            class: 'bg-success',
                            title: data.name,
                            subtitle: 'record',
                            body: data.msg
                        });


                    } else {
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.name,
                            subtitle: 'record',
                            body: data.msg
                        });
                    }
                }
            });
            return false;
        }


        function deleteReport(id, counter_id) {

            if (confirm("Are you sure you want to delete this?")) {
                $.ajax({
                    url: "{{ url('/admin/patient-reports/destroy/') }}/" + id,
                    //data: formData,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error == 'No') {
                            // Close modal and success Message
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

        function changeStatus(id, counter_id, action) {
            var alertmsg = 'De-activate'
            if (action == 'activate')
                alertmsg = 'Activate'

            if (confirm("Are you sure you want to " + alertmsg + " this?")) {

                var sendInfo = {
                    action: action,
                    counter: counter_id,
                    id: id
                };

                $.ajax({
                    url: "{{ url('/admin/users/changestatus/') }}/" + id,
                    data: sendInfo,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error == 'No') {
                            // Close modal and success Message
                            $('#is_active_' + id).html(data.status_btn);
                            $('#status_action_btn_' + id).html(data.status_action_btn);

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

        // $(document).ready(function() {
        // });
    </script>
@endsection
