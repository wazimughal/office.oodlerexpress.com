@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Open Balance Deliveries| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-sm-6">
                        <h1>View Open Balance Deliveries </h1>

                    </div>
                    <div class="col-sm-4">&nbsp;</div>

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
                                <h3 class="card-title">Open Balance Deliveries</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                @if ($user->group_id == config('constants.groups.admin'))
                                    <form action="" method="GET">
                                        <div class="row" style="margin-bottom:15px;">
                                            <div class="col-md-4">&nbsp;</div>
                                            <div class="col-md-4">

                                                <label>Select Customer</label>
                                                <select name="customer_id"
                                                    class="form-control select2bs4 @error('customer_id') is-invalid @enderror"
                                                    placeholder="How Often do you ship" value="{{ old('customer_id') }}">
                                                    {!! get_customers_options($customer_id) !!}
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-success btn-block"><i
                                                        class="fa fa-save"></i> Search Open Balance</button>
                                            </div>
                                        </div>

                                    </form>
                                @endif
                                <form action="{{ $route }}" method="GET">
                                    @csrf
                                    <input type="hidden" name="customer_id"
                                        value="{{ isset($customer_id) ? $customer_id : '' }}" id="input_customer_id">
                                    <input type="hidden" value="0" name="amount" id="input_total_payment">
                                    <table id="example1" class="table table-bordered table-striped table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Select</th>
                                                <th>PO Number</th>
                                                <th>Invoice#</th>
                                                <th>Cost</th>
                                                <th>Paid</th>
                                                <th>Due</th>
                                                <th>Pick-up Street Address</th>
                                                <th>Drop-off Street Address</th>
                                                <th>Customer</th>
                                                <th>Assigned to</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php 
                                            $counter = 1;
                                            
                                            foreach ($quotesData as $data){

                                                $extra_charges=($data['quote_agreed_cost']['extra_charges']>0)?$data['quote_agreed_cost']['extra_charges']:0;
                                                $quoted_price=($data['quote_agreed_cost']['quoted_price']>0)?$data['quote_agreed_cost']['quoted_price']:0;
                                                $quoted_price=$quoted_price+ $extra_charges;

                                                $paid_amount=0;
                                                foreach($data['invoices'] as $invoice){
                                                    $paid_amount=$paid_amount+$invoice['paid_amount'];
                                                }

                                                $due_amount=$quoted_price-$paid_amount;
                                                if($due_amount<1)
                                                continue;
                                            ?>
                                            <input type="hidden" value="{{ $quoted_price }}"
                                                id="input_cost_{{ $data['id'] }}">
                                            <input type="hidden" value="{{ $paid_amount }}"
                                                id="input_paid_{{ $data['id'] }}">
                                            <input type="hidden" value="{{ $due_amount }}"
                                                id="input_due_{{ $data['id'] }}">

                                            <tr id="row_{{ $data['id'] }}">
                                                <td>
                                                    <div class="icheck-primary d-inline">
                                                        <input onchange='handleChange(this);'type="checkbox"
                                                            value="{{ $data['id'] }}" name="open_balance_quote_id[]"
                                                            id="open_balance_quote_id_{{ $data['id'] }}">
                                                        <label for="open_balance_quote_id_{{ $data['id'] }}">
                                                        </label>
                                                    </div>
                                                </td>
                                                <td id="po_number_{{ $data['id'] }}">{{ $data['po_number'] }}</td>
                                                <td id="qb_invoice_{{ $data['id'] }}">{{ $data['qb_invoice_no'] }}</td>
                                                <td id="quoted_price_{{ $data['id'] }}">${{ $quoted_price }}</td>
                                                <td id="paid_amount_{{ $data['id'] }}">${{ $paid_amount }}</td>
                                                <td id="due_amount_{{ $data['id'] }}">${{ $due_amount }}</td>
                                                <td id="pickup_street_address_{{ $data['id'] }}">
                                                    {{ $data['pickup_street_address'] }},<br>
                                                    {{ $data['pickup_contact_number'] }}
                                                </td>
                                                <td id="drop_off_street_address_{{ $data['id'] }}">
                                                    {{ $data['drop_off_street_address'] }},<br>
                                                    {{ $data['drop_off_contact_number'] }}

                                                </td>
                                                <td id="drop_off_contact_number_{{ $data['id'] }}">
                                                    {{ $data['customer']['name'] }}
                                                </td>

                                                <td>
                                                    @php
                                                        //p($data); break;
                                                        if (isset($data['driver_id']) && $data['driver_id'] > 0) {
                                                            echo 'Driver :' . $data['driver']['name'];
                                                        } elseif ($data['sub']) {
                                                            echo 'Sub :' . $data['sub']['business_name'] . '<br> Status:' . sub_status_msg($data['sub_status']);
                                                        } else {
                                                            echo 'Not Assigned';
                                                        }
                                                    @endphp
                                                </td>
                                                <td>

                                                    <a href="{{ route('deliveries.view', $data['id']) }}"
                                                        class="btn btn-info btn-block btn-sm"><i class="fas fa-eye"></i>
                                                        View</a>
                                                </td>

                                                </td>

                                            </tr>
                                            <?php 
                                                $counter ++;
                                        }
                                        ?>



                                        </tbody>

                                    </table>
                                    <div class="row">
                                        <div class="col-md-3">TOTAL: $ <span id="total_payment">0</span></div>
                                        <div class="col-md-9">&nbsp;</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"><button type="submit" class="btn btn-success btn-block"><i
                                                    class="fa fa-save"></i> Pay</button></div>
                                        <div class="col-md-9">&nbsp;</div>
                                    </div>

                                </form>
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
            $('.select2').select2();
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });

            $('#from_date').datetimepicker({
                format: 'L'
            });
            $('#to_date').datetimepicker({
                format: 'L'
            });
        });

        function handleChange(checkbox) {
            quote_id = $(checkbox).val();
            cost = parseInt($('#input_cost_' + quote_id).val(), 10);
            paid = parseInt($('#input_paid_' + quote_id).val(), 10);
            due = parseInt($('#input_due_' + quote_id).val(), 10);
            //alert(typeof(due));

            if (checkbox.checked == true) {
                input_total_payment = parseInt($('#input_total_payment').val(), 10);
                input_total_payment = input_total_payment + due;


            } else {
                input_total_payment = parseInt($('#input_total_payment').val(), 10);
                input_total_payment = input_total_payment - due;

            }
            $('#input_total_payment').val(input_total_payment);
            $('#total_payment').html(input_total_payment);
        }
    </script>
@endsection
