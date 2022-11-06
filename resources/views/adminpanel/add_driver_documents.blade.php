@extends('adminpanel.admintemplate')
@push('title')
    <title>Add drivers | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add New drivers</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add New driver</li>
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
                                <h3 class="card-title">Add New drivers</h3>
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-6">
                                 <!-- flash-message -->
                                <div class="flash-message">
                                  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                    @if(Session::has('alert-' . $msg))
                              
                                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                    @endif
                                  @endforeach
                                </div> <!-- end .flash-message -->
                                </div>
                                <div class="col-3">&nbsp;</div>
                              </div>
                      @php
                        $userData=$userData[0];
                        //p($userData);
                    
                      @endphp
                              <div class="row form-group">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-3"> <strong>Name :</strong></div>
                                <div class="col-3">{{$userData['name']}}</div>
                              </div>
                              <div class="row form-group">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-3"> <strong>Email :</strong></div>
                                <div class="col-3">{{$userData['email']}}</div>
                              </div>
                              <div class="row form-group">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-3"> <strong>User :</strong></div>
                                <div class="col-3">{{(Str::ucfirst($userData['get_groups']['title']))}}</div>
                              </div>
                              <div class="row form-group">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-3"> <strong>Mobile No :</strong></div>
                                <div class="col-3">{{$userData['mobileno']}}</div>
                              </div>
                              <div class="row form-group">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-3"> <strong>License No :</strong></div>
                                <div class="col-3">{{$userData['license_no']}}</div>
                              </div>
                              <div class="row form-group">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-3"> <strong>City :</strong></div>
                                <div class="col-3">{{$userData['city']['name']}}</div>
                              </div>
                              <div class="row form-group">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-3"> <strong>Zip Code :</strong></div>
                                <div class="col-3">{{$userData['zip_code']['code']}}</div>
                              </div>
                              <div class="row form-group">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-3"> <strong>Address :</strong></div>
                                <div class="col-3">{{$userData['address']}}</div>
                              </div>

                                         
         <form method="post" action="{{ route('dropzone.store') }}" enctype="multipart/form-data" id="image-upload" >                       <!-- /.row -->
          @csrf
          <div class="row">
          <div class="col-md-1">&nbsp;</div>
            <div class="col-md-10">
              <div class="card card-default">
                <div class="card-header">
                  <h3 class="card-title">Upload Documents:   <small> <em> <strong>Click!</strong> Add file -> Start Upload</em> (Cancel to Stop uploading file)</small></h3>
                </div>
                <div class="card-body">
                  <div id="actions" class="row">
                    <div class="col-lg-6">
                      <div class="btn-group w-100">
                        <span class="btn btn-success col fileinput-button">
                          <i class="fas fa-plus"></i>
                          <span>Add files</span>
                        </span>
                        <button type="submit" class="btn btn-primary col start">
                          <i class="fas fa-upload"></i>
                          <span>Start upload</span>
                        </button>
                        <button type="reset" class="btn btn-warning col cancel">
                          <i class="fas fa-times-circle"></i>
                          <span>Cancel upload</span>
                        </button>
                      </div>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center">
                      <div class="fileupload-process w-100">
                        <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table table-striped files" id="previews">
                    <div id="template" class="row mt-2">
                      <div class="col-auto">
                          <span class="preview"><img src="data:," alt="" data-dz-thumbnail /></span>
                      </div>
                      <div class="col d-flex align-items-center">
                          <p class="mb-0">
                            <span class="lead" data-dz-name></span>
                            (<span data-dz-size></span>)
                          </p>
                          <strong class="error text-danger" data-dz-errormessage></strong>
                      </div>
                      <div class="col-4 d-flex align-items-center">
                          <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                          </div>
                      </div>
                      <div class="col-auto d-flex align-items-center">
                        <div class="btn-group">
                          <button class="btn btn-primary start">
                            <i class="fas fa-upload"></i>
                            <span>Start</span>
                          </button>
                          <button data-dz-remove class="btn btn-warning cancel">
                            <i class="fas fa-times-circle"></i>
                            <span>Cancel</span>
                          </button>
                          <button data-dz-remove class="btn btn-danger delete">
                            <i class="fas fa-trash"></i>
                            <span>Delete</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  You can select multiple files (e.g Picture, .docx , .xls ,.csv, .pdf ) and upload in {{$userData['name']}} ({{(Str::ucfirst($userData['get_groups']['title']))}}) document doriectory. 
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>
         </form>
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
   

  // DropzoneJS Demo Code Start
  Dropzone.autoDiscover = false

// Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
var previewNode = document.querySelector("#template")
previewNode.id = ""
var previewTemplate = previewNode.parentNode.innerHTML
previewNode.parentNode.removeChild(previewNode)

var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
  url: "{{url('/admin/drivers/upload-documents/'.$userData['id'])}}", // Set the url
  thumbnailWidth: 80,
  thumbnailHeight: 80,
  parallelUploads: 20,
  previewTemplate: previewTemplate,
  autoQueue: false, // Make sure the files aren't queued until manually added
  previewsContainer: "#previews", // Define the container to display the previews
  clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
})

myDropzone.on("addedfile", function(file) {
  // Hookup the start button
  console.log('file :'+file);
  file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file) }
})

// Update the total progress bar
myDropzone.on("totaluploadprogress", function(progress) {
  document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
})

myDropzone.on("sending", function(file) {
  // Show the total progress bar when upload starts
  document.querySelector("#total-progress").style.opacity = "1"
  // And disable the start button
  file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
})

// Hide the total progress bar when nothing's uploading anymore
myDropzone.on("queuecomplete", function(progress) {
  document.querySelector("#total-progress").style.opacity = "0"
})

// Setup the buttons for all transfers
// The "add files" button doesn't need to be setup because the config
// `clickable` has already been specified.
document.querySelector("#actions .start").onclick = function() {
  myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
}
document.querySelector("#actions .cancel").onclick = function() {
  myDropzone.removeAllFiles(true)
}
// DropzoneJS Demo Code End
        </script>
 @endsection