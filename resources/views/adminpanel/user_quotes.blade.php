@extends('adminpanel.admintemplate')
@push('title')
    <title>
        quotes| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-2">
                        <h1>View quotes </h1>

                    </div>
                    <div class="col-sm-4"><a style="width:60%" href="{{route('quotes.request_quotes_form')}}"
                            class="btn btn-block btn-success btn-lg">Request New Quote <i class="fa fa-plus"></i></a></div>
                    
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
                                <h3 class="card-title">Leads</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Quote Type</th>
                                            <th>Business Type</th>
                                            <th>PO Number</th>
                                            <th>Pick-up Street Address</th>
                                            <th>Pick-up Phone</th>
                                            <th>Drop-off Street Address</th>
                                            <th>Drop-off Phone</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            foreach ($quotesData as $data){
                                            ?>
                                        <tr id="row_{{ $data['id'] }}">
                                            <td><strong id="quote_type_{{ $data['id'] }}">{{ $data['quote_type'] }}</strong>
                                            </td>
                                            <td id="business_type_{{ $data['id'] }}">{{ $data['business_type'] }}</td>
                                            <td id="po_number_{{ $data['id'] }}">
                                                {{ $data['po_number'] }}</td>
                                            <td id="pickup_street_address_{{ $data['id'] }}">
                                                {{ $data['pickup_street_address'] }}</td>
                                            <td id="pickup_contact_number_{{ $data['id'] }}">
                                               {{$data['pickup_contact_number']}}
                                            </td>
                                            <td id="drop_off_street_address_{{ $data['id'] }}">
                                               {{$data['drop_off_street_address']}}
                                            </td>
                                            <td id="drop_off_contact_number_{{ $data['id'] }}">
                                               {{$data['drop_off_contact_number']}}
                                            </td>
                                            <td id="status{{ $data['id'] }}">
                                                @if ($data['status']==config('constants.quote_status.pedning'))
                                                <span class="btn btn-info btn-block btn-sm"><i
                                                    class="fas fa-chart-line"></i>
                                                Pending</span> 
                                               @elseif($data['status']==config('constants.quote_status.approved'))
                                               <span class="btn btn-info btn-block btn-sm"><i
                                                class="fas fa-chart-line"></i>
                                            Approved</span> 
                                               @elseif($data['status']==config('constants.quote_status.declined'))
                                               <span class="btn btn-info btn-block btn-sm"><i
                                                class="fas fa-chart-line"></i>
                                            Cancelled</span> 
                                               @elseif($data['status']==config('constants.quote_status.quote_submitted'))
                                               {{-- <span  class="btn btn-success btn-block btn-sm"><i
                                                    class="fas fa-chart-line"></i>
                                                New</span> --}}
                                                <select name="status" id="customer_action" onchange="customer_action({{$data['id']}},'change_quote_status')" class="form-control select2bs4">
                                                    <option value=""> Select Approve or Decline </option>
                                                    <option value="{{base64_encode('approved')}}"> Approve </option>
                                                    <option value="{{base64_encode('declined')}}"> Decline </option>
                                                </select>
                                               @endif
                                                
                                            </td>
                                            <td>
                                                @if ($user->group_id==config('constants.groups.admin'))

                                                <a href="{{route('quotes.send_quote_form',$data['id']) }}"
                                                class="btn btn-success btn-block btn-sm"><i class="fas fa-edit"></i>
                                                Send Quote</a>
                                                
                                                @if(isset($type) && $type=='requested')
                                                <a href="{{route('quotes.quoteeditform',$data['id']) }}"
                                                class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                Edit</a>
                                                @endif
                                                @if ($data['is_active']==1)
                                                <button
                                                onClick="do_action({{ $data['id'] }},'delete',{{ $counter }})"
                                                type="button" class="btn btn-danger btn-block btn-sm"><i
                                                    class="fas fa-trash"></i>
                                                Delete</button>
                                                @elseif ($data['is_active']==2) 
                                                <button
                                                onClick="do_action({{ $data['id'] }},'restore',{{ $counter }})"
                                                type="button" class="btn btn-primary btn-block btn-sm"><i
                                                    class="fas fa-undo"></i>
                                                Restore</button>
                                                    @endif
                                                @else
                                                <a href="{{route('quotes.view',$data['id']) }}"
                                                class="btn btn-primary btn-block btn-sm"><i class="fas fa-edit"></i>
                                                View</a>
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
                                            <th>Quote Type</th>
                                            <th>Business Type</th>
                                            <th>PO Number</th>
                                            <th>Pick-up Street Address</th>
                                            <th>Pick-up Phone</th>
                                            <th>Drop-off Street Address</th>
                                            <th>Drop-off Phone</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-right"> {{ $quotesData->links() }}</div>
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
                        <h3 class="card-title"> Leads Panel</h3>
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
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('footer-js-css')
    <!-- DataTables  & Plugins -->
    
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
      $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });
        
        
function customer_action(id, action_name) {
    
    selectval=$('#customer_action').val();
    if(selectval=='')
    return false;

if (confirm('Are you sure you want to perform this action')) {

var sendInfo = {
    action: action_name,
    status: selectval,
    id: id
};

$.ajax({
    url: "{{ url('/admin/quotes/ajaxcall/') }}/" + id,
    data: sendInfo,
    contentType: 'application/json',
    error: function() {
        alert('There is Some Error, Please try again !');
    },
    type: 'GET',
    dataType: 'json',
    success: function(data) {
        if (data.error == 'No') {
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
       window.location="";
    }
});
}
}
        function do_action(id, action_name,counter_id) {

                if(action_name=='restore')
                alertMsg = 'Are you sure you want to Restore this?';
                else if(action_name=='delete')
                alertMsg = 'Are you sure you want to Delete this?';

            if (confirm(alertMsg)) {

                var sendInfo = {
                    action: action_name,
                    counter: counter_id,
                    id: id
                };

                $.ajax({
                    url: "{{ url('/admin/quotes/ajaxcall/') }}/" + id,
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
    </script>
@endsection
