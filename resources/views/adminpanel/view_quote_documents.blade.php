@extends('adminpanel.admintemplate')
@push('title')
    <title>View Requested Quolte Documents | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View Requested Quolte Documents</h1>
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
                                <div class="row">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-6">
                                        <!-- flash-message -->
                                        <div class="flash-message">
                                            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                                @if (Session::has('alert-' . $msg))
                                                    <p class="alert alert-{{ $msg }}">
                                                        {{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                                                            data-dismiss="alert" aria-label="close">&times;</a></p>
                                                @endif
                                            @endforeach
                                        </div> <!-- end .flash-message -->
                                    </div>
                                    <div class="col-3">&nbsp;</div>
                                </div>
                                @php
                                    //$quotesData['customer'] = $quotesData['customer'][0];
                                    
                                @endphp
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>PO Number :</strong></div>
                                    <div class="col-3">{{ $quotesData['po_number'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Email :</strong></div>
                                    <div class="col-3">{{ $quotesData['customer']['email'] }}</div>
                                </div>
                              
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Mobile No :</strong></div>
                                    <div class="col-3">{{ $quotesData['customer']['mobileno'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Business Name :</strong></div>
                                    <div class="col-3">{{ $quotesData['customer']['business_name'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Designation :</strong></div>
                                    <div class="col-3">{{ $quotesData['customer']['designation'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Zip Code :</strong></div>
                                    <div class="col-3">{{ $quotesData['customer']['zipcode'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-3"> <strong>Address :</strong></div>
                                    <div class="col-3">{{ $quotesData['customer']['business_address'] }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-1">&nbsp;</div>
                                    <div class="col-10">
                                        <hr>
                                    </div>
                                </div>


                                <div class="row form-group">
                                    <div class="col-1">&nbsp;</div>
                                    <div class="col-10">
                                        <div class="row form-group">
                                            <?php
                                     $imagesTypes=array('jpg','jpeg','png','gif');
                                     $excelTypes=array('xls','xlsx');
                                     $docTypes=array('doc','docx');
                                     //p($quotesData['document_for_request_quote']);
                                        foreach($quotesData['document_for_request_quote'] as $data){
                                          if(in_array($data['otherinfo'],$imagesTypes))
                                            $thumb_img=$data['path'];
                                          else if(in_array($data['otherinfo'],$excelTypes))
                                            $thumb_img=url('adminpanel/dist/img/xls.jpeg');
                                          else if(in_array($data['otherinfo'],$docTypes))
                                            $thumb_img=url('adminpanel/dist/img/doxx.png');
                                          else if($data['otherinfo']=='pdf')
                                          $thumb_img=url('adminpanel/dist/img/pdf.png');
                                            ?>
                                            <div id="file_{{ $data['id'] }}" class="col-3 text-center"
                                                style="position: relative;">
                                                <label class="">{{ $data['name'] }}</label>
                                                {{-- <i onclick="removeFile({{ $data['id'] }})"
                                                    style="position: absolute; top:15px; right:0px; cursor:pointer"
                                                    class="fas fa-times"></i> --}}
                                                <a href="{{ $data['path'] }}" target="_blank"><img
                                                        class="w-100 shadow-1-strong rounded mb-4 img-thumbnail"
                                                        src="{{ isset($thumb_img)?$thumb_img:'' }}" width="200" alt="Uploaded Image"></a>
                                            </div>


                                            <?php 
                                          }
                                      ?>



                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
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
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx,.xls,.csv"
        });


        function removeFile(id) {


            if (confirm('Are you sure? you want to delete this file?')) {

                var sendInfo = {
                    action: 'delteFile',
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
