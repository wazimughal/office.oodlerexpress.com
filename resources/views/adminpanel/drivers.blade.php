@extends('adminpanel.admintemplate')
@push('title')
    <title>
        drivers| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1>View drivers </h1>

                    </div>
                    <div class="col-sm-2">
                        @if ($user->group_id==config('constants.groups.admin'))
                        <a href="{{ route('drivers.openform') }}"
                            class="btn  btn-success">Add New <i class="fa fa-plus"></i></a>    
                        @endif
                         &nbsp; 
                        </div>
                    <div class="col-sm-1">&nbsp;</div>
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
                                <h3 class="card-title">drivers</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>License no</th>
                                            <th>Address</th>
                                            <th>Zip Code</th>
                                            <th>City</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                           
                                            foreach ($driversData as $data){
                                     
                                           
                                            ?>
                                        <tr id="row_{{ $data['id'] }}">
                                            <td><strong id="name_{{ $data['id'] }}">{{ $data['name'] }}</strong>
                                            </td>
                                            <td id="email_{{ $data['id'] }}">{{ $data['email'] }}</td>
                                            <td id="phone_{{ $data['id'] }}">
                                                {{ $data['phone'] }}</td>
                                            <td id="license_no_{{ $data['id'] }}">
                                                {{ $data['license_no'] }} </td>
                                            <td id="address_{{ $data['id'] }}">
                                                {{ $data['address'] }}</td>
                                            <td id="state_{{ $data['id'] }}">
                                                {{ $data['state'] }}</td>
                                            <td id="city_{{ $data['id'] }}">
                                                {{ $data['city'] }}</td>


                                            <td>

                                                
                                                {{-- <button onClick="viewLeadData({{ $data['id'] }},{{ $counter }})"
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                    View</button> --}}
                                                    <a href="{{ url('/admin/drivers/add-documents/' . $data['id']) }}"
                                                    class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i>
                                                    Attach documents</a>

                                                @if ($data['is_active'] == 1 && $user->group_id==config('constants.groups.admin'))
                                               
                                                <a href="{{route('drivers.open_edit_form',$data['id'])}}"
                                                    class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</a>
                                                    <button
                                                        onClick="do_action({{ $data['id'] }},{{ $counter }},'delete')"
                                                        type="button" class="btn btn-danger btn-block btn-sm"><i
                                                            class="fas fa-trash"></i>
                                                        Delete</button>
                                                @elseif ($data['is_active'] == 2 && $user->group_id==config('constants.groups.admin'))
                                                <button
                                                        onClick="do_action({{ $data['id'] }},{{ $counter }},'restore')"
                                                        type="button" class="btn btn-warning btn-block btn-sm"><i
                                                            class="fas fa-undo"></i>
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
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile Number</th>
                                            <th>Lisence Number</th>
                                            <th>Address</th>
                                            <th>Zip Code</th>
                                            <th>City</th>
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
                        <h3 class="card-title"> drivers Panel</h3>
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
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "paging": false,
                "autoWidth": false,
                "info": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        });

        function changeCity() {

            selectOption = $('#city option:selected').text();
            $('#cityname').val(selectOption);

            console.log('option' + selectOption);
            if (selectOption == 'Other') {
                otherCity =
                    '<div class="row form-group"><div class="col-3">&nbsp;</div><div class="col-6"><div class="input-group mb-3"><input  type="text" name="othercity" class="form-control" placeholder="City Name" required></div></div><div class="col-3">&nbsp;</div></div>';
                $('#othercity').html(otherCity);
            } else {
                $('#othercity').html('');
            }
        };

        function changezipcode() {
            selectOption = $('#zipcode_id option:selected').text();
            $('#zipcode_no').val(selectOption);

            if (selectOption == 'Other') {
                otherZipCode =
                    '<div class="row form-group"><div class="col-3">&nbsp;</div><div class="col-6"><div class="input-group mb-3"><input  type="text" name="otherzipcode" class="form-control" placeholder="Please enter Zip Code" required></div></div><div class="col-3">&nbsp;</div></div>';
                $('#otherzipcode').html(otherZipCode);
            } else {
                $('#otherzipcode').html('');
            }
        };


        function do_action(id, counter_id, action) {

            alertMsg = 'Are you sure you want to perform this action?';
            if (action == 'restore')
                alertMsg = 'Are you sure you want to restore this?';
            else if (action == 'delete')
                alertMsg = 'Are you sure you want to Delete this?';

            if (confirm(alertMsg)) {

                var sendInfo = {
                    action: action,
                    counter: counter_id,
                    id: id
                };

                $.ajax({
                    url: "{{ url('/admin/drivers/ajaxcall/') }}/" + id,
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

                    }

                });

            }

        }
    </script>
@endsection
