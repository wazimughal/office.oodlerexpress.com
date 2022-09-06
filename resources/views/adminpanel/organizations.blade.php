@extends('adminpanel.admintemplate')
@push('title')
    <title>Organizations| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View Organizations</h1>
                    </div>
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
                                <h3 class="card-title">ORGANIZATONS</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Facility Name</th>
                                            <th>Facility Head Name</th>
                                            <th>Labortary Name</th>
                                            <th>Head Of Lab Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $counter = 1;
                                        @endphp
                                        @foreach ($organizationsData as $organization)
                                            <tr>
                                                <td><strong
                                                        id="name_{{ $organization->id }}">{{ $organization->name }}</strong>
                                                </td>
                                                <td id="hod_name_{{ $organization->id }}">{{ $organization->hod_name }}
                                                    ({{ $organization->hod_designation }})</td>
                                                <td id="lab_name_{{ $organization->id }}">{{ $organization->lab_name }}
                                                </td>
                                                <td id="lab_hod_name_{{ $organization->id }}">
                                                    {{ $organization->lab_hod_name }}
                                                    ({{ $organization->lab_hod_desination }})
                                                </td>
                                                <td id="email_{{ $organization->id }}">
                                                    {{ $organization->lab_hod_email }}</td>
                                                <td id="lab_hod_phone_{{ $organization->id }}">
                                                    {{ $organization->lab_hod_phone }}</td>
                                                <td><a class="btn btn-danger btn-block btn-sm" data-toggle="modal"
                                                        data-target="#modal-xl-{{ $counter }}"><i
                                                            class="fas fa-edit"></i> Edit</a><a data-toggle="modal"
                                                        data-target="#modal-lg-{{ $counter }}"
                                                        class="btn btn-success btn-block btn-sm"><i
                                                            class="fas fa-eye"></i> View</a>

                                                    <div class="modal fade" id="modal-xl-{{ $counter }}">
                                                        <div class="modal-dialog modal-xl">
                                                            <div class="modal-content">
                                                                <div class="card card-success">
                                                                    <div class="card-header">
                                                                        <h3 class="card-title">
                                                                            {{ $organization->name }}</h3>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form id="EditOrg_{{ $organization->id }}"
                                                                            method="POST"
                                                                            action="{{ url('/admin/organizations/update') . '/' . $organization->id }}"
                                                                            onsubmit="return updateOrg({{ $organization->id }},{{ $counter }})">
                                                                            @csrf

                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="name"
                                                                                            class="form-control @error('name') is-invalid @enderror"
                                                                                            placeholder="Enter Facility Name (e.g DHQ Hospital Okara)"
                                                                                            value="{{ $organization->name }}"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <select name="state"
                                                                                            class="form-control select2 select2-danger"
                                                                                            data-dropdown-css-class="select2-danger"
                                                                                            style="width: 100%;">
                                                                                            <option selected="selected"
                                                                                                value="pubjab">Punjab
                                                                                            </option>
                                                                                            <option value="sindh">Sindh
                                                                                            </option>
                                                                                            <option value="sindh">Sindh
                                                                                            </option>
                                                                                            <option value="kpk">Khyber
                                                                                                Pakhtunkhawa</option>
                                                                                            <option value="balochistan">
                                                                                                Balochistan</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <select name="district"
                                                                                            class="form-control">
                                                                                            <option value="okara">Okara
                                                                                            </option>
                                                                                            <option selected value="vehari">
                                                                                                Vehari</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <select name="tehsil"
                                                                                            class="form-control"
                                                                                            placeholder="Select Tehsil">
                                                                                            <option selected value="vehari">
                                                                                                Vehari</option>
                                                                                            <option value="sindh">Burewala
                                                                                            </option>
                                                                                            <option value="sindh">Mailsi
                                                                                            </option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" required
                                                                                            name="address"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Facility Address (e.g New Sharqi Colony DHQ Hospital Okara)"
                                                                                            value="{{ $organization->address }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-3">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="lang"
                                                                                            class="form-control"
                                                                                            placeholder="Langitude (Optional) e.g: 30.0358172"
                                                                                            value="{{ $organization->lang }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="lat"
                                                                                            class="form-control"
                                                                                            placeholder="Latitude (Optional) e.g: 72.3670309"
                                                                                            value="{{ $organization->lat }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" required
                                                                                            name="hod_name"
                                                                                            class="form-control"
                                                                                            placeholder="Name of Facility Head (e.g Chaudhary Wasim)"
                                                                                            value="{{ $organization->hod_name }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" required
                                                                                            name="hod_designation"
                                                                                            class="form-control"
                                                                                            placeholder="Facility Head Designation (e.g Medical Suprintendent )"
                                                                                            value="{{ $organization->hod_designation }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" required
                                                                                            name="description"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Facility Description (e.g Mission Statement)"
                                                                                            value="{{ $organization->description }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="lab_name"
                                                                                            required class="form-control"
                                                                                            placeholder="Enter Labortary Name (e.g Laboratary of DHQ Hospital Okara)"
                                                                                            value="{{ $organization->lab_name }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" required
                                                                                            name="lab_hod_name"
                                                                                            class="form-control"
                                                                                            placeholder="Head of Labortary Name (e.g Ali Affan Chaudhary)"
                                                                                            value="{{ $organization->lab_hod_name }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" required
                                                                                            name="lab_hod_desination"
                                                                                            class="form-control"
                                                                                            placeholder="Designation of Labortary Head (e.g Pathalogist)"
                                                                                            value="{{ $organization->lab_hod_desination }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" required
                                                                                            name="lab_hod_cnic"
                                                                                            class="form-control"
                                                                                            placeholder="CNIC of Labortary Head (e.g 36603-2794665-7)"
                                                                                            value="{{ $organization->lab_hod_cnic }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" readonly required
                                                                                            name="lab_hod_email"
                                                                                            class="form-control"
                                                                                            placeholder="Email of Labortary Head (e.g pathalogist@gmail.com)"
                                                                                            value="{{ $organization->lab_hod_email }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" required
                                                                                            name="lab_hod_phone"
                                                                                            class="form-control "
                                                                                            placeholder="PhoneNo of Labortary Head (e.g 03007731712)"
                                                                                            value="{{ $organization->lab_hod_phone }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            {{-- New Row Button --}}
                                                                            <div class="row form-group">
                                                                                <div class="col-5">&nbsp;</div>
                                                                                <div class="col-2">
                                                                                    <button {{-- onclick="updateOrg({{ $organization->id }})" --}}
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
                                                                        <button  type="button"
                                                                            class="btn btn-default"
                                                                            data-dismiss="modal">Close</button>
                                                                        {{-- <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button> --}}
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
                                                                            {{ $organization->name }}</h3>
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
                                                                                    <strong>Facility Name</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->name }}</div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Location</strong>
                                                                                </div>
                                                                                <div class="col-5">Teh.
                                                                                    {{ $organization->tehsil }} and
                                                                                    Distt.
                                                                                    {{ $organization->district }},
                                                                                    {{ $organization->state }}</div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Address</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->address }}</div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5"><strong>Name of
                                                                                        Facility Head</strong></div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->hod_name }}
                                                                                    ({{ $organization->hod_designation }})
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Description</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->description }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Labortary Name</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->lab_name }}</div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5">
                                                                                    <strong>Labortary Head Name</strong>
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->lab_hod_name }}
                                                                                    ({{ $organization->lab_hod_desination }})
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5"><strong>HOD
                                                                                        CNIC No.</strong></div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->lab_hod_cnic }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5"><strong>HOD
                                                                                        Email</strong></div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->lab_hod_email }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-1">&nbsp;</div>
                                                                                <div class="col-5"><strong>HOD
                                                                                        Phone</strong></div>
                                                                                <div class="col-5">
                                                                                    {{ $organization->lab_hod_phone }}
                                                                                </div>
                                                                                <div class="col-1">&nbsp;</div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Close</button>
                                                                        {{-- <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button> --}}
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
                                        @endforeach



                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Facility Name</th>
                                            <th>Facility Head Name</th>
                                            <th>Labortary Name</th>
                                            <th>Head Of Lab Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
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
        // Ajax to Update Organization Data
        function updateOrg(id, counter_id = 1) {
            var formData = ($('#EditOrg_' + id).formToJson());
            // console.log(formData);
            $.ajax({
                url: "{{ url('/admin/organizations/update') }}/" + id,
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
                        $('#hod_name_' + data.id).html(data.hod_name);
                        $('#lab_name_' + data.id).html(data.lab_name);
                        $('#lab_hod_name_' + data.id).html(data.lab_hod_name);
                        $('#lab_hod_phone_' + data.id).html(data.lab_hod_phone);

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
                    console.log(data);
                    //alert('i am here');
                }

            });
            return false;
        }

        // $(document).ready(function() {
        // });
    </script>
@endsection
