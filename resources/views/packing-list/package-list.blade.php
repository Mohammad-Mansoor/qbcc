@extends('dsh.master')
@section('title' , 'جزئیات بسته‌بندی')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Action Header -->
    <div class="row mb-4 hideOnPrint">
        <div class="col-md-6 text-right">
            <h3 class="mb-0 font-weight-bold text-dark"><i class="fa fa-cubes text-primary mr-2"></i> جزئیات پکینگ لیست #{{$packing_id->packing_no}}</h3>
            <p class="text-muted small">مدیریت بایل‌ها و قالین‌های بسته‌بندی شده</p>
        </div>
        <div class="col-md-6 text-left">
            <button class="btn btn-primary shadow-sm px-4 font-weight-bold" onclick="printPage('premiumPackingList')">
                <i class="fa fa-print mr-2"></i> چاپ پکینگ لیست
            </button>
            <a href="/dashboard/packing-list" class="btn btn-light shadow-sm px-4 ml-2">بازگشت</a>
        </div>
    </div>

    <div class="row">
        <!-- Add Package Form -->
        <div class="col-md-4 hideOnPrint">
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white py-3 text-right">
                    <h5 class="mb-0 font-weight-bold text-success"><i class="fa fa-plus-circle mr-2"></i> افزودن بایل (پکیج) جدید</h5>
                </div>
                <div class="card-body">
                    <form action="/dashboard/package-list" method="post">
                        @csrf
                        <input type="hidden" name="packing_id" value="{{$packing_id->id}}">
                        
                        <div class="form-group text-right">
                            <label class="small font-weight-bold text-muted">نمبر بایل</label>
                            <div class="input-group shadow-sm">
                                <input type="text" value="{{ $package_no }}" name="package_no" 
                                       class="form-control border-0 bg-light font-weight-bold text-center" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light border-0"><i class="fa fa-archive"></i></span>
                                </div>
                            </div>
                            @error('package_no') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <button class="btn btn-success btn-block shadow-sm py-2 font-weight-bold" type="submit">
                            <i class="fa fa-save mr-2"></i> ثبت بایل
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Summary Card -->
            <div class="card border-0 shadow-sm rounded-lg bg-gradient-dark text-white p-4 text-right" style="background: linear-gradient(135deg, #343a40 0%, #212529 100%);">
                <div class="text-right">
                    <p class="mb-1 opacity-7 small text-uppercase">خلاصه پکینگ</p>
                    <h2 class="font-weight-bold mb-3">{{$packing_id->packing_no}}</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="font-weight-bold">{{count($package_list)}} بایل</span>
                        <span class="opacity-7 small">:تعداد بایل‌ها</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Packing List Details -->
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-lg overflow-hidden" id="premiumPackingList">
                <div class="card-header bg-white border-0 p-5 text-center">
                    <h1 class="font-weight-bold text-dark mb-2" style="letter-spacing: 5px;">PACKING LIST</h1>
                    <p class="text-primary font-weight-bold mb-0">QBIC ERP SYSTEM</p>
                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <span class="text-muted small">پکینگ نمبر: <b class="text-dark">#{{$packing_id->packing_no}}</b></span>
                        <span class="text-muted small">تاریخ: <b class="text-dark">{{$packing_id->created_at->format('Y-m-d')}}</b></span>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(session("status"))
                        <div class="alert alert-success rounded-0 mb-0 py-2 text-center small status">
                            <i class="fa fa-check-circle mr-1"></i> {{session('status')}}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th rowspan="2" class="align-middle border-0" style="width: 50px;">NO</th>
                                    <th rowspan="2" class="align-middle border-0">Item Number</th>
                                    <th rowspan="2" class="align-middle border-0">Type</th>
                                    <th colspan="3" class="border-0">Dimensions (Meters)</th>
                                </tr>
                                <tr>
                                    <th class="border-top-0 border-right-0">Length</th>
                                    <th class="border-top-0">Width</th>
                                    <th class="border-top-0 border-left-0">Sqr Meter</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($package_list as $package)
                                <!-- Package Header Row -->
                                <tr class="bg-soft-dark">
                                    <td colspan="6" class="px-4 py-2 text-right">
                                        <h6 class="mb-0 font-weight-bold text-dark">
                                            <i class="fa fa-archive mr-2"></i> بایل نمبر: {{$package->package_no}}
                                        </h6>
                                    </td>
                                </tr>
                                
                                @php($package_area = 0)
                                @php($package_count = 0)
                                
                                @foreach($package->carpet as $carpet)
                                    @php($sale = \Illuminate\Support\Facades\DB::table('sales')->where('carpet_id',$carpet->carpet_id)->where('is_returned', 0)->first())
                                    @php($c_area = $sale->carpet_area ?? $carpet->area)
                                    @php($c_width = $sale->carpet_width ?? $carpet->width)
                                    @php($c_height = $sale->carpet_height ?? $carpet->height)
                                    @php($package_area += $c_area)
                                    @php($package_count++)
                                    
                                    <tr class="text-center">
                                        <td class="small text-muted">{{$no++}}</td>
                                        <td class="font-weight-bold">{{$carpet->carpet_no}}</td>
                                        <td><span class="badge badge-light px-2 py-1">{{$carpet->type->carpet_type}}</span></td>
                                        <td>{{number_format($c_height, 2)}}</td>
                                        <td>{{number_format($c_width, 2)}}</td>
                                        <td class="font-weight-bold text-primary">{{number_format($c_area, 2)}}</td>
                                    </tr>
                                @endforeach
                                
                                <!-- Package Summary Row -->
                                <tr class="bg-light text-center">
                                    <td colspan="3" class="text-right font-weight-bold small px-4">مجموع بایل {{$package->package_no}}:</td>
                                    <td colspan="2" class="small">{{$package_count}} تخته</td>
                                    <td class="font-weight-bold text-dark">{{number_format($package_area, 2)}} m²</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center text-muted">هیچ بایل یا پکیجی یافت نشد.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($package_list) > 0)
                            <tfoot class="bg-dark text-white">
                                <tr class="text-center">
                                    <td colspan="3" class="text-right px-4 font-weight-bold">مجموع کل پکینگ لیست:</td>
                                    <td colspan="2">{{$package_list->sum(function($p){ return $p->carpet->count(); })}} تخته</td>
                                    <td class="font-weight-bold">{{number_format($package_list->sum(function($p){ 
                                        return $p->carpet->sum(function($c){
                                            $s = \Illuminate\Support\Facades\DB::table('sales')->where('carpet_id',$c->carpet_id)->where('is_returned', 0)->first();
                                            return $s->carpet_area ?? $c->area;
                                        });
                                    }), 2)}} m²</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Footer (Visible in Print) -->
                <div class="card-footer bg-white border-0 p-5">
                    <div class="row text-center mt-5">
                        <div class="col-4">
                            <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold text-uppercase">Dispatcher Signature</div>
                        </div>
                        <div class="col-4">
                            <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold text-uppercase">Warehouse Stamp</div>
                        </div>
                        <div class="col-4">
                            <div class="border-top pt-2 mx-4 text-muted tiny font-weight-bold text-uppercase">Receiver Signature</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-dark { background-color: rgba(52, 58, 64, 0.05); }
    .rounded-lg { border-radius: 1rem !important; }
    .tiny { font-size: 10px; }
    .opacity-7 { opacity: 0.7; }
    @media print {
        body { background: white !important; }
        .hideOnPrint { display: none !important; }
        .card { box-shadow: none !important; border: none !important; }
        .card-header, .card-footer { padding: 0 !important; }
        .container-fluid { padding: 0 !important; }
        .col-md-8 { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
        .bg-dark { background-color: #343a40 !important; -webkit-print-color-adjust: exact; }
        .text-white { color: white !important; -webkit-print-color-adjust: exact; }
        .bg-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () { $(this).remove(); });
        }, 3000);
    });
</script>
@endsection
