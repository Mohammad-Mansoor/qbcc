@extends('dsh.master')
@section('title', 'Miscellaneous Money Requests - QBCC Forensic ERP')

@section('content')
<style>
    /* QBCC PREMIUM GLASSMORPHISM SYSTEM */
    :root {
        --qbcc-primary: #1e3a8a;
        --qbcc-secondary: #3b82f6;
        --qbcc-header-bg: #ffffff;
        --qbcc-border: #e2e8f0;
        --qbcc-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.8);
        --radius-xl: 20px;
        --radius-lg: 12px;
        --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .page-header-modern {
        background: var(--qbcc-header-bg);
        border-bottom: 1px solid var(--qbcc-border);
        margin-bottom: 25px;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
    }

    /* TABLE SYSTEM */
    .glass-card { background: white; border: 1px solid var(--qbcc-border); border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
    .table-modern { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .table-modern thead th { background: #f8fafc; padding: 15px; font-weight: 700; color: #64748b; font-size: 12px; border: none; }
    .table-modern tbody tr { background: white; transition: all 0.2s; }
    .table-modern tbody tr:hover { background: #f8fafc; }
    .table-modern td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }

    .type-badge { font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; }
    .badge-receipt { background: #d1fae5; color: #065f46; }
    .badge-payment { background: #fee2e2; color: #991b1b; }
    
    .forensic-tag { font-size: 10px; font-weight: 800; background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px; }
</style>

<div class="row">
    <div class="col-sm-12">
        <!-- HEADER -->
        <div class="page-header-modern d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1 text-dark font-weight-bold">درخواست‌های تایید نشده پول (حسابات متفرقه)</h3>
                <p class="text-muted mb-0 small"><i class="feather icon-shield text-success mr-1"></i> صف تایید تراکنش‌های مالی متفرقه (Forensic Ready)</p>
            </div>
            <div class="hideOnPrint">
                <button class="btn btn-white shadow-sm border rounded-lg px-3" onclick="window.print()">
                    <i class="feather icon-printer mr-1"></i> چاپ لیست
                </button>
            </div>
        </div>

        @if(session("status"))
            <div class="alert alert-success border-0 shadow-sm mb-4">{{session('status')}}</div>
        @endif

        <!-- TABLE CARD -->
        <div class="card glass-card">
            <div class="card-body">
                <div class="table-responsive" id="MRDetails">
                    <table class="table-modern" id="dataTable">
                        <thead>
                            <tr>
                                <th>نام حساب</th>
                                <th>نوعیت</th>
                                <th>مبلغ درخواستی</th>
                                <th>نرخ تبدیل</th>
                                <th>معادل دالر (USD)</th>
                                <th>تفصیلات</th>
                                <th>تاریخ</th>
                                <th class="text-center hideOnPrint">عملیات تایید</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($requests) > 0)
                                @foreach($requests as $r)
                                <tr class="ur{{$r->id}}">
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ $r->account->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">ID: #{{ $r->account_id }}</div>
                                    </td>
                                    <td>
                                        <span class="type-badge {{ $r->type == 'رسید' ? 'badge-receipt' : 'badge-payment' }}">
                                            {{ $r->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold" dir="ltr">
                                            {{ number_format($r->amount, 2) }} 
                                            <span class="small text-muted">{{ $r->currency_code }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted" dir="ltr">1 {{ $r->currency_code }} = {{ number_format($r->exchange_rate, 4) }} USD</div>
                                    </td>
                                    <td>
                                        <div class="text-primary font-weight-bold" dir="ltr">
                                            {{ number_format($r->base_amount, 2) }} <small>USD</small>
                                        </div>
                                    </td>
                                    <td class="small">{{ $r->description }}</td>
                                    <td>{{ $r->date }}</td>
                                    <td class="text-center hideOnPrint">
                                        <div class="btn-group">
                                            <button onclick="approveRequest({{$r->id}})" class="btn btn-sm btn-light-success text-success rounded-lg mr-1" title="تایید">
                                                <i class="feather icon-check"></i> تایید
                                            </button>
                                            <button onclick="deleteRequest({{$r->id}})" class="btn btn-sm btn-light-danger text-danger rounded-lg" title="رد کردن">
                                                <i class="feather icon-trash-2"></i> رد کردن
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="feather icon-inbox display-4 d-block mb-3"></i>
                                            <h5>هیچ درخواستی در صف انتظار نیست</h5>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/feather-icons"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    function approveRequest(id) {
        swal({
            title: "تایید تراکنش مالی؟",
            text: "آیا از تایید این پرداخت و درج آن در سیستم مالی اطمینان دارید؟",
            icon: "info",
            buttons: {
                cancel: "انصراف",
                confirm: { text: "بله، تایید شود", className: "btn-success" }
            }
        }).then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    type: 'DELETE',
                    data: { '_token': '{{csrf_token()}}' },
                    url: '/dashboard/different-account-approve-request-money/' + id,
                    success: function (res) {
                        if (res.status == 'success') {
                            $('.ur' + id).fadeOut(500, function() { $(this).remove(); });
                            swal("تراکنش با موفقیت تایید و ثبت شد.", { icon: "success" });
                        } else {
                            swal("خطا در تایید تراکنش!", { icon: "error" });
                        }
                    }
                });
            }
        });
    }

    function deleteRequest(id) {
        swal({
            title: "رد کردن درخواست؟",
            text: "آیا از حذف این درخواست اطمینان دارید؟ این عمل قابل بازگشت نیست.",
            icon: "warning",
            buttons: {
                cancel: "انصراف",
                confirm: { text: "بله، حذف شود", className: "btn-danger" }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    data: { '_token': '{{csrf_token()}}' },
                    url: '/dashboard/different-account-delete-request-money/' + id,
                    success: function (res) {
                        $('.ur' + id).fadeOut(500, function() { $(this).remove(); });
                        swal("درخواست با موفقیت حذف گردید.", { icon: "info" });
                    }
                });
            }
        });
    }
</script>
@endsection