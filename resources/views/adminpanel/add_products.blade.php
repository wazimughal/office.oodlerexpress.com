@extends('adminpanel.admintemplate')
@push('title')
    <title>Add products | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add New products</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add New product</li>
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
                                <h3 class="card-title">Add New products</h3>
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
                                <form method="POST" action="{{ route('products.add') }}">
                                    @csrf
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                          <div class="input-group mb-3">
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Product Name" value="{{ old('name') }}">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                    
                                        </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                          <div class="input-group mb-3">
                                            <textarea type="text" name="sizes" class="form-control @error('sizes') is-invalid @enderror"
                                                placeholder="Product Sizes (e.g:  Size 1 Feet, 3 Feet ,9 Feet  )" >{{ old('sizes');}}</textarea>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-building"></span>
                                                </div>
                                            </div>
                                            @error('sizes')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                    
                                        </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                          <div class="input-group mb-3">
                                            <textarea type="text" name="additional_notes" class="form-control @error('additional_notes') is-invalid @enderror"
                                                placeholder="Addition Notes" >{{ old('additional_notes');}}</textarea>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-building"></span>
                                                </div>
                                            </div>
                                            @error('additional_notes')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                    
                                        </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                            <select id="cat_id" onChange="changeProCategory()" name="cat_id" class="form-control select2bs4 @error('city') is-invalid @enderror" placeholder="Select City">@php echo getProductCatOptions(); @endphp</select>
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div id="other_cat"></div>
                                    
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
@endsection
@section('footer-js-css')

 <!-- Select2 -->
 <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
 <script>
    $(function() {
        $('.select2bs4').select2({
            theme: 'bootstrap4'
            });
            // Shorthand for $( document ).ready()
       
        });
        function changeProCategory() {
            selectOption = $('#cat_id option:selected').text();
            
            if (selectOption == 'Other') {
                otherCity ='<div class="row form-group"><div class="col-3">&nbsp;</div><div class="col-6"><div class="input-group mb-3"><input  type="text" name="other_category" class="form-control" placeholder="Please enter Category Name" required></div></div><div class="col-3">&nbsp;</div></div>';
                $('#other_cat').html(otherCity);
            } else {
                $('#other_cat').html('');
            }
        };
        
        </script>
 @endsection