@extends('adminpanel.admintemplate')
@push('title')
    <title>Users| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-2">
                        <h1>View users </h1>
                        
                    </div>
                    <div class="col-sm-2"><a style="width:60%" href="{{url('/admin/users/add')}}" class="btn btn-block btn-success btn-lg">Add New <i class="fa fa-plus"></i></a></div>
                    <div class="col-sm-2">&nbsp;</div>
                    <div class="col-sm-6">
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
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Group</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                         foreach ($usersData as $data){
                                            ?>
                                            <tr id="row_{{$data['id']}}">
                                                <td><strong id="name_{{ $data['id'] }}">{{ $data['name'] }}</strong>
                                                </td>
                                                <td id="email_{{ $data['id'] }}">{{ $data['email'] }}</td>
                                                <td id="phone_{{ $data['id'] }}">
                                                    {{$data['phone']}}</td>
                                                
                                                <td id="group_title_{{ $data['id'] }}">
                                                    {{ $data['getGroups']['title'] }}</td>
                                                <td id="group_role_{{ $data['id'] }}">
                                                    {{ $data['getGroups']['role'] }}</td>
                                                <td id="is_active_{{ $data['id'] }}">
                                                    @if ($data['is_active']==1)
                                                    <a @disabled(true) class="btn btn-success btn-flat btn-sm"><i class="fas fa-chart-line"></i> Active</a>
                                                    @else
                                                    <a @disabled(true) class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i> In-Active</a>
                                                    @endif</td>

                                                <td><a class="btn btn-info btn-block btn-sm" href="{{route('admin.edit_user_form',$data['id'])}}" ><i
                                                            class="fas fa-edit"></i> Edit</a><a data-toggle="modal"
                                                        data-target="#modal-lg-{{ $counter }}"
                                                        class="btn btn-primary btn-block btn-sm"><i
                                                            class="fas fa-eye"></i> View</a>
                                                    <button
                                                        onClick="deleteUser({{ $data['id'] }},{{ $counter }})"
                                                        type="button" class="btn btn-danger btn-block btn-sm"><i
                                                            class="fas fa-trash"></i>
                                                        Delete</button>
                                                      <div style="margin-top: 5px;" id="status_action_btn_{{$data['id']}}">
                                                        @if ($data['is_active']==1)
                                                        <button
                                                        onClick="changeStatus({{ $data['id'] }},{{ $counter }},'deactivate')"
                                                        type="button" class="btn btn-warning btn-block btn-sm"><i class="fas fa-chart-line"></i>
                                                        De-Activate</button>
                                                        @else
                                                        <button
                                                        onClick="changeStatus({{ $data['id'] }},{{ $counter }},'activate')"
                                                        type="button" class="btn btn-success btn-block btn-sm"><i class="fas fa-chart-line"></i>
                                                        Activate</button>
                                                        @endif
                                                      </div>
                                                    </td>

                                                    <div class="modal fade" id="modal-xl-{{ $counter }}">
                                                        <div class="modal-dialog modal-xl">
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
                                                                        <form id="EditUser_{{ $data['id'] }}"
                                                                            method="GET"
                                                                            action="{{ url('/admin/users/update') . '/' . $data['id'] }}"
                                                                            onsubmit="return updateUser({{ $data['id'] }},{{ $counter }})">
                                                                            @csrf

                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="name"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Name"
                                                                                            value="{{ $data['name'] }}"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input readonly type="text"
                                                                                            name="email"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Email"
                                                                                            value="{{ $data['email'] }}"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            
                                                                     
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <select name="group_id"
                                                                                            class="form-control">
                                                                                            @php
                                                                                                $userGroups = getGroups();
                                                                                                foreach ($userGroups as $groupdata) {
                                                                                                    $selected = '';
                                                                                                    if ($groupdata['id'] == $data['getGroups']['id']) {
                                                                                                        $selected = 'selected="selected"';
                                                                                                    }
                                                                                                    echo '<option ' . $selected . ' value="' . $groupdata['id'] . '">' . $groupdata['title'] . '</option>';
                                                                                                }
                                                                                            @endphp
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            {{-- New Row Button --}}
                                                                            <div class="row form-group">
                                                                                <div class="col-5">&nbsp;</div>
                                                                                <div class="col-2">
                                                                                    <button {{-- onclick="updateOrg({{ $data['id }})" --}}
                                                                                        id="update_org" type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i>
                                                                                        Save Changes</button>
                                                                                </div>
                                                                                <div class="col-5">&nbsp;</div>

                                                                            </div>
                                                                        </form>
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
                                                    <!-- /.Edit modal -->
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
                                                                                    <strong>Email</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $data['email'] }}</div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Group</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $data['getGroups']['title'] }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Role</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $data['getGroups']['role'] }}
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
                                            <th>Group</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr><td colspan="8"><div class="text-right"> {{$usersData->links()}}</div></td></tr>
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
                "paging": false,
                "info": false,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
           
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

        
        function deleteUser(id, counter_id) {

            if (confirm("Are you sure you want to delete this?")) {
                $.ajax({
                    url: "{{ url('/admin/users/delete/') }}/" + id,
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
