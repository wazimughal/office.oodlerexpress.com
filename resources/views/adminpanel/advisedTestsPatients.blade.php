@extends('adminpanel.admintemplate')
@push('title')
    <title>Patients| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1>View Reports </h1>
                        
                    </div>
                    <div class="col-sm-2"><a style="width:90%" href="{{url('/admin/patient-reports/create')}}" class="btn btn-block btn-primary btn-lg">Add Report <i class="fa fa-plus"></i></a></div>
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
                                <h3 class="card-title">Reports</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            
                                            <th>ID </th>
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
                                            //  p($usersData);
                                            //  die;
                                        @endphp
                                        @foreach ($TestsDatawithPatient as $data)

                                        @php
                                            $patient=$data['patient'][0]
                                        @endphp
                                       
                                            <tr id="row_{{$data['id']}}">
                                                <td><strong id="id_{{ $patient['id'] }}">{{ $patient['id']}}</strong></td>
                                                <td><strong id="name_{{ $patient['id'] }}">{{ $patient['name'].' S/W '.$patient['gaudian_name'] }}</strong>
                                                </td>
                                               
                                                <td id="phone_{{ $patient['id'] }}">
                                                    {{ $patient['phone'] }}</td>
                                                <td id="address_{{ $patient['id'] }}">
                                                    {{ $patient['address'] }}</td>
                                                <td id="gender_{{ $patient['id'] }}">
                                                    {{ $patient['gender'] }}</td>
                                                <td id="advised_tests_{{ $data['id'] }}">
                                                  @php
                                                  if(!empty($data['advised_tests']))
                                                      $res=getAdvisedTestsNames(json_decode($data['advised_tests'],true));
                                                      echo implode('<br> ', array_column($res, 'test_name'));
                                                //p($data);
                                                  @endphp  
                                                </td>
                                                    <td id="opdno_{{ $data['id'] }}">{{ $data['opdno'] }}</td>
                                                <td id="status_{{ $data['id'] }}">
                                                    @if ($data['status']==1)
                                                    <span @disabled(true) class="btn btn-success btn-flat btn-sm"><i class="fas fa-chart-line"></i>Completed</span>
                                                    @else
                                                    <span @disabled(true) class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i>Awaited</span>
                                                    @endif</td>

                                                <td><a class="btn btn-info btn-block btn-sm" href="{{url('/admin/patient-reports').'/'.$data['id']}}" ><i
                                                            class="fas fa-edit"></i> Edit</a>
                                                            <a href="{{url('/admin/patient-reports/view').'/'.$data['id']}}" class="btn btn-success btn-block btn-sm">
                                                            <i class="fas fa-eye"></i>Report</a>
                                                            <a href="javascript:void(0)" onclick="viewReport({{$data['id']}})"
                                                        class="btn btn-primary btn-block btn-sm"><i
                                                            class="fas fa-eye"></i> View</a>
                                                    <button
                                                        onClick="deleteReport({{ $data['id'] }},{{ $counter }})"
                                                        type="button" class="btn btn-danger btn-block btn-sm"><i
                                                            class="fas fa-trash"></i>
                                                        Delete</button>
                                                      <div style="margin-top: 5px;" id="status_action_btn_{{$data['id']}}">
                                                        @if ($data['status']==1)
                                                        <button
                                                        onClick="changeStatus({{ $data['id'] }},{{ $counter }},'deactivate')"
                                                        type="button" class="btn btn-warning btn-block btn-sm"><i class="fas fa-chart-line"></i>
                                                        Awaited</button>
                                                        @else
                                                        <button
                                                        onClick="changeStatus({{ $data['id'] }},{{ $counter }},'activate')"
                                                        type="button" class="btn btn-success btn-block btn-sm"><i class="fas fa-chart-line"></i>
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
                                                                            {{ $patient['name'] }}</h3>
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
                                                                                    {{ $patient['gaudian_name'] }}</div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>CNIC #:</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $patient['cnic'] }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Phone</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $patient['phone'] }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5"><strong>Address</strong></div>
                                                                                <div class="col-5">
                                                                                    {{ $patient['address'] }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Gender</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ ($patient['gender']=='m')?'Male':'Female' }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Advised Test</strong>
                                                                                </div>
                                                                                <div class="col-5">{!!implode('<br> ', array_column($res, 'test_name'));!!}</div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Advised By</strong>
                                                                                </div>
                                                                                <div class="col-5">{{$data['advised_by']}}</div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5"><strong>Patient Type</strong></div>
                                                                                <div class="col-5">
                                                                                    {{ $data['patient_type'] }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5"><strong>Visit Date</strong></div>
                                                                                <div class="col-5">
                                                                                    {{ date('d/m/Y',$data['prescription_date']) }}
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
                                                $counter ++;
                                            @endphp
                                        @endforeach



                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Name </th>
                                            <th>ID </th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Gender</th>
                                            <th>Advised Tests</th>
                                            <th>OPD NO</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr><td colspan="5">&nbsp;</td><td colspan="4"> {{$TestsDatawithPatient->links()}}</td></tr>
                                    </tfoot>
                                </table>
                                {{-- Pagination --}}
                                
                               
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


        {{-- Here is View Modal --}}
        <div class="modal fade" id="modal-reportResponse">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ $patient['name'] }}</h3>
                            <button type="button" class="close"
                                data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container" id="reportResponse">
                                
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
                "paging": false,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
        // Ajax to view Report with Patient Data
        function viewReport(id) {
            $.ajax({
                    url: "{{ url('/admin/patient-reports/show/') }}/" + id,
                    //data: formData,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error == 'No') {
                            $('#reportResponse').html(data.msg);

                        } else {
                            $('#reportResponse').html('Error');
                        }
                        $('#modal-reportResponse').modal('show');
                        console.log(data);
                        //alert('i am here');
                    }

                });
            }

        
        function deleteReport(id, counter_id) {

            if (confirm("Are you sure you want to delete this?")) {
                $.ajax({
                    url: "{{ url('/admin/patient-reports/delete/') }}/" + id,
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
        function changeStatus(id, counter_id,action) {
            var alertmsg='De-activate'
            if(action=='activate')
            alertmsg='Activate'

            if (confirm("Are you sure you want to "+alertmsg+ " this?")) {

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
