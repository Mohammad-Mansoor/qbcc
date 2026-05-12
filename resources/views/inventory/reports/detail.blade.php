@extends('dsh.master')
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>جزئیات تراکنش های جنس (Audit Trail)</h4>
                    <a href="{{ route('inventory.reports.index') }}" class="btn btn-secondary btn-sm" style="float: left;">برگشت</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>اطلاعات پایه:</h5>
                            <table class="table table-sm">
                                <tr><th>نوع:</th><td>{{ $item->type == 'App\Carpet' ? 'قالین' : 'مواد' }}</td></tr>
                                <tr><th>شناسه سابقه:</th><td>{{ $item->ref_id }}</td></tr>
                                <tr><th>قیمت فی واحد فعلی (WAC):</th><td>${{ number_format($item->current_cost, 2) }}</td></tr>
                            </table>
                        </div>
                    </div>

                    <h5>تاریخچه تراکنش ها:</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-xs">
                            <thead>
                                <tr>
                                    <th>تاریخ</th>
                                    <th>نوعیت</th>
                                    <th>گدام</th>
                                    <th>جهت</th>
                                    <th>تعداد</th>
                                    <th>فی واحد</th>
                                    <th>ارزش کل</th>
                                    <th>توسط</th>
                                    <th>توضیحات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $t)
                                    <?php 
                                        $warehouse = DB::table('warehouses')->where('id', $t->warehouse_id)->first();
                                        $user = DB::table('users')->where('id', $t->created_by)->first();
                                    ?>
                                    <tr>
                                        <td>{{ $t->created_at }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $t->type }}</span>
                                            @if($t->is_value_adjustment)
                                                <span class="badge badge-warning">تعدیل قیمت</span>
                                            @endif
                                        </td>
                                        <td>{{ $warehouse->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($t->direction == 'IN')
                                                <span class="text-success"><i class="fas fa-arrow-down"></i> ورودی</span>
                                            @else
                                                <span class="text-danger"><i class="fas fa-arrow-up"></i> خروجی</span>
                                            @endif
                                        </td>
                                        <td>{{ $t->quantity }}</td>
                                        <td>${{ number_format($t->unit_cost, 2) }}</td>
                                        <td>${{ number_format($t->total_cost, 2) }}</td>
                                        <td>{{ $user->name ?? 'Unknown' }}</td>
                                        <td>{{ $t->reference_type }} #{{ $t->reference_id }}</td>
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
