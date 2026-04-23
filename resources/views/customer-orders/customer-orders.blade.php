@extends('dsh.master')

@section('content')

    <style>
        @media print {
            @page {
                size: A4 landscape;

                overflow-y: scroll;
                overflow-x: visible;
                padding: 0;
                margin: 0;
            }
        }
    </style>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">

            <div class="card">
                <div class="card-header">
                    <h4 style="float:left;">Add New Order</h4>
                </div>
                <div class="card-body">

                    @if(!$orderEdit)
                        <form action="/dashboard/customer-orders" method="post" enctype="multipart/form-data">
                            @csrf
                            <br>
                            <input type="hidden" name="customer_id" value="{{$customer->c_id}}">

                            <div class="row" style="direction: ltr;">

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Order Number</label>
                                    <input type="text" name="order_name" placeholder="Order Number"
                                           class="form-control">
                                    @error('order_name') <p class="text-danger">
                                        {{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Order Date</label>
                                    <input type="date" name="order_date" class="form-control">
                                    @error('order_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>


                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 35px;">

                                    <button class="btn btn-block btn-primary submit-btn" style="display: inline;"
                                            type="submit"><span
                                            class="fa fa-save"></span> Save
                                    </button>
                                </div>

                            </div>
                        </form>

                    @else
                        <form action="/dashboard/customer-orders/{{$orderEdit->co_id}}" method="post"
                              enctype="multipart/form-data">
                            {{method_field('patch')}}
                            @csrf
                            <br>
                            <input type="hidden" name="customer_id" value="{{$orderEdit->customer_id}}">
                            <div class="row" style="direction: ltr;">

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Order Number</label>
                                    <input type="text" name="order_name" value="{{$orderEdit->order_name}}"
                                           placeholder="Order Name"
                                           class="form-control">
                                    @error('order_name') <p class="text-danger">
                                        {{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>


                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Order Date</label>
                                    <input type="date" name="order_date" value="{{$orderEdit->order_date}}"
                                           class="form-control">
                                    @error('order_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>


                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 35px;">

                                    <button class="btn btn-block btn-primary submit-btn" style="display: inline;"
                                            type="submit"><span
                                            class="fa fa-save"></span> Save
                                    </button>
                                </div>

                            </div>

                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <div class="row" id="expensePrint">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">

                            <div class="btn-group hideOnPrint" style="float: right; ">
                                <div class="btn btn-sm btn-primary" style="float: left"
                                     onclick="printPage('expensePrint')"><i
                                        class="fa fa-print"></i> Print
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            <h4 style="float:left;">{{$customer->customer_name}} Orders List</h4>
                        </div>


                    </div>


                    @if(session("status"))
                        <div class="alert alert-success status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('status')}}</p>
                        </div>

                    @endif
                    @if(session("error"))

                        <div class="alert alert-danger error" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('error')}}</p>
                        </div>

                    @endif
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover table-xs" id="expense_list">
                            <thead>
                            <tr>

                                <th><span style="float:left;">Action</span></th>
                                <th><span style="float:left;">Order Date</span></th>
                                <th><span style="float:left;">Order #</span></th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach($customer_orders as $co)
                                <tr>
                                    <td class="printTitle hideOnPrint">
                              <span style="float:left;">
                                  <a href="/dashboard/customer-order-details/{{$co->co_id}}" class="btn btn-sm btn-primary">&nbsp;Details</a>

                              </span>
                              <span style="float:left;">
                                  <a href="/dashboard/customer-orders/{{$co->co_id}}/edit" class="btn btn-sm btn-info">&nbsp;Edit</a>

                              </span>

                                        @if(auth()->user()->role == 'SP')
                                            <span style="float:left;">
                                            <button onclick="deleteOrder({{$co->co_id}})"
                                                    class="btn btn-danger btn-sm ">
                                                <i
                                                    class="fa fa-tick"></i>Delete
                                            </button>
                                                 </span>
                                        @endif
                                    </td>

                                    <td><span style="float:left;">{{$co->order_date}}</span></td>
                                    <td><span style="float:left;">{{$co->order_name}}</span></td>
                                </tr>

                            @endforeach

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>

        function deleteOrder(id) {

            swal({
                text: "Are you Sure ?",
                buttons: true,
                dangerMode: true,
                buttons: {
                    confirm: {text: 'Yes', className: 'btn-danger'},
                    cancel: 'No'
                },
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'DELETE',
                            data: {
                                '_token': '{{csrf_token()}}',
                            },
                            url: '/dashboard/customer-orders/' + id,
                            success: function (res) {

                                if (res.status == 'success') {
                                    $('.ur' + id).hide();
                                    $('.alert-success').show();
                                    location.reload();
                                } else {
                                    $('.alert-danger').show();
                                }


                                window.setTimeout(function () {
                                    $(".alert-success").fadeTo(500, 0).slideUp(500, function () {

                                        $(this).remove();
                                    });
                                }, 2000);
                            },

                        })
                    }
                });
        }

        $("#height").on("change paste keyup", function () {

            var h = parseFloat($(this).val());

            var w = parseFloat($('#width').val());
            $('#area').val(h * w);
        });
        $("#width").on("change paste keyup", function () {

            var w = parseFloat($(this).val());

            var h = parseFloat($('#height').val());

            $('#area').val(h * w);
        });

        $('#current_status').select2();


    </script>
@endsection

