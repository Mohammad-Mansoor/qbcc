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
                    <h4 style="float:left;">Add New </h4>
                </div>
                <div class="card-body">

                    @if(!$orderEdit)
                        <form action="/dashboard/customer-order-details" method="post" enctype="multipart/form-data">
                            @csrf
                            <br>
                            <input type="hidden" name="customer_order_id" value="{{$customer_order->co_id}}">

                            <div class="row" style="direction: ltr;">



                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Quality</label>
                                    <input type="text" name="quality" placeholder="Quality " class="form-control">
                                    @error('quality') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Height</label>
                                    <input type="text" name="height" id="height" placeholder="Height "
                                           class="form-control">
                                    @error('height') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Width</label>
                                    <input type="text" name="width" id="width" placeholder="Width "
                                           class="form-control">
                                    @error('width') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Area</label>
                                    <input type="text" name="area" id="area" placeholder="Area " readonly
                                           class="form-control">
                                    @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Warp</label>
                                    <input type="text" name="warp" placeholder="Warp " class="form-control">
                                    @error('warp') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Weft</label>
                                    <input type="text" name="weft" placeholder="Weft " class="form-control">
                                    @error('weft') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Wash Type</label>
                                    <input type="text" name="wash_type" placeholder="Wash Type" class="form-control">
                                    @error('wash_type') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="  float:left;">Pile Height</label>
                                    <input type="text" name="pile_height" placeholder="Pile Height "
                                           class="form-control">
                                    @error('pile_height') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left">Weaver Code</label>
                                    <input type="text" name="weaver_code" placeholder="Weaver Code "
                                           class="form-control">
                                    @error('weaver_code') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Start Date</label>
                                    <input type="date" name="start_date" class="form-control">
                                    @error('start_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">End Date</label>
                                    <input type="date" name="end_date" class="form-control">
                                    @error('end_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>


                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="form-group fill">
                                        <label style="float: left;">Photo</label>
                                        <input type="file" name="photo"

                                               class="form-control">
                                        @error('photo') <p
                                            class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 12px;">
                                    <label style="float: left;">Current Status</label>
                                    <select name="current_status" id="current_status" class="form-control">
                                        <option>Select Current Status</option>
                                        <option value="Graphing">Graphing</option>
                                        <option value="Dyeing">Dyeing</option>
                                        <option value="On loom">On loom</option>
                                        <option value="Off loom">Off loom</option>
                                        <option value="Washing">Washing</option>
                                        <option value="Finishing">Finishing</option>
                                        <option value="Repairing">Repairing</option>
                                        <option value="Ready">Ready</option>
                                        <option value="Shipped">Shipped</option>
                                        <option value="Paused">Paused</option>
                                        <option value="Cancelled">Cancelled</option>

                                    </select>

                                    @error('current_status') <p class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
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
                        <form action="/dashboard/customer-order-details/{{$orderEdit->cod_id}}" method="post"
                              enctype="multipart/form-data">
                            {{method_field('patch')}}
                            @csrf
                            <br>
                            <div class="row" style="direction: ltr;">

                                <input type="hidden" name="customer_order_id" value="{{$orderEdit->customer_order_id}}">
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Quality</label>
                                    <input type="text" name="quality" placeholder="Quality "
                                           value="{{$orderEdit->quality}}" class="form-control">
                                    @error('quality') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Height</label>
                                    <input type="text" name="height" id="height" placeholder="Height "
                                           value="{{$orderEdit->height}}" class="form-control">
                                    @error('height') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Width</label>
                                    <input type="text" name="width" id="width" placeholder="Width " value="{{$orderEdit->width}}"
                                           class="form-control">
                                    @error('width') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Area</label>
                                    <input type="text" name="area" id="area" placeholder="Area " value="{{$orderEdit->area}}"
                                           readonly class="form-control">
                                    @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Warp</label>
                                    <input type="text" name="warp" placeholder="Warp " value="{{$orderEdit->warp}}"
                                           class="form-control">
                                    @error('warp') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Weft</label>
                                    <input type="text" name="weft" placeholder="Weft " value="{{$orderEdit->weft}}"
                                           class="form-control">
                                    @error('weft') <p class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Wash Type</label>
                                    <input type="text" name="wash_type" placeholder="Wash Type"
                                           value="{{$orderEdit->wash_type}}" class="form-control">
                                    @error('wash_type') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float:left;">Pile Height</label>
                                    <input type="text" name="pile_height" value="{{$orderEdit->pile_height}}"
                                           placeholder="Pile Height "
                                           class="form-control">
                                    @error('pile_height') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left">Weaver Code</label>
                                    <input type="text" name="weaver_code" value="{{$orderEdit->weaver_code}}"
                                           placeholder="Weaver Code "
                                           class="form-control">
                                    @error('weaver_code') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">Start Date</label>
                                    <input type="date" name="start_date" value="{{$orderEdit->start_date}}"
                                           class="form-control">
                                    @error('start_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label style="float: left;">End Date</label>
                                    <input type="date" name="end_date" value="{{$orderEdit->end_date}}"
                                           class="form-control">
                                    @error('end_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>


                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="form-group fill">
                                        <label style="float: left;">Photo</label>
                                        <input type="file" name="photo"

                                               class="form-control">
                                        @error('photo') <p
                                            class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 12px;">
                                    <label style="float: left;">Current Status</label>
                                    <select name="current_status" id="current_status" class="form-control">
                                        <option>Select Current Status</option>
                                        <option
                                            value="Graphing" {{ $orderEdit->current_status == 'Graphing' ? 'selected' : '' }} >
                                            Graphing
                                        </option>
                                        <option
                                            value="Dyeing" {{ $orderEdit->current_status == 'Dyeing' ? 'selected' : '' }}>
                                            Dyeing
                                        </option>
                                        <option
                                            value="On loom" {{ $orderEdit->current_status == 'On loom' ? 'selected' : '' }}>
                                            On loom
                                        </option>
                                        <option
                                            value="Off loom" {{ $orderEdit->current_status == 'Off loom' ? 'selected' : '' }}>
                                            Off loom
                                        </option>
                                        <option
                                            value="Washing" {{ $orderEdit->current_status == 'Washing' ? 'selected' : '' }}>
                                            Washing
                                        </option>
                                        <option
                                            value="Finishing" {{ $orderEdit->current_status == 'Finishing' ? 'selected' : '' }}>
                                            Finishing
                                        </option>
                                        <option
                                            value="Repairing" {{ $orderEdit->current_status == 'Repairing' ? 'selected' : '' }}>
                                            Repairing
                                        </option>
                                        <option
                                            value="Ready" {{ $orderEdit->current_status == 'Ready' ? 'selected' : '' }}>
                                            Ready
                                        </option>
                                        <option
                                            value="Shipped" {{ $orderEdit->current_status == 'Shipped' ? 'selected' : '' }}>
                                            Shipped
                                        </option>
                                        <option
                                            value="Paused" {{ $orderEdit->current_status == 'Paused' ? 'selected' : '' }}>
                                            Paused
                                        </option>
                                        <option
                                            value="Cancelled" {{ $orderEdit->current_status == 'Cancelled' ? 'selected' : '' }}>
                                            Cancelled
                                        </option>

                                    </select>

                                    @error('current_status') <p class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
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

                            <?php

                                $customer = \Illuminate\Support\Facades\DB::table('customer_account_orders')->where('c_id',$customer_order->customer_id)->first();
                                ?>
                            <h4 style="float:left;">{{$customer->customer_name}} order number {{$customer_order->order_name}} Orders List</h4>
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

                    <div class="modal fade order_image" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title h4" id="myLargeModalLabel">
                                        <input type="text" name="order_no" id="order_number" class="form-control"
                                               readonly>
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
                            <tr>

                                <th>Action</th>
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

                            </tr>
                            </thead>
                            <tbody>


                            @foreach($customer_order_details as $co)
                                <tr>
                                    <td class="printTitle hideOnPrint">
                              <span style="float:left;"><a
                                      href="/dashboard/customer-order-details/{{$co->cod_id}}/edit"
                                      class="btn btn-sm btn-info">&nbsp;Edit</a></span>

{{--                                        @if(auth()->user()->role == 'SP')--}}
                                            <button onclick="deleteOrder({{$co->cod_id}})"
                                                    class="btn btn-danger btn-sm ">
                                                <i
                                                    class="fa fa-tick"></i>Delete
                                            </button>
{{--                                        @endif--}}
                                    </td>
                                    <td>{{$co->current_status}}</td>
                                    <td><a href="#"
                                           onclick="$('#order_number').val('  Order Image  {{$customer_order->order_name}} ');
                                         $('#order_image').attr('src', '/{{str_replace('\\','/',$co->photo)}}');
                                         "
                                           data-toggle="modal"
                                           data-target=".order_image"><img src="/{{$co->photo}}" style="height: 32px;"
                                                                           alt=""></a></td>
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
                                </tr>

                            @endforeach
                            <tr>
                                <td ><b>Area</b></td>
                                <td style="direction: ltr;"><b>Order#</b></td>
                                <td colspan="2"><b>Status</b></td>
                            </tr>

                                <?php
                                $graphing = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Graphing')->get();


                                $dyeing = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Dyeing')->get();
                                $on_loom = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'On loom')->get();
                                $off_loom = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Off loom')->get();


                                $washing = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Washing')->get();
                                $finishing = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Finishing')->get();
                                $repairing = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Repairing')->get();
                                $ready = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Ready')->get();
                                $shipped = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Shipped')->get();
                                $paused = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Paused')->get();
                                $cancelled = \Illuminate\Support\Facades\DB::table('customer_order_details')->where('customer_order_id', $customer_order->co_id)->where('current_status', 'Cancelled')->get();


                                ?>


                            @if($graphing->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$graphing->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$graphing->count()}}</b></td>
                                    <td colspan="2"><b>Graphing</b></td>
                                </tr>
                            @endif

                            @if($dyeing->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$dyeing->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$dyeing->count()}}</b></td>
                                    <td colspan="2"><b>Dyeing</b></td>
                                </tr>
                            @endif
                            @if($on_loom->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$on_loom->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$on_loom->count()}}</b></td>
                                    <td colspan="2"><b>On loom</b></td>
                                </tr>
                            @endif

                            @if($off_loom->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$off_loom->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$off_loom->count()}}</b></td>
                                    <td colspan="2"><b>Off loom</b></td>
                                </tr>
                            @endif

                            @if($washing->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$washing->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$washing->count()}}</b></td>
                                    <td colspan="2"><b>Washing</b></td>
                                </tr>
                            @endif
                            @if($finishing->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$finishing->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$finishing->count()}}</b></td>
                                    <td colspan="2"><b>Finishing</b></td>
                                </tr>
                            @endif
                            @if($repairing->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$repairing->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$repairing->count()}}</b></td>
                                    <td colspan="2"><b>Repairing</b></td>
                                </tr>
                            @endif
                            @if($ready->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$ready->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$ready->count()}}</b></td>
                                    <td colspan="2"><b>Ready</b></td>
                                </tr>
                            @endif
                            @if($shipped->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$shipped->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$shipped->count()}}</b></td>
                                    <td colspan="2"><b>Shipped</b></td>
                                </tr>
                            @endif
                            @if($paused->sum('area') > 0)
                                <tr>
                                    <td style="direction: ltr;"><b>{{$paused->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$paused->count()}}</b></td>
                                    <td colspan="2"><b>Paused</b></td>
                                </tr>
                            @endif
                            @if($cancelled->sum('area') > 0)

                                <tr>
                                    <td style="direction: ltr;"><b>{{$cancelled->sum('area')}} m
                                            <sq>2</sq>
                                        </b></td>
                                    <td><b>{{$cancelled->count()}}</b></td>
                                    <td colspan="2"><b>Cancelled</b></td>
                                </tr>
                            @endif


                            <tr>
                                <td style="direction: ltr;"><b> {{$customer_order_details->sum('area')}} m
                                        <sq>2</sq>
                                    </b></td>
                                <td><b>{{$customer_order_details->count()}}</b></td>
                                <td colspan="2"><b>Total Order</b></td>
                            </tr>

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
                            url: '/dashboard/customer-order-details/' + id,
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

