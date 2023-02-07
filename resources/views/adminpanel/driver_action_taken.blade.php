@extends('adminpanel.template')
@push('title')
    <title>Add document for drivers | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add New Documents for driver</h1>
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
                                <h3 class="card-title">Add New documents</h3>
                            </div>
                            <div class="card-body">
                                
                                @php
                                    $userData = $userData[0];
                                    
                                @endphp
                               
                                <div class="row form-group">
                                    <div class="col-1">&nbsp;</div>
                                    <div class="col-10 card card-default">
                                        <div class="card-header">
                                            <h3 class="card-title">Upload Documents: <small> <em> <strong>Click!</strong> in
                                                        box and upload files.</small></h3>
                                        </div>
                                        <form
                                            action="{{ route('drivers.driver_action_files',$userData['id']) }}"
                                            method="post" enctype="multipart/form-data" id="image-upload"
                                            class="dropzone ">
                                            @csrf
                                            <div>
                                                <h4 class="form-label">Upload Multiple Files By Click On Box</h4>
                                            </div>


                                        </form>
                                        <div class="card-footer">
                                            You can select multiple files (e.g images, .docx , .xls ,.csv, .pdf ) and upload
                                            in {{ $userData['name'] }}
                                            ({{ Str::ucfirst($userData['get_groups']['title']) }}) document dorictory.
                                        </div>
                                    </div>
                                    <div class="col-3">&nbsp;</div>

                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>

                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('head-js-css')
    <!-- dropzonejs -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.css') }}">
@endsection
@section('footer-js-css')
    <!-- dropzonejs -->
    <script src="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;

        var myDropzone = new Dropzone('#image-upload', {
            thumbnailWidth: 200,
            maxFilesize: 1,
            acceptedFiles: ".php,.jpeg,.jpg,.png,.gif,.pdf,.doc,.docx,.xls,.csv"
        });


        function removeFile(id) {


            if (confirm('Are you sure? you want to delete this file?')) {

                var sendInfo = {
                    action: 'delteFile',
                    id: id
                };

                $.ajax({
                    url: "{{ route('drivers.ajaxcall') }}/" + id+"/?time={{time()}}",
                    data: sendInfo,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
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

        }
    </script>
@endsection
