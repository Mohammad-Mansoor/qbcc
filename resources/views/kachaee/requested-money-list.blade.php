@extends('dsh.master')
@section('title', 'لیست درخواست های پول کچایی')

@section('content')
    @php
        $totalPendingCount = count($requests);
        $totalPendingUSD = 0;
        $totalPendingReceiptsUSD = 0;
        $totalPendingPaymentsUSD = 0;

        foreach ($requests as $r) {
            $usdVal = $r->base_amount ?: ($r->amount > 0 ? $r->amount : ($r->amount_af > 0 ? ($r->amount_af / ($r->exchange_rate ?: 1)) : 0.00));
            $totalPendingUSD += $usdVal;
            if ($r->type == 'رسید') {
                $totalPendingReceiptsUSD += $usdVal;
            } elseif ($r->type == 'گرفت') {
                $totalPendingPaymentsUSD += $usdVal;
            }
        }
    @endphp

    <style>
        /* PREMIUM GLASSMORPHISM UI */
        .glass-card {
            background: white;
            border: 1px solid var(--QBIC-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .glass-header {
            background: var(--QBIC-surface);
            padding: 20px 25px;
            border-bottom: 1px solid var(--QBIC-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .glass-header h4 {
            margin: 0;
            font-weight: 700;
            color: var(--QBIC-primary);
            font-size: 1.2rem;
        }

        .table-modern thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            border: none;
            letter-spacing: 0.5px;
            padding: 15px;
            font-size: 0.8rem;
        }

        .table-modern tbody td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
            color: #334155;
        }

        .table-modern tbody tr:hover {
            background: #f8fafc;
        }

        .badge-premium {
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* ANIMATED STATS CARDS */
        .stat-card {
            border: none;
            border-radius: 16px;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: default;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            bottom: -30px;
            left: -30px;
            transition: all 0.5s ease;
        }

        .stat-card:hover::after {
            transform: scale(1.5);
        }

        .stat-card-blue {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .stat-card-indigo {
            background: linear-gradient(135deg, #6366f1, #4338ca);
            color: white;
        }

        .stat-card-green {
            background: linear-gradient(135deg, #10b981, #047857);
            color: white;
        }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.15);
            font-size: 1.4rem;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-card-icon {
            transform: rotate(-10deg) scale(1.1);
            background: rgba(255, 255, 255, 0.25);
        }

        .stat-card-val {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .stat-card-lbl {
            font-size: 0.85rem;
            font-weight: 600;
            opacity: 0.9;
            margin-top: 4px;
        }
    </style>

    <!-- ANIMATED STATS CARDS ROW -->
    <div class="row mb-4 hideOnPrint">
        <!-- Card 1: Total Pending Requests -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-blue p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-card-val">{{ $totalPendingCount }} <span
                                style="font-size: 1rem; font-weight: normal;">درخواست</span></div>
                        <div class="stat-card-lbl">درخواست‌های معلق (Pending Requests)</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fa fa-folder-open-o"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 2: Total Pending USD Volume -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-indigo p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-card-val">${{ number_format($totalPendingUSD, 2) }}</div>
                        <div class="stat-card-lbl">مجموع حجم معلق (Total Pending USD)</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fa fa-usd"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 3: Pending Receipts -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-green p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-card-val">${{ number_format($totalPendingReceiptsUSD, 2) }}</div>
                        <div class="stat-card-lbl">مجموع رسید معلق (Pending Receipts)</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fa fa-arrow-down"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 4: Pending Payments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card p-4 h-100 text-white" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-card-val">${{ number_format($totalPendingPaymentsUSD, 2) }}</div>
                        <div class="stat-card-lbl">مجموع گرفت معلق (Pending Payments)</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fa fa-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="glass-card">
                <div class="glass-header">
                    <h4><i class="fa fa-money text-success mr-2"></i> لیست درخواست‌های پول کچایی</h4>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-primary btn-sm hideOnPrint" onclick="printPage('MRDetails')">
                            <i class="fa fa-print mr-1"></i> چاپ (Print)
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-success approve" style="display:none;" role="alert">
                        <i class="fa fa-check-circle mr-2"></i> پرداخت موفقانه تایید شد
                    </div>

                    <div class="alert alert-danger deleteAlert" style="display:none;" role="alert">
                        <i class="fa fa-times-circle mr-2"></i> درخواست رد شد
                    </div>

                    <div class="alert alert-danger errorAlert" style="display:none;" role="alert">
                        <i class="fa fa-exclamation-triangle mr-2"></i> پول در دخل کم است
                    </div>

                    <div class="static-table-list table-responsive" id="MRDetails">
                        <table class="table table-modern text-center mb-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th>نام کچای گر</th>
                                    <th>نوعیت</th>
                                    <th>معادل دالر (USD Equivalent)</th>
                                    <th>مقدار و اسعار اصلی</th>
                                    <th>کچای نمبر</th>
                                    <th>تفصیلات</th>
                                    <th>تاریخ</th>
                                    @can('approve_kachaee_money_requests')
                                    <th class="hideOnPrint">تایید پرداخت</th>
                                    @endcan
                                    @can('reject_kachaee_money_requests')
                                    <th class="hideOnPrint">رد نمودن</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($requests) > 0)
                                    @foreach($requests as $r)
                                        @php
                                            $kachaee_name = DB::table('kachaees')->where('id', $r->team_id)->first();
                                            $usdEquivalent = $r->base_amount ?: ($r->amount > 0 ? $r->amount : ($r->amount_af > 0 ? ($r->amount_af / ($r->exchange_rate ?: 1)) : 0.00));
                                        @endphp
                                        <tr class="ur{{$r->id}}">
                                            <td class="font-weight-bold text-dark">{{ $kachaee_name ? $kachaee_name->name : 'N/A' }}
                                            </td>

                                            <!-- Transaction Type -->
                                            <td>
                                                @if($r->type == 'رسید')
                                                    <span class="badge badge-success badge-premium"
                                                        style="background: rgba(16, 185, 129, 0.1); color: #10b981;">رسید</span>
                                                @else
                                                    <span class="badge badge-danger badge-premium"
                                                        style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">گرفت</span>
                                                @endif
                                            </td>

                                            <!-- USD Equivalent -->
                                            <td style="direction: ltr;" class="font-weight-bold">
                                                ${{ number_format($usdEquivalent, 2) }}
                                            </td>

                                            <!-- Original Request Currency & Amount Column -->
                                            <td style="direction: ltr;" class="font-weight-bold text-info">
                                                {{ $r->currency_symbol ?: ($r->amount > 0 ? '$' : '؋') }}
                                                {{ number_format($r->original_amount ?: ($r->amount > 0 ? $r->amount : $r->amount_af), 2) }}
                                                <span
                                                    class="badge badge-info ml-1 badge-premium">{{ $r->currency_code ?: ($r->amount > 0 ? 'USD' : 'AFN') }}</span>
                                            </td>

                                            <td>
                                                <span
                                                    class="badge badge-light-secondary badge-premium font-weight-bold">{{ $r->kachaee_number ?: 'N/A' }}</span>
                                            </td>
                                            <td>{{ $r->description }}</td>
                                            <td>{{ $r->date }}</td>

                                            <!-- Approve Button -->
                                            <td class="hideOnPrint">
                                                @can('approve_kachaee_money_requests')
                                                <button onclick="confirmAction({{ $r->id }}, 'approve', {
                                                            teamName: '{{ addslashes($kachaee_name ? $kachaee_name->name : 'N/A') }}',
                                                            type: '{{ $r->type }}',
                                                            originalAmount: '{{ number_format($r->original_amount ?: ($r->amount > 0 ? $r->amount : $r->amount_af), 2) }}',
                                                            currencyCode: '{{ $r->currency_code ?: ($r->amount > 0 ? 'USD' : 'AFN') }}',
                                                            currencySymbol: '{{ $r->currency_symbol ?: ($r->amount > 0 ? '$' : '؋') }}',
                                                            exchangeRate: '{{ number_format($r->exchange_rate ?: 1, 2) }}',
                                                            usdEquivalent: '{{ number_format($usdEquivalent, 2) }}',
                                                            kachaeeNumber: '{{ $r->kachaee_number ?: 'N/A' }}',
                                                            description: '{{ addslashes($r->description) }}',
                                                            date: '{{ $r->date }}'
                                                        })" class="btn btn-sm btn-success shadow-sm rounded-lg font-weight-bold">
                                                    <i class="fa fa-check mr-1"></i> تایید
                                                </button>
                                                @endcan
                                            </td>

                                            <!-- Reject Button -->
                                            <td class="hideOnPrint">
                                                @can('reject_kachaee_money_requests')
                                                <button onclick="confirmAction({{ $r->id }}, 'reject', {
                                                            teamName: '{{ addslashes($kachaee_name ? $kachaee_name->name : 'N/A') }}',
                                                            type: '{{ $r->type }}',
                                                            originalAmount: '{{ number_format($r->original_amount ?: ($r->amount > 0 ? $r->amount : $r->amount_af), 2) }}',
                                                            currencyCode: '{{ $r->currency_code ?: ($r->amount > 0 ? 'USD' : 'AFN') }}',
                                                            currencySymbol: '{{ $r->currency_symbol ?: ($r->amount > 0 ? '$' : '؋') }}',
                                                            exchangeRate: '{{ number_format($r->exchange_rate ?: 1, 2) }}',
                                                            usdEquivalent: '{{ number_format($usdEquivalent, 2) }}',
                                                            kachaeeNumber: '{{ $r->kachaee_number ?: 'N/A' }}',
                                                            description: '{{ addslashes($r->description) }}',
                                                            date: '{{ $r->date }}'
                                                        })" class="btn btn-sm btn-outline-danger rounded-lg font-weight-bold">
                                                    <i class="fa fa-times mr-1"></i> رد کردن
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center py-4 font-weight-bold text-danger">هنوز درخواست پول
                                            ثبت نشده است.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content"
                style="border-radius: 16px; border: none; background: rgba(255, 255, 255, 0.98); box-shadow: 0 20px 50px rgba(0,0,0,0.15); overflow: hidden;">
                <div class="modal-header text-white p-4" id="modalHeader" style="border: none;">
                    <h5 class="modal-title font-weight-bold" id="confirmationModalLabel" style="font-size: 1.2rem;">تایید
                        عملیات</h5>
                    <button type="button" class="close text-white font-weight-bold" data-dismiss="modal" aria-label="Close"
                        style="opacity: 0.8; outline: none; margin: -20px -20px -20px auto; background: none; border: none;">
                        <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-right" style="direction: rtl;">
                    <p class="font-weight-bold mb-3" id="modalDescription" style="font-size: 1rem; color: #334155;"></p>

                    <div class="p-3 mb-3 rounded-lg" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <table class="table table-sm table-borderless mb-0 text-right" style="font-size: 0.9rem;">
                            <tbody>
                                <tr>
                                    <td class="text-muted font-weight-bold" style="width: 40%;">نام کچای گر:</td>
                                    <td id="detailTeamName" class="font-weight-bold text-dark"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-weight-bold">نوعیت تراکنش:</td>
                                    <td><span id="detailType" class="badge badge-premium"></span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-weight-bold">مقدار اصلی:</td>
                                    <td id="detailOriginal" class="font-weight-bold text-info" style="direction: ltr;"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-weight-bold">نرخ تبدیل:</td>
                                    <td id="detailRate" class="font-weight-bold text-secondary" style="direction: ltr;">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-weight-bold">معادل دالر:</td>
                                    <td id="detailUSD" class="font-weight-bold text-dark" style="direction: ltr;"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-weight-bold">کچای نمبر:</td>
                                    <td id="detailKachaeeNumber" class="font-weight-bold text-dark"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-weight-bold">تفصیلات:</td>
                                    <td id="detailDesc" class="text-muted"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted font-weight-bold">تاریخ:</td>
                                    <td id="detailDate" class="text-dark"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3" style="border: none;">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-light shadow-sm px-4" data-dismiss="modal"
                            style="border-radius: 8px; font-weight: 600;">انصراف</button>
                        <button type="button" id="btnConfirmAction" class="btn text-white shadow-sm px-4"
                            style="border-radius: 8px; font-weight: 600; border: none;"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentActionId = null;
        let currentActionType = null;

        function confirmAction(id, actionType, details) {
            currentActionId = id;
            currentActionType = actionType;

            // Set text and styles based on action type
            if (actionType === 'approve') {
                $('#confirmationModalLabel').text('تایید درخواست پرداخت کچایی');
                $('#modalHeader').css('background', 'linear-gradient(135deg, #10b981, #047857)');
                $('#modalDescription').text('آیا مطمئن هستید که می‌خواهید این درخواست پرداخت را تایید و در دفتر کل ثبت کنید؟');
                $('#btnConfirmAction').text('بلی، تایید شود')
                    .removeClass('btn-danger').addClass('btn-success')
                    .css('background', 'linear-gradient(135deg, #10b981, #047857)');
            } else {
                $('#confirmationModalLabel').text('رد درخواست پرداخت کچایی');
                $('#modalHeader').css('background', 'linear-gradient(135deg, #ef4444, #b91c1c)');
                $('#modalDescription').text('آیا مطمئن هستید که می‌خواهید این درخواست پرداخت را رد و حذف کنید؟');
                $('#btnConfirmAction').text('بلی، رد شود')
                    .removeClass('btn-success').addClass('btn-danger')
                    .css('background', 'linear-gradient(135deg, #ef4444, #b91c1c)');
            }

            // Populate details table
            $('#detailTeamName').text(details.teamName);

            const typeBadge = $('#detailType');
            typeBadge.text(details.type);
            if (details.type === 'رسید') {
                typeBadge.removeClass('badge-danger').addClass('badge-success').css({ background: 'rgba(16, 185, 129, 0.1)', color: '#10b981' });
            } else {
                typeBadge.removeClass('badge-success').addClass('badge-danger').css({ background: 'rgba(239, 68, 68, 0.1)', color: '#ef4444' });
            }

            $('#detailOriginal').html(details.currencySymbol + ' ' + details.originalAmount + ' <span class="badge badge-info ml-1 badge-premium" style="font-size: 0.7rem;">' + details.currencyCode + '</span>');
            $('#detailRate').text(details.exchangeRate);
            $('#detailUSD').text('$' + details.usdEquivalent);
            $('#detailKachaeeNumber').text(details.kachaeeNumber);
            $('#detailDesc').text(details.description || 'بدون تفصیلات');
            $('#detailDate').text(details.date);

            // Show modal
            $('#confirmationModal').modal('show');
        }

        $(document).ready(function () {
            $('#btnConfirmAction').on('click', function () {
                if (!currentActionId || !currentActionType) return;

                // Hide confirmation modal
                $('#confirmationModal').modal('hide');

                const url = currentActionType === 'approve'
                    ? '/dashboard/kachaee-approve-request-money/' + currentActionId
                    : '/dashboard/kachaee-delete-request-money/' + currentActionId;

                $.ajax({
                    type: 'DELETE',
                    data: {
                        '_token': '{{csrf_token()}}',
                    },
                    url: url,
                    success: function (res) {
                        if (currentActionType === 'approve') {
                            if (res.status == 'success') {
                                $('.ur' + currentActionId).fadeOut(500, function () { $(this).remove(); });
                                $('.approve').fadeIn(300).delay(2000).fadeOut(300);
                                setTimeout(function () {
                                    location.reload();
                                }, 1000);
                            } else {
                                $('.errorAlert').fadeIn(300).delay(3000).fadeOut(300);
                            }
                        } else {
                            $('.ur' + currentActionId).fadeOut(500, function () { $(this).remove(); });
                            $('.deleteAlert').fadeIn(300).delay(2000).fadeOut(300);
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function () {
                        alert('خطا در اجرای عملیات. لطفا مجددا تلاش کنید.');
                    }
                });
            });
        });
    </script>
@endsection