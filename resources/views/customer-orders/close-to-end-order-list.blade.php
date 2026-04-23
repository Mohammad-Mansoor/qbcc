@extends('dsh.master')

@section('content')

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
                            <h4 style="float:left;">Close to End Order List</h4>
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

                        <div class="alert alert-danger status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('error')}}</p>
                        </div>

                    @endif
                </div>
                <div class="card-body">

                    <div class="modal fade order_image" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title h4" id="myLargeModalLabel">
                                        <input type="text" name="order_no" id="order_number" class="form-control" readonly>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">

                                    <img src="" id="order_image" height="800px" width="700px" alt="">


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn  btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-xs" id="expense_list">
                            <thead>
                            <tr >

                                <th>Current Status</th>
                                <th>Photo</th>
                                <th>End Date</th>
                                <th>Start Date</th>
                                <th>Weaver Code</th>
                                <th>Pile Height</th>
                                <th>Wash Type</th>
                                <th>Weft</th>

                                <th>Warp</th>
                                <th>Area</th>
                                <th>Width</th>
                                <th>Height</th>
                                <th>Quality</th>
                                <th>Order #</th>
                                <th>Customer Code</th>
                            </tr>
                            </thead>
                            <tbody>


                            @foreach($orders as $co)

                                <?php $ord = \Illuminate\Support\Facades\DB::table('customer_orders')->where('co_id',$co->customer_order_id)->first() ?>
                                <tr>

                                    <td>{{$co->current_status}}</td>
                                    <td>  <a href="#"
                                             onclick="$('#order_number').val('  Order Image  {{$ord->order_name}} ');
                                         $('#order_image').attr('src', '/{{str_replace('\\','/',$co->photo)}}');
                                         "
                                             data-toggle="modal"
                                             data-target=".order_image"><img src="/{{$co->photo}}" style="height: 32px;" alt=""></a></td>
                                    <td>{{$co->end_date}}</td>
                                    <td>{{$co->start_date}}</td>
                                    <td>{{$co->weaver_code}}</td>
                                    <td>{{$co->pile_height}}</td>
                                    <td>{{$co->wash_type}}</td>
                                    <td>{{$co->weft}}</td>
                                    <td>{{$co->warp}}</td>
                                    <td>{{$co->area}}</td>

                                    <td>{{$co->width}}</td>
                                    <td>{{$co->height}}</td>
                                    <td>{{$co->quality}}</td>
                                    <td>{{$ord->order_name}}</td>

                                    <?php $customer = \Illuminate\Support\Facades\DB::table('customer_account_orders')->where('c_id',$ord->customer_id)->first();?>
                                    <td>{{$customer->customer_name}}</td>



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


