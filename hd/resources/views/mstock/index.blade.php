@extends('dsh.master')
@section('title' , ' گدام تار')
@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                        
                            <h4 class="text-c-yellow"> @if($sales) {{$sales->sum('amount')}} kg @else 0 kg
                                @endif</h4>
                    
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-c-yellow">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="text-white m-b-0">مجموعه فروشات</h5>
                        </div>
                        <div class="col-3 text-right">
                            <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                        
                            <h4 class="text-c-yellow">{{$firstTotal}} kg </h4>
                    
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-c-yellow">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="text-white m-b-0">مجموعه {{$firstName->material_category}}</h5>
                        </div>
                        <div class="col-3 text-right">
                            <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                        
                            <h4 class="text-c-yellow">{{$secondTotal}} kg</h4>
                    
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-c-yellow">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="text-white m-b-0"> مجموعه {{$secondName->material_category}}</h5>
                        </div>
                        <div class="col-3 text-right">
                            <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                        
                            <h4 class="text-c-yellow">{{$thirdTotal}} kg </h4>
                    
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-c-yellow">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="text-white m-b-0"> مجموعه {{$thirdName->material_category}}</h5>
                        </div>
                        <div class="col-3 text-right">
                            <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card" id="stock">
        <div class="card-header">
            <h5>موجودی تار در گدام</h5>
            @if(session("status"))
                <div class="alert alert-success status"  style="display:none;" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <p class="text-center">{{session('status')}}</p>
                </div>
    
            @endif
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
                
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 hideOnPrint">
                    <div class="btn btn-primary btn-sm hideOnPrint"  onclick="printPage('stock')"
                         style="position: relative;float: left;"><i class="fa fa-print"></i> Print
                    </div>
                    
                </div>
    
    
            </div>
        </div>
        <div class="card-body">
            <div class="static-table-list table-responsive">
                <table class="table table-hover table-xs">
                    <thead>
                    <tr >
                        <th>نمبر گدام</th>
                        <th>کتگوری مواد</th>
                        <th>نوعیت مواد</th>
                        <th>مقدار</th>
                        <th>قیمت فی کیلو</th>
                      
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($stock as $p)
                     @if($p->quantity > 0)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td >{{ $p->category->material_category }}</td>
                            <td >{{ $p->type->material_type }}</td>
                            <td dir="ltr">{{ $p->quantity }} KG</td>
                            <td dir="ltr">{{ $p->price_per_kilo .'AF' }}
                            </td>
                            {{--<td dir="ltr">{{ \Carbon\Carbon::parse($p->created_at)->format('d-M-Y') }}</td>--}}
                        </tr>
                        @endif
                    @empty
                        <h5 style="color: red;text-align:center">هنوز موادی خریداری نشده</h5>
                    @endforelse
                    </tbody>
                </table>
    
            </div>
        </div>
    </div>
   
@endsection
