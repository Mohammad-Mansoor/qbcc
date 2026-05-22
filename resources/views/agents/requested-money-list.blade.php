@extends('dsh.master')

@section('content')
<style>
    :root {
        --qbcc-primary: #1e3a8a;
        --qbcc-secondary: #3b82f6;
        --qbcc-header-bg: #ffffff;
        --qbcc-border: #e2e8f0;
        --qbcc-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        --radius-xl: 20px;
        --radius-lg: 12px;
        --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        --transition-bounce: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        --color-success: #2ec4b6;
        --color-success-light: rgba(46, 196, 182, 0.1);
        --color-primary: #4361ee;
        --color-primary-light: rgba(67, 97, 238, 0.1);
        --color-warning: #ff9f1c;
        --color-warning-light: rgba(255, 159, 28, 0.1);
        --color-danger: #e71d36;
        --color-danger-light: rgba(231, 29, 54, 0.1);
        --color-text-main: #2b2d42;
        --color-text-muted: #8d99ae;
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.8);
    }

    .page-header-modern {
        background: var(--qbcc-header-bg);
        border-bottom: 1px solid var(--qbcc-border);
        margin-bottom: 25px;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
    }

    body {
        background: #f8f9fa;
        color: var(--color-text-main);
    }

    /* DASHBOARD CARD */
    .ux-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-soft);
        padding: 32px;
        margin-bottom: 24px;
        animation: fadeUp 0.6s ease-out backwards;
    }

    /* TABLE STYLES */
    .ux-table-wrap {
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .ux-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .ux-table th {
        background: #f1f5f9;
        padding: 16px 20px;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-align: right;
    }
    .ux-table td {
        padding: 18px 20px;
        background: white;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
        vertical-align: middle;
    }
    .ux-table tr:last-child td { border-bottom: none; }
    .ux-table tr:hover td { background: #f8fafc; }

    /* BADGES */
    .ux-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: var(--transition-bounce);
    }
    .ux-badge.info { background: var(--color-primary-light); color: var(--color-primary); }
    .ux-badge.success { background: var(--color-success-light); color: var(--color-success); }
    .ux-badge.warning { background: var(--color-warning-light); color: var(--color-warning); }
    .ux-badge.danger { background: var(--color-danger-light); color: var(--color-danger); }

    /* BUTTONS */
    .ux-btn {
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: var(--transition-bounce);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .ux-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .ux-btn:active { transform: scale(0.96); }
    .ux-btn-success { background: var(--color-success); color: white; }
    .ux-btn-danger { background: var(--color-danger); color: white; }
    .ux-btn-icon {
        width: 38px;
        height: 38px;
        padding: 0;
        justify-content: center;
    }

    /* KEYFRAMES */
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeUp { 
        from { opacity: 0; transform: translateY(20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }

    [dir="rtl"] .ux-header-title { text-align: right; }
    [dir="rtl"] .ux-table th, [dir="rtl"] .ux-table td { text-align: right; }
</style>

<div class="container-fluid py-4">
    <div class="page-header-modern d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 text-dark font-weight-bold">درخواست‌های تایید نشده پول</h3>
            <p class="text-muted mb-0 small"><i class="feather icon-shield text-success mr-1"></i> تایید مالی و پرداخت‌های نمایندگان</p>
        </div>
        <button class="btn btn-primary rounded-lg px-4 font-weight-bold shadow-sm hideOnPrint" onclick="window.print()" style="height: 45px;">
            <i class="feather icon-printer mr-1"></i> چاپ لیست
        </button>
    </div>

    <div class="ux-card">
        <div id="MRDetails">
            <!-- ALERTS -->
            <div class="alert alert-success approve" style="display:none; border-radius: 12px;" role="alert">
                <i class="feather icon-check-circle"></i> پرداخت با موفقیت تایید شد.
            </div>
            <div class="alert alert-danger deleteAlert" style="display:none; border-radius: 12px;" role="alert">
                <i class="feather icon-x-circle"></i> درخواست رد شد.
            </div>
            <div class="alert alert-danger errorAlert" style="display:none; border-radius: 12px;" role="alert">
                <i class="feather icon-alert-triangle"></i> خطا: موجودی دخل کافی نیست.
            </div>

            <div class="ux-table-wrap mt-4">
                <table class="ux-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>نام نماینده</th>
                            <th>نوعیت</th>
                            <th>مبلغ اصلی</th>
                            <th>ارز</th>
                            <th>نرخ تبادله</th>
                            <th>ارزش (USD)</th>
                            <th>تاریخ</th>
                            <th class="hideOnPrint">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $r)
                            @php
                                $agent = \App\Agents::find($r->agent_id);
                                $agent_name = $agent->user->name ?? 'نامعلوم';
                                
                                // Forensic Data Extraction
                                $currency = $r->currency_code ?: ($r->amount > 0 ? 'USD' : 'AFN');
                                $original = $r->original_amount ?: ($r->amount > 0 ? $r->amount : $r->amount_af);
                                $rate = $r->exchange_rate ?: ($r->dollar_rate ?: 1);
                                $base = $r->base_amount ?: ($r->amount ?: ($r->amount_af * 0.012)); // Approx if old
                                
                                $typeClass = $r->type == 'رسید' ? 'success' : 'warning';
                            @endphp
                            <tr class="ur{{$r->id}}">
                                <td style="font-weight: 800; color: var(--color-text-main);">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:36px; height:36px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:1.1rem; color:var(--color-primary);">
                                            <i class="feather icon-user"></i>
                                        </div>
                                        {{ $agent_name }}
                                    </div>
                                </td>
                                <td>
                                    <span class="ux-badge {{ $typeClass }}">
                                        {{ $r->type }}
                                    </span>
                                </td>
                                <td style="font-weight: 700;">{{ number_format($original, 2) }}</td>
                                <td>
                                    <span class="ux-badge info">{{ $currency }}</span>
                                </td>
                                <td style="color: var(--color-text-muted); font-family: monospace;">{{ number_format($rate, 4) }}</td>
                                <td style="font-weight: 800; color: var(--color-success);">
                                    ${{ number_format($base, 2) }}
                                </td>
                                <td style="color: var(--color-text-muted);">{{ $r->date }}</td>
                                <td class="hideOnPrint">
                                    <div style="display:flex; gap:8px;">
                                        <button onclick="approveRequest({{$r->id}})" class="ux-btn ux-btn-success ux-btn-icon" title="تایید پرداخت">
                                            <i class="feather icon-check"></i>
                                        </button>
                                        <button onclick="deleteRequest({{$r->id}})" class="ux-btn ux-btn-danger ux-btn-icon" title="رد نمودن">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 64px; color: var(--color-text-muted);">
                                    <div style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;">
                                        <i class="feather icon-inbox"></i>
                                    </div>
                                    <h5 style="font-weight: 700;">هیچ درخواستی در صف انتظار نیست</h5>
                                    <p>All money requests have been processed.</p>
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
<script src="https://unpkg.com/feather-icons"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    function approveRequest(id) {
        swal({
            title: "تایید درخواست پول",
            text: "آیا از تایید این پرداخت و انتقال آن به دفاتر حسابداری اطمینان دارید؟",
            icon: "warning",
            buttons: {
                cancel: { text: "انصراف", visible: true, className: "btn btn-default" },
                confirm: { text: "بلی، تایید شود", className: "btn btn-success" }
            },
            dangerMode: false,
        }).then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    type: 'DELETE', // Method is DELETE in existing controller route for some reason
                    data: { '_token': '{{csrf_token()}}' },
                    url: '/dashboard/agent-approve-request-money/' + id,
                    success: function (res) {
                        if (res.status == 'success') {
                            $('.ur' + id).fadeOut(600, function() { $(this).remove(); });
                            $('.approve').fadeIn().delay(3000).fadeOut();
                        } else {
                            $('.errorAlert').fadeIn().delay(3000).fadeOut();
                        }
                    }
                });
            }
        });
    }

    function deleteRequest(id) {
        swal({
            title: "رد درخواست پول",
            text: "آیا مطمئن هستید که می‌خواهید این درخواست را حذف کنید؟",
            icon: "error",
            buttons: {
                cancel: { text: "انصراف", visible: true },
                confirm: { text: "بلی، حذف شود", className: "btn btn-danger" }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: 'DELETE',
                    data: { '_token': '{{csrf_token()}}' },
                    url: '/dashboard/agent-delete-request-money/' + id,
                    success: function (res) {
                        $('.ur' + id).fadeOut(600, function() { $(this).remove(); });
                        $('.deleteAlert').fadeIn().delay(3000).fadeOut();
                    }
                });
            }
        });
    }

    function printPage(divName) {
        window.print();
    }
</script>
@endsection