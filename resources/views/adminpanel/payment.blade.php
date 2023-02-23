@extends('adminpanel.admintemplate')
@push('title')
  <title>Payments| {{config('constants.app_name')}}</title>
@endpush
@section('main-section')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Submit Payment</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Submit Payment</li>
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
                <!-- flash-message -->
                <div class="row form-group">
                  <div class="col-2">&nbsp;</div>
                  <div class="col-8">
              <div class="flash-message">
                  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                      @if (Session::has('alert-' . $msg))
                          <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}
                              <a href="#" class="close" data-dismiss="alert"
                                  aria-label="close">&times;</a>
                          </p>
                      @endif
                  @endforeach
              </div>
                  </div>
                  <div class="col-2">&nbsp;</div>
              </div> <!-- end .flash-message -->

              <form action="{{ url('/process-payment') }}" method="post">
                @csrf
                <div>
                  <label for="card-number">Card Number:</label>
                  <input type="text" name="card-number" id="card-number">
                </div>
                <div>
                  <label for="expiry-date">Expiry Date:</label>
                  <input type="text" name="expiry-date" id="expiry-date">
                </div>
                <div>
                  <label for="cvv">CVV:</label>
                  <input type="text" name="cvv" id="cvv">
                </div>
                <div>
                  <input type="submit" value="Submit Payment">
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
