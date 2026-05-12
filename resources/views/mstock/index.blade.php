@extends('dsh.master')
@section('title' , ' گدام تار')
@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="text-c-yellow"> @if($sales) {{$sales->sum('amount')}} kg @else 0 kg @endif</h4>
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

        @foreach($categoryTotals as $cat)
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h4 class="text-c-yellow">{{$cat->total}} kg </h4>
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-c-yellow">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="text-white m-b-0">مجموعه {{$cat->name}}</h5>
                        </div>
                        <div class="col-3 text-right">
                            <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
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
                <table class="table table-hover table-xs" id="dataTable">
                    <thead>
                    <tr>
                        <th>کتگوری مواد</th>
                        <th>نوعیت مواد</th>
                        <th>گدام</th>
                        <th>مقدار موجود</th>
                        <th>قیمت فی (WAC)</th>
                        <th>ارزش مجموعی</th>
                        <th class="hideOnPrint">تاریخچه</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($stock as $p)
                        @php 
                            $statusClass = '';
                            if($p->quantity < 10) $statusClass = 'table-danger';
                            elseif($p->quantity < 50) $statusClass = 'table-warning';
                        @endphp
                        <tr class="{{ $statusClass }}">
                            <td><strong>{{ $p->material_category }}</strong></td>
                            <td>{{ $p->material_type }}</td>
                            <td><span class="badge badge-light border">{{ $p->warehouse_name ?? 'گدام مرکزی' }}</span></td>
                            <td>
                                <span class="font-weight-bold">{{ number_format($p->quantity, 2) }} kg</span>
                                @if($p->quantity < 10)
                                    <br><small class="text-danger"><i class="fa fa-warning"></i> ذخیره کم است</small>
                                @endif
                            </td>
                            <td>{{ number_format($p->price_per_kilo, 2) }} AFN</td>
                            <td class="text-primary font-weight-bold">{{ number_format($p->total_value, 2) }} AFN</td>
                            <td class="hideOnPrint">
                                <a href="{{ route('material-stock.history', [$p->cat_id, $p->type_id]) }}" 
                                   class="btn btn-outline-info btn-xs" 
                                   title="مشاهده حرکات">
                                    <i class="fa fa-history"></i> تاریخچه
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <h5 class="text-muted">هنوز موادی در گدام ثبت نشده است</h5>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
   
@endsection
