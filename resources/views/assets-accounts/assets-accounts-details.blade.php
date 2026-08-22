@extends('dsh.master')

@section('content')
    <!-- Google Fonts & Custom CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0A192F;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        .assets-body {
            font-family: 'Outfit', 'Inter', 'Segoe UI', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .premium-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .premium-card:hover {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        .premium-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 20px 24px;
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .premium-header h4, .premium-header h5 {
            margin: 0;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #f8fafc;
        }
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 8px;
            display: inline-block;
        }
        .premium-input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            height: auto;
            font-family: inherit;
            color: #1e293b;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }
        .premium-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            background-color: #ffffff;
        }
        .usd-preview-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.15);
            border-left: 5px solid #10b981;
        }
        .currency-badge {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .table-premium {
            border-collapse: separate;
            border-spacing: 0 8px;
            width: 100%;
        }
        .table-premium th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            border: none;
        }
        .table-premium tbody tr {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.01);
            transition: all 0.2s ease;
        }
        .table-premium tbody tr:hover {
            transform: scale(1.005);
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            background-color: #fafbfd;
        }
        .table-premium td {
            padding: 16px;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            vertical-align: middle;
        }
        .table-premium td:first-child {
            border-left: 1px solid #f1f5f9;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .table-premium td:last-child {
            border-right: 1px solid #f1f5f9;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        .btn-premium {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }
        .btn-premium:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
            transform: translateY(-1px);
            color: #ffffff;
        }
        .btn-action-edit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background-color: rgba(14, 165, 233, 0.1);
            color: #0ea5e9;
            border-radius: 10px;
            border: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .btn-action-edit:hover {
            background-color: #0ea5e9;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }
        .btn-action-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border-radius: 10px;
            border: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .btn-action-delete:hover {
            background-color: #ef4444;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .asset-avatar {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .text-cyan {
            color: #38bdf8 !important;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-blue), #1e3c72);
            color: white;
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(10, 25, 47, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .kpi-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--glass-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        .kpi-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }
        .kpi-details h6 { color: #64748b; font-size: 0.95rem; font-weight: 700; margin-bottom: 5px; }
        .kpi-details h3 { color: var(--primary-blue); font-size: 1.6rem; font-weight: 800; margin: 0; }
        .modal-content { border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .modal-header { background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-bottom: 1px solid #e2e8f0; }
    </style>

    @php
        $today = \Carbon\Carbon::today()->format('Y');
        $total_current_value = 0;
        $total_base_cost = 0;

        foreach($asset_account_details as $co) {
            $purchaseYear = \Carbon\Carbon::parse($co->acquisition_date)->format('Y');
            $yearsPassed = max(0, $today - $purchaseYear);
            
            $usefulLife = $co->estimated_useful_life ?: 1;
            $salvageVal = $co->estimated_salvage_value ?: 0;
            $annualDep = ($co->acquisition_cost - $salvageVal) / $usefulLife;
            $currentValue = max($salvageVal, $co->acquisition_cost - ($yearsPassed * $annualDep));
            
            $total_current_value += $currentValue;
            $total_base_cost += $co->acquisition_cost;
        }
    @endphp

    <div class="assets-body container-fluid py-4">

        <!-- Premium Header -->
        <div class="dashboard-header hideOnPrint">
            <div>
                <h3 class="mb-1 text-white" style="font-weight: 800;"><i class="fa fa-cubes mr-2"></i> دارایی‌های ثابت: {{ $account->aa_name }}</h3>
                <p class="mb-0 text-white-50">لیست تمامی اجناس ثبت شده در این اکونت</p>
            </div>
            <div>
                @if(!$detailEdit)
                @can('manage_assets_account')
                <button class="btn btn-light" style="border-radius: 10px; font-weight: 700; color: var(--primary-blue);" data-toggle="modal" data-target="#assetDetailModal">
                    <i class="fa fa-plus-circle mr-1"></i> ثبت جنس جدید
                </button>
                @endcan
                @else
                <a href="/dashboard/assets-accounts/{{$account->aa_id}}" class="btn btn-light" style="border-radius: 10px; font-weight: 700; color: var(--primary-blue);">
                    <i class="fa fa-arrow-right mr-1"></i> بازگشت به ثبت جدید
                </a>
                @endif
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-4 hideOnPrint">
            <div class="col-md-4">
                <div class="kpi-card" style="border-right: 4px solid #00acc1;">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #00acc1, #00838f);"><i class="fa fa-list"></i></div>
                    <div class="kpi-details">
                        <h6>تعداد کل اقلام</h6>
                        <h3>{{ $asset_account_details->count() }} <small style="font-size: 1rem;">آیتم</small></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card" style="border-right: 4px solid #F59E0B;">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);"><i class="fa fa-dollar"></i></div>
                    <div class="kpi-details">
                        <h6>ارزش خرید (Base Cost USD)</h6>
                        <h3>$ {{ number_format($total_base_cost, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card" style="border-right: 4px solid #10B981;">
                    <div class="kpi-icon" style="background: linear-gradient(135deg, #10B981, #059669);"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-details">
                        <h6>ارزش فعلی دفتری (Current Value)</h6>
                        <h3>$ {{ number_format($total_current_value, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- list table card -->
        <div class="row" id="expensePrint">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="premium-card card">
                    <div class="premium-header card-header">
                        <h4><i class="fa fa-list mr-2"></i> لیست و جزییات حساب: {{$account->aa_name}}</h4>
                        <div class="btn-group hideOnPrint">
                            <button class="btn btn-sm btn-light font-weight-bold text-dark" onclick="printPage('expensePrint')">
                                <i class="fa fa-print mr-1"></i> چاپ گزارش
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if($errors->any() && !$detailEdit)
                            <div class="alert alert-danger error p-3 mb-3 border-0 rounded-lg" role="alert">
                                <h6 class="font-weight-bold mb-2 text-danger"><i class="fa fa-times-circle mr-2"></i> ثبت انجام نشد:</h6>
                                <ul class="mb-0 pl-3 text-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(session("status"))
                            <div class="alert alert-success status p-3 mb-3 border-0 rounded-lg" role="alert">
                                <p class="text-center mb-0 font-weight-bold"><i class="fa fa-check-circle mr-2"></i> {{session('status')}}</p>
                            </div>
                        @endif
                        @if(session("error"))
                            <div class="alert alert-danger error p-3 mb-3 border-0 rounded-lg" role="alert">
                                <p class="text-center mb-0 font-weight-bold"><i class="fa fa-times-circle mr-2"></i> {{session('error')}}</p>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table-premium table" id="expense_list">
                                <thead>
                                <tr>
                                    <th>شماره</th>
                                    <th>عکس</th>
                                    <th>اسم جنس</th>
                                    <th>کلاس جنس</th>
                                    <th>موقعیت فزیکی</th>
                                    <th>سریال نمبر / نمبر جنس</th>
                                    <th>تاریخ خرید</th>
                                    <th>حسابات (Accounts)</th>
                                    <th>ارزش خرید (Original)</th>
                                    <th>نرخ تسعیر (FX)</th>
                                    <th class="text-primary">ارزش معادل (USD Base)</th>
                                    <th>ارزش اسقاط (USD)</th>
                                    <th class="text-success">ارزش دفتر فعلی (USD Book Value)</th>
                                    <th class="hideOnPrint">عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($asset_account_details as $co)
                                        <?php
                                        $purchaseYear = \Carbon\Carbon::parse($co->acquisition_date)->format('Y');
                                        $yearsPassed = max(0, $today - $purchaseYear);
                                        
                                        // Calculations
                                        $usefulLife = $co->estimated_useful_life ?: 1;
                                        $salvageVal = $co->estimated_salvage_value ?: 0;
                                        $annualDep = ($co->acquisition_cost - $salvageVal) / $usefulLife;
                                        $currentValue = max($salvageVal, $co->acquisition_cost - ($yearsPassed * $annualDep));
                                        ?>
                                        <tr class="ur{{$co->aad_id}}">
                                            <td><span class="font-weight-bold text-muted">{{$co->aad_id}}</span></td>
                                            <td>
                                                @if($co->asset_image)
                                                    <a href="{{ asset('uploads/assets/' . $co->asset_image) }}" target="_blank">
                                                        <img src="{{ asset('uploads/assets/' . $co->asset_image) }}" class="asset-avatar" alt="Asset">
                                                    </a>
                                                @else
                                                    <span class="text-muted small"><i class="fa fa-picture-o fa-2x text-light"></i></span>
                                                @endif
                                            </td>
                                            <td><span class="font-weight-bold text-slate-800">{{$co->asset_name}}</span></td>
                                            <td><span class="badge badge-light text-secondary font-weight-bold" style="border: 1px solid #cbd5e1;">{{$co->asset_class}}</span></td>
                                            <td><span class="text-muted"><i class="fa fa-map-marker mr-1"></i> {{$co->physical_location}}</span></td>
                                            <td>
                                                <div class="small"><b>SN:</b> {{$co->asset_serial_number}}</div>
                                                <div class="small text-muted"><b>Num:</b> {{$co->asset_number}}</div>
                                            </td>
                                            <td><span class="text-muted font-weight-bold">{{$co->acquisition_date}}</span></td>
                                            <td>
                                                <?php
                                                    $deb = $co->debitAccount ?? ($mapping->debitAccount ?? null);
                                                    $cred = $co->creditAccount ?? ($mapping->creditAccount ?? null);

                                                    $debCode = $deb ? $deb->account_code : '13500';
                                                    $credCode = $cred ? $cred->account_code : '10100';

                                                    $debName = $deb ? ($deb->account_code . ' - ' . $deb->account_name) : 'Fixed Assets (13500)';
                                                    $credName = $cred ? ($cred->account_code . ' - ' . $cred->account_name) : 'Cash-AFN (10100)';
                                                ?>
                                                <div style="font-size: 0.78rem; line-height: 1.3;">
                                                    <span class="badge badge-light border text-primary font-weight-bold d-block mb-1" style="padding: 3px 6px;" title="حساب بدهکار (Debit): {{ $debName }}" data-toggle="tooltip">
                                                        Dr: {{ $debCode }}
                                                    </span>
                                                    <span class="badge badge-light border text-success font-weight-bold d-block" style="padding: 3px 6px;" title="حساب بستانکار (Credit): {{ $credName }}" data-toggle="tooltip">
                                                        Cr: {{ $credCode }}
                                                    </span>
                                                </div>
                                            </td>
                                            
                                            <!-- Original Cost with Currency Info -->
                                            <td>
                                                <span class="font-weight-bold text-dark">
                                                    {{ number_format($co->original_amount ?? $co->acquisition_cost, 2) }} 
                                                    <small class="text-muted">{{ $co->currency_code ?? 'USD' }}</small>
                                                </span>
                                            </td>

                                            <!-- Exchange Rate (FX) -->
                                            <td>
                                                @if($co->currency_code && $co->currency_code !== 'USD')
                                                    <span class="text-muted small font-weight-bold">{{ number_format($co->exchange_rate, 6) }}</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>

                                            <!-- USD Normalized Cost -->
                                            <td class="text-primary font-weight-bold">
                                                $ {{ number_format($co->acquisition_cost, 2) }}
                                            </td>

                                            <!-- Salvage value -->
                                            <td>
                                                $ {{ number_format($co->estimated_salvage_value, 2) }}
                                            </td>

                                            <!-- Current Book Value -->
                                            <td class="text-success font-weight-bold">
                                                $ {{ number_format($currentValue, 2) }}
                                                <div class="small text-muted" style="font-size: 0.7rem;">استهلاک شده: {{ $yearsPassed }} سال</div>
                                            </td>

                                            <td class="hideOnPrint text-center" style="white-space: nowrap; width: 1%;">
                                                 <div class="d-flex align-items-center justify-content-center" style="gap: 8px;">
                                                     @can('edit_assets_account_details')
                                                     <a href="/dashboard/assets-accounts-details/{{$co->aad_id}}/edit" class="btn-action-edit" title="ویرایش">
                                                         <i class="fa fa-edit fa-lg"></i>
                                                     </a>
                                                     @endcan
                                                     @can('delete_assets_account_details')
                                                     <button onclick="deleteAssetAccountDetails({{$co->aad_id}})" class="btn-action-delete" title="حذف">
                                                         <i class="fa fa-trash fa-lg"></i>
                                                     </button>
                                                     @endcan
                                                     @if(!auth()->user()->can('edit_assets_account_details') && !auth()->user()->can('delete_assets_account_details'))
                                                         <span class="text-muted">-</span>
                                                     @endif
                                                 </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Summary Row -->
                                    <tr style="background: rgba(241, 245, 249, 0.6); font-weight: 700;">
                                        <td colspan="3"><span class="text-indigo">جمله کل دارایی‌های ثابت</span></td>
                                        <td colspan="5"></td>
                                        <td><span class="text-dark">تعداد جنس: {{$asset_account_details->count()}}</span></td>
                                        <td></td>
                                        <td class="text-primary font-weight-bold">$ {{ number_format($total_base_cost, 2) }}</td>
                                        <td></td>
                                        <td class="text-success font-weight-bold">$ {{ number_format($total_current_value, 2) }}</td>
                                        <td class="hideOnPrint"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Add/Edit -->
    <div class="modal fade" id="assetDetailModal" tabindex="-1" role="dialog" aria-labelledby="assetDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-dark" id="assetDetailModalLabel">
                        @if(!$detailEdit) <i class="fa fa-plus-circle text-primary mr-2"></i> ثبت نهایی جنس ثابت @else <i class="fa fa-edit text-primary mr-2"></i> ویرایش جزییات جنس ثابت @endif
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-4">
                    @if($errors->any() && $detailEdit)
                        <div class="alert alert-danger error p-3 mb-3 border-0 rounded-lg" role="alert">
                            <h6 class="font-weight-bold mb-2 text-danger"><i class="fa fa-times-circle mr-2"></i> لطفا خطاهای زیر را برطرف کنید:</h6>
                            <ul class="mb-0 pl-3 text-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!$detailEdit)
                        <form action="/dashboard/assets-accounts-details" method="post" enctype="multipart/form-data" id="asset_form">
                            @csrf
                            <input type="hidden" name="assets_account_id" value="{{$account->aa_id}}">
                    @else
                        <form action="/dashboard/assets-accounts-details/{{$detailEdit->aad_id}}" method="post" enctype="multipart/form-data" id="asset_form">
                            {{method_field('patch')}}
                            @csrf
                            <input type="hidden" name="assets_account_id" value="{{$detailEdit->ajnas_account_id}}">
                    @endif

                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">اسم جنس</label>
                                        <input type="text" name="asset_name" value="{{ $detailEdit ? $detailEdit->asset_name : old('asset_name') }}" placeholder="مثال: ژنراتور دیزلی" class="form-control premium-input" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">کلاس جنس</label>
                                        <input type="text" name="asset_class" value="{{ $detailEdit ? $detailEdit->asset_class : old('asset_class') }}" placeholder="مثال: ماشین آلات" class="form-control premium-input" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">تفصیلات جنس</label>
                                        <input type="text" name="asset_description" value="{{ $detailEdit ? $detailEdit->asset_description : old('asset_description') }}" placeholder="مثال: ژنراتور 250 کیلووات" class="form-control premium-input" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">موقعیت فزیکی جنس</label>
                                        <input type="text" name="physical_location" value="{{ $detailEdit ? $detailEdit->physical_location : old('physical_location') }}" placeholder="مثال: دفتر مرکزی کابل - منزل اول" class="form-control premium-input" required>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">نمبر جنس</label>
                                        <input type="text" name="asset_number" value="{{ $detailEdit ? $detailEdit->asset_number : old('asset_number') }}" placeholder="مثال: AST-009" class="form-control premium-input" required>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">سریال نمبر جنس</label>
                                        <input type="text" name="asset_serial_number" value="{{ $detailEdit ? $detailEdit->asset_serial_number : old('asset_serial_number') }}" placeholder="مثال: SN-998822" class="form-control premium-input" required>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">تاریخ خرید</label>
                                        <input type="date" name="acquisition_date" value="{{ $detailEdit ? $detailEdit->acquisition_date : old('acquisition_date', date('Y-m-d')) }}" class="form-control premium-input" required>
                                    </div>
                                </div>

                                <!-- Currency & Cost Fields -->
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label text-indigo font-weight-bold"><i class="fa fa-money"></i> انتخاب اسعار خرید</label>
                                        <select name="currency_id" id="currency_id" class="form-control premium-input select2">
                                            @foreach($currencies as $curr)
                                                <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}" data-symbol="{{ $curr->symbol }}" data-code="{{ $curr->code }}" {{ ($detailEdit && $detailEdit->currency_id == $curr->id) || (!$detailEdit && $curr->code == 'USD') ? 'selected' : '' }}>
                                                    {{ $curr->code }} ({{ $curr->symbol }}) - {{ $curr->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label text-success font-weight-bold"><i class="fa fa-dollar"></i> قیمت خرید (به اسعار انتخاب شده)</label>
                                        <input type="number" step="0.0001" name="acquisition_cost" id="purchase_cost" value="{{ $detailEdit ? ($detailEdit->original_amount ?? $detailEdit->acquisition_cost) : old('acquisition_cost') }}" class="form-control premium-input text-success font-weight-bold" placeholder="0.00" required>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">تعداد سال قابل استفاده</label>
                                        <input type="number" name="estimated_useful_life" id="estimated_useful_life" value="{{ $detailEdit ? $detailEdit->estimated_useful_life : old('estimated_useful_life') }}" class="form-control premium-input" placeholder="مثال: 5" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">ارزش اسقاط (Salvage Value)</label>
                                        <input type="number" step="0.0001" name="estimated_salvage_value" id="estimated_salvage_value" value="{{ $detailEdit ? ($detailEdit->currency_code && $detailEdit->currency_code !== 'USD' && $detailEdit->exchange_rate ? round($detailEdit->estimated_salvage_value / $detailEdit->exchange_rate, 2) : $detailEdit->estimated_salvage_value) : old('estimated_salvage_value') }}" class="form-control premium-input" placeholder="ارزش بعد از مستهلک شدن" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label text-danger">استهلاک سالانه (Annual Dep.)</label>
                                        <input type="text" id="annual_depreciation" readonly class="form-control premium-input bg-light text-danger font-weight-bold" placeholder="محاسبه خودکار">
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">عکس جنس (اختیاری)</label>
                                        <input type="file" name="asset_image" class="form-control premium-input bg-white" accept="image/*">
                                        @if($detailEdit && $detailEdit->asset_image)
                                            <a href="{{ asset('uploads/assets/' . $detailEdit->asset_image) }}" target="_blank" class="small mt-2 d-block text-info"><i class="fa fa-image"></i> مشاهده عکس فعلی</a>
                                        @endif
                                    </div>
                                </div>

                                <!-- USD Normalization Live Preview Card -->
                                <div class="col-lg-12 my-3">
                                    <div class="usd-preview-card">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <span class="text-uppercase text-muted small tracking-wider font-weight-bold d-block mb-1">ارزش نهایی معادل دالر (USD Normalized Cost)</span>
                                                <h2 class="mb-0 text-cyan font-weight-bold" id="usd_normalized_preview">$ 0.00</h2>
                                                <small class="text-muted" id="usd_salvage_preview">ارزش اسقاط معادل دالر: $ 0.00</small>
                                            </div>
                                            <div class="text-right">
                                                <span class="currency-badge d-inline-block" id="exchange_rate_badge">1 USD = 1.00 USD</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ACCOUNT OVERRIDES -->
                                <div class="col-lg-12 mt-3">
                                    <div class="p-4" style="background: rgba(255, 255, 255, 0.8); border: 1px solid #e2e8f0; border-radius: 12px;">
                                        <h6 class="mb-3 font-weight-bold text-slate-700"><i class="fa fa-university"></i> تنظیمات حسابی دارایی ثابت (General Ledger Mapping)</h6>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label class="form-label text-primary">حساب دارایی ثابت (Debit)</label>
                                                    <select name="override_debit_account_id" id="override_debit_account_id" class="form-control premium-input select2 w-100">
                                                        @foreach($allowedDebitAccounts as $acc)
                                                            <option value="{{ $acc->id }}" {{ ($detailEdit && $detailEdit->override_debit_account_id == $acc->id) || (!$detailEdit && $mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label class="form-label text-primary">حساب پرداخت (Credit)</label>
                                                    <select name="override_credit_account_id" id="override_credit_account_id" class="form-control premium-input select2 w-100">
                                                        @foreach($allowedCreditAccounts as $acc)
                                                            <option value="{{ $acc->id }}" {{ ($detailEdit && $detailEdit->override_credit_account_id == $acc->id) || (!$detailEdit && $mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-12 mt-4 text-left">
                                    <button class="btn btn-premium px-5 py-2 font-weight-bold" type="submit">
                                        <span class="fa fa-save mr-2"></span> {{ $detailEdit ? 'بروزرسانی تغییرات' : 'ثبت و ذخیره جنس' }}
                                    </button>
                                </div>

                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Apply select2
            $('#currency_id').select2({
                placeholder: "انتخاب اسعار",
                allowClear: false,
                dropdownParent: $('#assetDetailModal')
            });
            $('#override_debit_account_id').select2({
                dropdownParent: $('#assetDetailModal')
            });
            $('#override_credit_account_id').select2({
                dropdownParent: $('#assetDetailModal')
            });

            @if($detailEdit || $errors->any())
                $('#assetDetailModal').modal('show');
            @endif

            // Run initial calculations
            calculateFXAndDepreciation();

            // Triggers for calculation
            $('#purchase_cost, #currency_id, #estimated_useful_life, #estimated_salvage_value').on('change keyup paste', function() {
                calculateFXAndDepreciation();
            });

            // Re-fade flash alerts
            window.setTimeout(function () {
                $(".alert-success, .alert-danger").fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            }, 3000);
        });

        function calculateFXAndDepreciation() {
            // 1. Currency & FX Math
            var selectedOpt = $('#currency_id').find(':selected');
            var rate = parseFloat(selectedOpt.data('rate')) || 1.0;
            var symbol = selectedOpt.data('symbol') || '$';
            var code = selectedOpt.data('code') || 'USD';

            var origCost = parseFloat($('#purchase_cost').val()) || 0;
            var origSalvage = parseFloat($('#estimated_salvage_value').val()) || 0;
            var usefulLife = parseFloat($('#estimated_useful_life').val()) || 1;

            // USD base values
            var costUsd = origCost * rate;
            var salvageUsd = origSalvage * rate;

            // Display USD Live normalized previews
            $('#usd_normalized_preview').text('$ ' + costUsd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 }));
            $('#usd_salvage_preview').text('ارزش اسقاط معادل دالر: $ ' + salvageUsd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 }));
            
            if (code === 'USD') {
                $('#exchange_rate_badge').text('1 USD = 1.00 USD');
            } else {
                $('#exchange_rate_badge').text('1 ' + code + ' = ' + rate.toFixed(6) + ' USD');
            }

            // 2. Straight-line Depreciation Math in Original Currency
            if (usefulLife > 0) {
                var annualDepOrig = (origCost - origSalvage) / usefulLife;
                $('#annual_depreciation').val(annualDepOrig.toFixed(2) + ' ' + symbol);
            } else {
                $('#annual_depreciation').val('0.00 ' + symbol);
            }
        }

        function deleteAssetAccountDetails(id) {
            swal({
                title: "آیا مطمئن هستید؟",
                text: "این جنس ثابت و تمام اسناد حسابی (GL) متصل به آن برای همیشه معکوس و حذف خواهند شد!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                buttons: {
                    confirm: {text: 'بله، حذف شود', className: 'btn-danger'},
                    cancel: 'خیر'
                },
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: 'DELETE',
                        data: {
                            '_token': '{{csrf_token()}}',
                        },
                        url: '/dashboard/assets-accounts-details/' + id,
                        success: function (res) {
                            if (res.status == 'success') {
                                $('.ur' + id).fadeOut(400, function() {
                                    $(this).remove();
                                    location.reload();
                                });
                            } else {
                                swal("خطا", "عملیات حذف موفقیت آمیز نبود.", "error");
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
