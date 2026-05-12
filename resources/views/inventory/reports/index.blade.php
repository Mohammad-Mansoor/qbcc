@extends('dsh.master')
@section('content')

    <style>
        @media print {
            .pcoded-navbar, .pcoded-header, .btn, #search_form {
                display: none !important;
            }
            .pcoded-main-container {
                margin-left: 0px !important;
                margin-top: 0px !important;
            }
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>گزارش موجودی گدام (ERP Inventory Stock)</h4>
                    <div class="btn-group" style="float: left;">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">چاپ <span class="fas fa-print"></span></button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.reports.index') }}" method="GET" id="search_form" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label>گدام</label>
                                <select name="warehouse_id" class="form-control">
                                    <option value="">همه گدام ها</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>نوع جنس</label>
                                <select name="item_type" class="form-control">
                                    <option value="">همه</option>
                                    <option value="App\Carpet" {{ request('item_type') == 'App\Carpet' ? 'selected' : '' }}>قالین</option>
                                    <option value="App\MaterialStock" {{ request('item_type') == 'App\MaterialStock' ? 'selected' : '' }}>مواد (تار)</option>
                                </select>
                            </div>
                            <div class="col-md-2" style="margin-top: 28px;">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">جستجو</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>نوع</th>
                                    <th>شناسه (ID)</th>
                                    <th>نام / نمبر</th>
                                    <th>موجودی (Qty)</th>
                                    <th>مساحت (Area)</th>
                                    <th>فی واحد (WAC)</th>
                                    <th>ارزش مجموعی</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $i => $item)
                                    <?php 
                                        $legacy = DB::table(str_replace('App\\', '', $item->type) == 'Carpet' ? 'carpets' : 'material_stocks')
                                            ->where(str_replace('App\\', '', $item->type) == 'Carpet' ? 'carpet_id' : 'id', $item->ref_id)
                                            ->first();
                                    ?>
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item->type == 'App\Carpet' ? 'قالین' : 'مواد' }}</td>
                                        <td>{{ $item->ref_id }}</td>
                                        <td>
                                            @if($item->type == 'App\Carpet')
                                                {{ $legacy->carpet_no ?? 'N/A' }}
                                            @elseif($legacy)
                                                @php 
                                                    $mType = DB::table('material_types')->where('id', $legacy->material_type)->first();
                                                    $mCat = DB::table('material_categories')->where('id', $legacy->material_category)->first();
                                                @endphp
                                                {{ ($mType->type ?? 'N/A') . ' - ' . ($mCat->category ?? 'N/A') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ number_format($item->stock_balance, 2) }}</td>
                                        <td>{{ number_format($item->area_balance, 2) }}</td>
                                        <td>${{ number_format($item->current_cost, 2) }}</td>
                                        <td>${{ number_format($item->total_value, 2) }}</td>
                                        <td>
                                            <a href="{{ route('inventory.reports.detail', $item->id) }}" class="btn btn-info btn-sm">جزئیات</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
