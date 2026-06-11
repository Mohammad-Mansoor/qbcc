@extends('dsh.master')
@section('title', 'سلیپ معاش — ' . $item->name)
@section('content')

<div class="container py-4" id="salary-slip">

    {{-- Print / Back buttons (hidden on print) --}}
    <div class="row mb-3 hideOnPrint">
        <div class="col-12 text-left">
            <button onclick="window.print()" class="btn btn-primary shadow-sm px-5 font-weight-bold">
                <i class="fa fa-print mr-2"></i> چاپ سلیپ
            </button>
            <a href="{{ route('payroll.show', $run->id) }}" class="btn btn-light shadow-sm px-4 ml-2">
                <i class="fa fa-arrow-left mr-1"></i> بازگشت
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════ SALARY SLIP ═══════════════════════════ --}}
    <div class="card border shadow-lg" style="max-width:780px; margin:auto; font-family:'Inter','Roboto',sans-serif; border-radius:12px; overflow:hidden;">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%); padding:32px 40px 24px;">
            <div class="row align-items-center">
                <div class="col-6">
                    <h2 style="color:#fff; font-weight:800; margin:0; letter-spacing:2px; font-size:1.4rem;">QBIC ERP SYSTEM</h2>
                    <p style="color:#94a3b8; font-size:.82rem; margin:4px 0 0;">کابل، افغانستان</p>
                </div>
                <div class="col-6 text-left">
                    <h4 style="color:#f59e0b; font-weight:700; margin:0;">سلیپ معاش</h4>
                    <p style="color:#94a3b8; font-size:.82rem; margin:4px 0 0;">SALARY SLIP</p>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-6">
                    <p style="color:#94a3b8; font-size:.72rem; text-transform:uppercase; letter-spacing:.5px; margin-bottom:2px;">دوره معاشاتی</p>
                    <p style="color:#f1f5f9; font-weight:700; font-size:1rem; margin:0;">{{ $run->month_year }}</p>
                </div>
                <div class="col-6 text-left">
                    <p style="color:#94a3b8; font-size:.72rem; text-transform:uppercase; letter-spacing:.5px; margin-bottom:2px;">تاریخ اجرا</p>
                    <p style="color:#f1f5f9; font-weight:700; font-size:1rem; margin:0;">{{ \Carbon\Carbon::parse($run->run_date)->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Employee Info Band --}}
        <div style="background:#f0f9ff; border-bottom:2px solid #bae6fd; padding:18px 40px;">
            <div class="row">
                <div class="col-sm-6">
                    <p style="color:#0369a1; font-size:.7rem; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px;">نام کارمند</p>
                    <p style="font-size:1.1rem; font-weight:800; color:#1e293b; margin:0;">{{ $item->name }}</p>
                </div>
                <div class="col-sm-3">
                    <p style="color:#0369a1; font-size:.7rem; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px;">وظیفه</p>
                    <p style="font-size:.9rem; font-weight:600; color:#334155; margin:0;">{{ $item->job_title }}</p>
                </div>
                <div class="col-sm-3">
                    <p style="color:#0369a1; font-size:.7rem; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px;">دیپارتمنت</p>
                    <p style="font-size:.9rem; font-weight:600; color:#334155; margin:0;">{{ $item->department ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Earnings / Deductions Table --}}
        <div style="padding:24px 40px;">
            <table style="width:100%; border-collapse:collapse; font-size:.88rem;">
                <thead>
                    <tr style="background:#1e293b; color:#e2e8f0;">
                        <th style="padding:10px 14px; text-align:right; border-radius:6px 0 0 6px;">تفصیل</th>
                        <th style="padding:10px 14px; text-align:center;">ارز</th>
                        <th style="padding:10px 14px; text-align:right;" dir="ltr">مبلغ (ارز قرارداد)</th>
                        <th style="padding:10px 14px; text-align:right; border-radius:0 6px 6px 0; color:#4ade80;" dir="ltr">معادل (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Base Salary --}}
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px 14px; font-weight:600; color:#1e293b;">معاش پایه (Base Salary)</td>
                        <td style="padding:12px 14px; text-align:center;">
                            <span style="background:#dbeafe;color:#1d4ed8;padding:2px 10px;border-radius:20px;font-size:.75rem;font-weight:700;">
                                {{ $item->currency_code ?? 'USD' }}
                            </span>
                        </td>
                        <td style="padding:12px 14px; text-align:right; font-weight:700;" dir="ltr">
                            {{ number_format($item->base_salary, 2) }} {{ $item->currency_code ?? 'USD' }}
                        </td>
                        <td style="padding:12px 14px; text-align:right; font-weight:700; color:#059669;" dir="ltr">
                            ${{ number_format($item->base_salary_usd ?? $item->base_salary, 2) }}
                        </td>
                    </tr>

                    {{-- Bonus --}}
                    @if($item->bonus > 0)
                    <tr style="border-bottom:1px solid #f1f5f9; background:#f0fdf4;">
                        <td style="padding:12px 14px; font-weight:600; color:#15803d;">بونس / اضافه کاری (Bonus)</td>
                        <td style="padding:12px 14px; text-align:center;">
                            <span style="background:#dcfce7;color:#16a34a;padding:2px 10px;border-radius:20px;font-size:.75rem;font-weight:700;">+</span>
                        </td>
                        <td style="padding:12px 14px; text-align:right; color:#16a34a; font-weight:700;" dir="ltr">
                            +{{ number_format($item->bonus, 2) }}
                        </td>
                        <td style="padding:12px 14px; text-align:right; color:#16a34a; font-weight:700;" dir="ltr">
                            +${{ number_format($item->bonus * ($item->exchange_rate ?? 1), 2) }}
                        </td>
                    </tr>
                    @endif

                    {{-- Deductions --}}
                    @if($item->deductions > 0)
                    <tr style="border-bottom:1px solid #f1f5f9; background:#fff1f2;">
                        <td style="padding:12px 14px; font-weight:600; color:#b91c1c;">کسرات / جریمه (Deductions)</td>
                        <td style="padding:12px 14px; text-align:center;">
                            <span style="background:#fee2e2;color:#dc2626;padding:2px 10px;border-radius:20px;font-size:.75rem;font-weight:700;">−</span>
                        </td>
                        <td style="padding:12px 14px; text-align:right; color:#dc2626; font-weight:700;" dir="ltr">
                            -{{ number_format($item->deductions, 2) }}
                        </td>
                        <td style="padding:12px 14px; text-align:right; color:#dc2626; font-weight:700;" dir="ltr">
                            -${{ number_format($item->deductions * ($item->exchange_rate ?? 1), 2) }}
                        </td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr style="background:#0f172a; color:#fff; border-radius:0 0 8px 8px;">
                        <td style="padding:14px 14px; font-weight:800; font-size:1rem;" colspan="2">خالص معاش (NET SALARY)</td>
                        <td style="padding:14px 14px; text-align:right; font-size:1rem; font-weight:800;" dir="ltr">
                            {{ number_format($item->net_salary, 2) }} {{ $item->currency_code ?? 'USD' }}
                        </td>
                        <td style="padding:14px 14px; text-align:right; font-size:1.1rem; font-weight:900; color:#4ade80;" dir="ltr">
                            ${{ number_format($item->net_salary_usd ?? $item->net_salary, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- FX Note --}}
            @if(($item->exchange_rate ?? 1) != 1)
            <div style="margin-top:12px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:8px 14px; font-size:.78rem; color:#92400e;">
                <i class="fa fa-exchange mr-1"></i>
                نرخ تبدیل ارز اعمال شده:
                <strong>1 {{ $item->currency_code }} = {{ number_format($item->exchange_rate, 8) }} USD</strong>
                (ثبت شده در تاریخ قرارداد — Forensic FX Snapshot)
            </div>
            @endif
        </div>

        {{-- Signatures --}}
        <div style="padding:20px 40px 30px; border-top:2px dashed #e2e8f0;">
            <div class="row text-center">
                <div class="col-4">
                    <div style="border-top:1px solid #94a3b8; padding-top:8px; margin:0 12px; color:#64748b; font-size:.75rem; font-weight:600;">امضای کارمند</div>
                </div>
                <div class="col-4">
                    <div style="border-top:1px solid #94a3b8; padding-top:8px; margin:0 12px; color:#64748b; font-size:.75rem; font-weight:600;">مهر شرکت</div>
                </div>
                <div class="col-4">
                    <div style="border-top:1px solid #94a3b8; padding-top:8px; margin:0 12px; color:#64748b; font-size:.75rem; font-weight:600;">امضای مدیر مالی</div>
                </div>
            </div>
        </div>

        {{-- Footer watermark --}}
        <div style="background:#f8fafc; padding:8px 40px; text-align:center; font-size:.7rem; color:#94a3b8; border-top:1px solid #e2e8f0;">
            این سلیپ توسط سیستم QBIC ERP به صورت خودکار تولید شده است — مرجع دفتر کل: <strong>PAY-{{ $run->month_year }}</strong>
        </div>
    </div>

</div>

<style>
    @media print {
        .hideOnPrint { display: none !important; }
        body { background: white !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; margin: 0 !important; max-width: 100% !important; }
        #salary-slip .container { padding: 0 !important; }
    }
    .rounded-lg { border-radius: 1rem !important; }
</style>
@endsection
