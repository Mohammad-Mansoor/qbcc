@extends('dsh.master')
@section('title' , '  قالین های نماینده')
@section('content')
    <!-- navbar -->

  
    <div id="agentBalance">  
        <div style="position: relative;">
            <div class="btn btn-sm btn-primary hideOnPrint" style="position: absolute;right:265px;top:40px" onclick="printPage('agentBalance')"><i class="fa fa-print"></i> Print</div>
        </div>
        <div class="sparkline12-list">   
            <div class="row" style="margin-top: 70px">
                <div class="col-lg-8" style="margin: auto;float:none">
                    <div class="sparkline8-graph">
                        <h4 style="margin-bottom: 50px">حساب کلی قالین شماره : {{$carpet->carpet_no}}</h4>
                        <div class="static-table-list">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="text-align:right !important;">مشخصات</th>
                                        <th style="text-align:right !important;">مقادیر</th>
                                        <th style="text-align:right !important;">مشخصات</th>
                                        <th style="text-align:right !important;">مقادیر</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>نوعیت</td>
                                        <td>{{ $carpet->type->carpet_type }}</td>
                                        <td>مساحت قالین</td>
                                        <td>{{ $carpet->area }} </td>
                                    </tr>
                                    <tr>
                                        <td>قیمت فی متر</td>
                                        <td>{{ $carpet->price }} AF</td>
                                        <td>قیمت مجموع</td>
                                        <td>{{ $carpet->total_price }} $</td>
                                    </tr>
                                    <tr>
                                        <td>نماینده</td>
                                        <td>{{ $carpet->agent->user->name }}</td> 
                                        <td>آدرس نماینده</td>
                                        <td>{{ $carpet->agent->agent_address }}</td> 
                                    </tr>
                                    <tr>
                                        <td><h4 style="margin-top: 35px;margin-bottom:20px;">  مصارف قالین  </h4> </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0px !important"> دریافت نماینده </td>
                                        <td >{{$totalReceiv}} $</td>
                                        <td > مجموع مواد رنگه</td>
                                        <td >{{ $mawad_ranga }}kg </td>
                                    </tr>
                                    <tr>
                                        <td >مجموع تنسته</td>
                                        <td >{{ $mawad_pakhta }}kg</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

