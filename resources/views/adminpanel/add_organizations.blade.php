@extends('adminpanel.admintemplate')
@push('title')
    <title>Add Test | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add New Test</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add New Test</li>
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
                                <h3 class="card-title">Add New Test</h3>
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
                                <form method="POST" action="{{ url('/admin/organizations/add') }}">
                                    @csrf
                                   
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    placeholder="Enter Facility Name (e.g DHQ Hospital Okara)"
                                                    value="{{ old('name') }}">
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
                                              <select name="state" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 100%;">
                                                <option selected="selected" value="pubjab">Punjab</option>
                                                <option value="sindh">Sindh</option>
                                                <option value="sindh">Sindh</option>
                                                <option value="kpk">Khyber Pakhtunkhawa</option>
                                                <option value="balochistan">Balochistan</option>
                                              </select>
                                                @error('state')
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
                                              <select name="district" class="form-control">
                                                <option value="okara">Okara</option>
                                                <option selected value="vehari">Vehari</option>
                                              </select>
                                                @error('district')
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
                                              <select name="tehsil" class="form-control" placeholder="Select Tehsil">
                                                <option selected value="vehari">Vehari</option>
                                                <option value="sindh">Burewala</option>
                                                <option value="sindh">Mailsi</option>
                                              </select>
                                                @error('tehsil')
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
                                                <input type="text" name="address"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    placeholder="Enter Facility Address (e.g New Sharqi Colony DHQ Hospital Okara)"
                                                    value="{{ old('address') }}">
                                                @error('address')
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
                                      <div class="col-3">
                                          <div class="input-group mb-3">
                                              <input type="text" name="lang"class="form-control" placeholder="Langitude (Optional) e.g: 30.0358172"value="{{ old('lang') }}">
                                          </div>
                                      </div>
                                      <div class="col-3">
                                        <div class="input-group mb-3">
                                            <input type="text" name="lat"class="form-control" placeholder="Latitude (Optional) e.g: 72.3670309"value="{{ old('lat') }}">
                                        </div>
                                    </div>
                                      <div class="col-3">&nbsp;</div>
                                  </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="hod_name"
                                                    class="form-control @error('hod_name') is-invalid @enderror"
                                                    placeholder="Name of Facility Head (e.g Chaudhary Wasim)"
                                                    value="{{ old('hod_name') }}">
                                                @error('hod_name')
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
                                                <input type="text" name="hod_designation"
                                                    class="form-control @error('hod_designation') is-invalid @enderror"
                                                    placeholder="Facility Head Designation (e.g Medical Suprintendent )"
                                                    value="{{ old('hod_designation') }}">
                                                @error('hod_designation')
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
                                                <input type="text" name="description"
                                                    class="form-control @error('description') is-invalid @enderror"
                                                    placeholder="Enter Facility Description (e.g Mission Statement)"
                                                    value="{{ old('description') }}">
                                                @error('description')
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
                                                <input type="text" name="lab_name"
                                                    class="form-control @error('lab_name') is-invalid @enderror"
                                                    placeholder="Enter Labortary Name (e.g Laboratary of DHQ Hospital Okara)"
                                                    value="{{ old('lab_name') }}">
                                                @error('lab_name')
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
                                                <input type="text" name="lab_hod_name"
                                                    class="form-control @error('lab_hod_name') is-invalid @enderror"
                                                    placeholder="Head of Labortary Name (e.g Ali Affan Chaudhary)"
                                                    value="{{ old('lab_hod_name') }}">
                                                @error('lab_hod_name')
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
                                                <input type="text" name="lab_hod_desination"
                                                    class="form-control @error('lab_hod_desination') is-invalid @enderror"
                                                    placeholder="Designation of Labortary Head (e.g Pathalogist)"
                                                    value="{{ old('lab_hod_desination') }}">
                                                @error('lab_hod_desination')
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
                                                <input type="text" name="lab_hod_cnic"
                                                    class="form-control @error('lab_hod_cnic') is-invalid @enderror"
                                                    placeholder="CNIC of Labortary Head (e.g 36603-2794665-7)"
                                                    value="{{ old('lab_hod_cnic') }}">
                                                @error('lab_hod_cnic')
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
                                                <input type="text" name="lab_hod_email"
                                                    class="form-control @error('lab_hod_email') is-invalid @enderror"
                                                    placeholder="Email of Labortary Head (e.g pathalogist@gmail.com)"
                                                    value="{{ old('lab_hod_email') }}">
                                                @error('lab_hod_email')
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
                                                <input type="text" name="lab_hod_phone"
                                                    class="form-control @error('lab_hod_phone') is-invalid @enderror"
                                                    placeholder="PhoneNo of Labortary Head (e.g 03007731712)"
                                                    value="{{ old('lab_hod_phone') }}">
                                                @error('lab_hod_phone')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    


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
