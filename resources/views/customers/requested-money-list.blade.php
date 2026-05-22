@extends('dsh.master')

@section('content')
<div class="container-fluid py-4" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Header Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 font-weight-bold text-dark">تایید درخواست‌های واریز/برداشت مشتریان</h3>
            <p class="text-muted mb-0">لیست پرداخت‌های معلق در انتظار تایید توسط مدیر سیستم</p>
        </div>
        <div class="hideOnPrint">
            <button class="btn btn-secondary btn-sm px-4 shadow-sm" onclick="printPage('MRDetails')">
                <i class="fa fa-print"></i> چاپ لیست
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <div class="hideOnPrint">
        <div class="alert alert-success approve text-center" style="display:none; border-radius: 8px;" role="alert">
            <i class="fa fa-check-circle mr-1"></i> پرداخت با موفقیت تایید و به دفتر کل (GL) ارسال شد.
        </div>
        <div class="alert alert-danger deleteAlert text-center" style="display:none; border-radius: 8px;" role="alert">
            <i class="fa fa-times-circle mr-1"></i> درخواست پرداخت رد و حذف گردید.
        </div>
        <div class="alert alert-danger errorAlert text-center" style="display:none; border-radius: 8px;" role="alert">
            <i class="fa fa-warning mr-1"></i> خطا: موجودی صندوق/حساب برای انجام این پرداخت کافی نیست!
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 font-weight-bold text-dark">لیست درخواست‌های تایید نشده</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" id="MRDetails">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="bg-light text-secondary font-weight-bold" style="font-size: 0.85rem;">
                        <tr>
                            <th class="py-3 px-4">نام مشتری</th>
                            <th class="py-3 text-left">مبلغ اصلی (اسعار)</th>
                            <th class="py-3 text-left">نرخ تبادله</th>
                            <th class="py-3 text-left">معادل دالر (USD)</th>
                            <th class="py-3 text-center">نوعیت</th>
                            <th class="py-3">انوایس نمبر</th>
                            <th class="py-3">تفصیلات</th>
                            <th class="py-3">تاریخ درخواست</th>
                            <th class="py-3 hideOnPrint text-center" style="width: 130px;">تایید</th>
                            <th class="py-3 hideOnPrint text-center" style="width: 130px;">رد درخواست</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $r)
                            <tr class="ur{{$r->id}}">
                                <td class="px-4 font-weight-bold text-dark">
                                    {{ $r->agent ? $r->agent->name : 'مشتری نامشخص' }}
                                </td>
                                <td class="text-left font-weight-bold text-dark" dir="ltr">
                                    {{ number_format($r->original_amount ?? ($r->amount > 0 ? $r->amount : $r->amount_af), 2) }}
                                    <span class="badge badge-light border text-muted font-weight-normal">{{ $r->currency_code ?? ($r->amount > 0 ? 'USD' : 'AFN') }}</span>
                                </td>
                                <td class="text-left text-muted" dir="ltr" style="font-size: 0.85rem;">
                                    {{ number_format($r->exchange_rate ?? ($r->amount > 0 ? 1.0 : (1 / ($r->dollar_rate > 0 ? $r->dollar_rate : 1))), 8) }}
                                </td>
                                <td class="text-left font-weight-bold text-primary" dir="ltr">
                                    ${{ number_format($r->base_amount ?? ($r->amount > 0 ? $r->amount : ($r->amount_af * ($r->exchange_rate ?? 1.0))), 2) }}
                                </td>
                                <td class="text-center">
                                    @if($r->type == 'رسید')
                                        <span class="badge badge-success px-2 py-1">رسید</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1">گرفت</span>
                                    @endif
                                </td>
                                <td>
                                    @if($r->invoice_number == 'نقد')
                                        <span class="text-muted">نقد</span>
                                    @else
                                        <span class="font-weight-bold">{{ $r->invoice_number }}</span>
                                    @endif
                                </td>
                                <td>{{ $r->description }}</td>
                                <td>{{ $r->date }}</td>
                                <td class="hideOnPrint text-center">
                                    <button onclick="approveRequest({{$r->id}})" class="btn btn-sm btn-outline-success px-3" style="border-radius: 6px;">
                                        <i class="fa fa-check mr-1"></i> تایید
                                    </button>
                                </td>
                                <td class="hideOnPrint text-center">
                                    <button onclick="deleteRequest({{$r->id}})" class="btn btn-sm btn-outline-danger px-3" style="border-radius: 6px;">
                                        <i class="fa fa-times mr-1"></i> رد کردن
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fa-2x mb-3 text-light-gray d-block"></i>
                                    هیچ درخواست پرداختی در حال حاضر معلق نیست.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function approveRequest(id) {
        swal({
            text: "آیا از تایید این پرداخت و ثبت آن در سیستم حسابداری مطمئن هستید؟",
            buttons: true,
            dangerMode: false,
            buttons: {
                confirm: {text: 'بلی، تایید شود', className: 'btn-success'},
                cancel: 'انصراف'
            },
        }).then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    type: 'DELETE',
                    data: {
                        '_token': '{{csrf_token()}}',
                    },
                    url: '/dashboard/customer-approve-request/' + id,
                    success: function (res) {
                        if (res.status == 'success') {
                            $('.ur' + id).fadeOut(400, function() { $(this).remove(); });
                            $('.approve').fadeIn(200);
                            setTimeout(function() {
                                $('.approve').fadeOut(300);
                            }, 3000);
                        } else {
                            $('.errorAlert').fadeIn(200);
                            setTimeout(function() {
                                $('.errorAlert').fadeOut(300);
                            }, 3000);
                        }
                    },
                    error: function() {
                        $('.errorAlert').fadeIn(200);
                        setTimeout(function() {
                            $('.errorAlert').fadeOut(300);
                        }, 3000);
                    }
                });
            }
        });
    }

    function deleteRequest(id) {
        swal({
            text: "آیا از رد و حذف این درخواست پرداخت مطمئن هستید؟",
            buttons: true,
            dangerMode: true,
            buttons: {
                confirm: {text: 'بلی، رد شود', className: 'btn-danger'},
                cancel: 'انصراف'
            },
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    data: {
                        '_token': '{{csrf_token()}}',
                    },
                    url: '/dashboard/customer-delete-request/' + id,
                    success: function (res) {
                        $('.ur' + id).fadeOut(400, function() { $(this).remove(); });
                        $('.deleteAlert').fadeIn(200);
                        setTimeout(function() {
                            $('.deleteAlert').fadeOut(300);
                        }, 3000);
                    }
                });
            }
        });
    }
</script>
@endsection