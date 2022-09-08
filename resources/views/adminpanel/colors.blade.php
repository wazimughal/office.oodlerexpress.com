@extends('adminpanel.admintemplate')
@push('title')
    <title>Add Colors | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View Colors</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">View Colors</li>
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
                                <h3 class="card-title">Add New Colors</h3>
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
                                <form method="POST" action="{{ url('/admin/colors') }}">
                                    @csrf
                                    @foreach ($colorsData as $data )
                                    {{-- <input type="hidden" name="color_id[]" value="{{$data['id']}}"> --}}
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-2">{!!($data['id']==1)?'<label>Title</label>':''!!}<input readonly disabled type="text" name="title[]" class="form-control @error('title') is-invalid @enderror"
                                            placeholder="Title" value="{{ $data['title'] }}">
                                            @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-2">{!!($data['id']==1)?'<label>Background Color</label>':''!!}<input type="text" name="bg_color[{{$data['id']}}][]" required class="my-colorpicker1 form-control @error('bg_color') is-invalid @enderror"
                                            placeholder="Background Color" value="{{ $data['bg_color'] }}">
                                            @error('bg_color')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-2">{!!($data['id']==1)?'<label>Color (e.g #000f24)</label>':''!!}<input type="text" name="color_value[{{$data['id']}}][]" required class="my-colorpicker2 form-control @error('color_value') is-invalid @enderror"
                                            placeholder="Color (e.g #000f24)" value="{{ $data['color_value'] }}">
                                            @error('color_value')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-2">{!!($data['id']==1)?'<label>Lable (e.g danger)</label>':''!!}<input type="text" name="color_for[{{$data['id']}}][]" required class="form-control @error('color_for') is-invalid @enderror"
                                            placeholder="Lable (e.g danger)" value="{{ $data['color_for'] }}">
                                            @error('color_for')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-2">{!!($data['id']==1)?'<label>Any note..</label>':''!!}<input type="text" name="description[{{$data['id']}}][]" class="form-control @error('description') is-invalid @enderror"
                                            placeholder="Any note.." value="{{ $data['description'] }}">
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                    </div>
                                    @endforeach
                                    
                                    {{-- New Row Button --}}
                                    <div class="row form-group">
                                        <div class="col-5">&nbsp;</div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i
                                                    class="fa fa-save"></i> Save</button>
                                        </div>
                                        <div class="col-5">&nbsp;</div>

                                    </div>
                                </form>
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
   <!-- Select2 -->
      <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
      <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
      <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="{{ url('adminpanel/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
@endsection
@section('footer-js-css')

 <!-- Select2 -->
 <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
 <!-- bootstrap color picker -->
<script src="{{ url('adminpanel/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
 <script>
    $(function() {
        $('.select2bs4').select2({
            theme: 'bootstrap4'
            });
            // Shorthand for $( document ).ready()

                //Colorpicker
                $('.my-colorpicker1').colorpicker()
                //color picker with addon
                $('.my-colorpicker2').colorpicker()

                $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
                })

                $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
                })
       
        });
        </script>
 @endsection