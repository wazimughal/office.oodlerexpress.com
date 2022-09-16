@extends('adminpanel.admintemplate')
@push('title')
    <title>
        categories| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1>View categories </h1>

                    </div>
                    <div class="col-sm-2"><button style="width:60%" onclick="addCategoryForm()" 
                            class="btn btn-block btn-success btn-lg">Add New <i class="fa fa-plus"></i></button></div>
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
                                <h3 class="card-title">categories</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                           
                                            foreach ($categoriesData as $data){
                                     
                                           
                                            ?>
                                        <tr id="row_{{ $data['id'] }}">
                                            <td><strong id="name_{{ $data['id'] }}">{{ $data['name'] }}</strong></td>
                                            
                                            <td>
                                                <button onClick="editCategoryForm({{ $data['id'] }},{{ $counter }})"
                                                    class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</button>
                                                
                                                <button
                                                    onClick="delete_category({{ $data['id'] }},{{ $counter }},'delete')"
                                                    type="button" class="btn btn-danger btn-block btn-sm"><i
                                                        class="fas fa-trash"></i>
                                                    Delete</button>
                                                
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
                                           
                                           
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-right"> {{ $categoriesData->links() }}</div>
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
                        <h3 class="card-title"> categories Panel</h3>
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
        // Open Product Edit Form
        function addCategoryForm() {

            var sendInfo = {
                action: 'addCategoryForm',
                
            };
            
            $.ajax({
                url: "{{ url('/admin/products/categoryajaxcall') }}",
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        $('#responseData').html(data.formdata);
                       
                    } else {
                        $('#responseData').html('There is some error, Please try again Later');
                    }
                    $('#modal-xl-lead').modal('toggle')
                }
            });
            return false;
        }
        // Open Product Edit Form
        function editCategoryForm(id, counter_id = 1) {

            var sendInfo = {
                action: 'editCategoryForm',
                counter: counter_id,
                status: status,
                id: id
            };
            
            $.ajax({
                url: "{{ url('/admin/products/ajaxcall') }}/" + id,
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        $('#responseData').html(data.formdata);
                        $('.select2bs4').select2({
                            theme: 'bootstrap4'
                        });
                    } else {
                        $('#responseData').html('There is some error, Please try again Later');
                    }
                    $('#modal-xl-lead').modal('toggle')
                }
            });
            return false;
        }
        // Ajax to Update Category Edit POP UP Data
        function update_category_form_data(id, counter_id = 1) {
            var formData = ($('#edit_categories_form').formToJson());
             console.log(formData);
            $.ajax({
                url: "{{ url('/admin/products/ajaxcall') }}/" + id,
                data: formData,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        console.log(data);

                        $('#name_' + data.id).html(data.name);
                        
                        // // Close modal and success Message
                        $('#modal-xl-lead').modal('toggle')

                        $(document).Toasts('create', {
                            class: 'bg-success',
                            title: data.title,
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
      
        // Ajax to Update Category Edit POP UP Data
        function add_category_form_data() {
            var formData = ($('#add_categories_form').formToJson());
             id=1;
            $.ajax({
                url: "{{ url('/admin/products/ajaxcall') }}/" + id,
                data: formData,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        console.log(data);

                        $('#name_' + data.id).html(data.name);
                        
                        // // Close modal and success Message
                        $('#modal-xl-lead').modal('toggle')

                        $(document).Toasts('create', {
                            class: 'bg-success',
                            title: data.title,
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
                    window.location="";
                }
            });
            return false;
        }
      

   
      

        function delete_category(id, counter_id, action) {

           
                alertMsg = 'Are you sure you want to Delete this?';

            if (confirm(alertMsg)) {

                var sendInfo = {
                    action: 'delete_category',
                    counter: counter_id,
                    id: id
                };

                $.ajax({
                    url: "{{ url('/admin/products/ajaxcall/') }}/" + id,
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

        // $(document).ready(function() {
        // });
    </script>
@endsection
