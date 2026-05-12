@extends('dsh.master')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }
    .order-header-mini {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container-fluid">
    <div class="order-header-mini glass-card" style="direction: rtl; text-align: right;">
        <h3 class="text-white mb-0">{{$customer->customer_name}} - مدیریت فرمایشات</h3>
        <p class="mb-0 opacity-75">کشور: {{ $customer->customer_country }}</p>
    </div>

    <div class="row">
        <div class="col-lg-12 hideOnPrint">
            <div class="card glass-card mb-4">
                <div class="card-header" style="direction: rtl; text-align: right;">
                    <h5 class="mb-0">{{ $orderEdit ? 'ویرایش فرمایش' : 'ثبت فرمایش جدید' }}</h5>
                </div>
                
                <!-- Dari Explanation Alert -->
                <div class="alert alert-info mx-3 mt-3" style="direction: rtl; text-align: right; border-radius: 10px; border: none; background: #e8f5e9;">
                    <h5 class="text-success"><i class="fa fa-file-invoice mr-2"></i> مدیریت فرمایشات و اتصال به سیستم مالی</h5>
                    <p class="mb-2 small">در این مرحله، شما یک "پروژه" یا "قرارداد" جدید برای مشتری ایجاد می‌کنید. این فرمایش به صورت خودکار به سیستم حسابداری متصل می‌شود.</p>
                    <div class="row small mt-2">
                        <div class="col-md-4"><strong>شماره فرمایش:</strong> نام طرح، نمبر قرارداد یا یک کد شناسایی برای این فرمایش وارد کنید.</div>
                        <div class="col-md-4"><strong>حساب اصلی مشتری:</strong> این مهم‌ترین بخش است! با انتخاب حساب اصلی، تمام عواید و طلبات این فرمایش به صورت خودکار در دفتر کل (Ledger) مشتری ثبت می‌شود.</div>
                        <div class="col-md-4"><strong>دکمه جزئیات (Details):</strong> بعد از ذخیره، روی دکمه Details کلیک کنید تا مشخصات دقیق هر قالین را ثبت نمایید.</div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ $orderEdit ? '/dashboard/customer-orders/'.$orderEdit->co_id : '/dashboard/customer-orders' }}" method="post">
                        @csrf
                        @if($orderEdit) {{ method_field('patch') }} @endif
                        
                        <input type="hidden" name="customer_account_order_id" value="{{$customer->c_id}}">

                        <div class="row align-items-end" style="direction: rtl; text-align: right;">
                            <div class="col-md-3">
                                <label class="small font-weight-bold">نمبر/نام فرمایش</label>
                                <input type="text" name="order_name" class="form-control" value="{{ optional($orderEdit)->order_name ?? '' }}" required>
                                <small class="text-muted d-block mt-1">یک نام یا نمبر برای شناسایی این فرمایش وارد کنید.</small>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold">تاریخ فرمایش</label>
                                <input type="date" name="order_date" class="form-control" value="{{ optional($orderEdit)->order_date ?? date('Y-m-d') }}" required>
                                <small class="text-muted d-block mt-1">تاریخ ثبت این فرمایش را انتخاب کنید.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="small font-weight-bold">اتصال به حساب اصلی (حساب دفتر کل)</label>
                                <select name="customer_id" class="form-control select2" required>
                                    <option value="">انتخاب حساب مشتری</option>
                                    @foreach($main_customers as $mc)
                                        <option value="{{$mc->id}}" {{ (is_object($orderEdit) && $orderEdit->main_customer_id == $mc->id) ? 'selected' : '' }}>
                                            {{$mc->name}} ({{$mc->country}})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">این فرمایش به حساب مالی این مشتری وصل خواهد شد.</small>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-save"></i> ذخیره فرمایش
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card glass-card">
                <div class="card-header d-flex justify-content-between" style="direction: rtl;">
                    <h5 class="mb-0">فرمایشات موجود</h5>
                    <button class="btn btn-sm btn-light" onclick="window.print()"><i class="fa fa-print"></i> چاپ</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pr-4 text-right">نام / نمبر فرمایش</th>
                                    <th class="text-right">تاریخ فرمایش</th>
                                    <th class="text-right">حساب متصل</th>
                                    <th class="text-left pl-4">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer_orders as $co)
                                    @php 
                                        $linkedCust = DB::table('customers')->where('id', $co->main_customer_id)->first();
                                    @endphp
                                    <tr>
                                        <td class="pr-4 text-right font-weight-bold text-primary">
                                            <i class="fa fa-folder-open ml-2"></i> {{ $co->order_name }}
                                        </td>
                                        <td class="text-right">{{ $co->order_date }}</td>
                                        <td class="text-right">
                                            @if($linkedCust)
                                                <span class="badge badge-info">{{ $linkedCust->name }}</span>
                                            @else
                                                <span class="badge badge-warning">تایید نشده (Unlinked)</span>
                                            @endif
                                        </td>
                                        <td class="text-left pl-4">
                                            <a href="/dashboard/customer-order-details/{{$co->co_id}}" class="btn btn-sm btn-outline-primary ml-1">
                                                <i class="fa fa-list"></i> جزئیات
                                            </a>
                                            <a href="/dashboard/customer-orders/{{$co->co_id}}/edit" class="btn btn-sm btn-outline-info ml-1">
                                                <i class="fa fa-edit"></i> ویرایش
                                            </a>
                                            <button onclick="deleteOrder({{$co->co_id}})" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i> حذف
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
            title: "Are you sure?",
            text: "This will delete the order and all its technical specifications!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    url: '/dashboard/customer-orders/' + id,
                    data: { _token: '{{csrf_token()}}' },
                    success: function (res) {
                        if (res.status == 'success') {
                            location.reload();
                        } else {
                            swal("Error", "Could not delete order", "error");
                        }
                    }
                });
            }
        });
    }
    $('.select2').select2({ width: '100%' });
</script>
@endsection
