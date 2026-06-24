@extends('dsh.master')
@section('title', 'Purchased Carpet Details - ' . $carpet->carpet_no)

@section('content')
    <style>
        :root {
            --QBIC-primary: #1e3a8a;
            --QBIC-secondary: #3b82f6;
            --QBIC-glass: rgba(255, 255, 255, 0.7);
            --QBIC-border: #e2e8f0;
            --radius-lg: 16px;
            --shadow-soft: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .glass-card {
            background: white;
            border: 1px solid var(--QBIC-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            transition: all 0.3s;
        }

        .nav-pills .nav-link {
            border-radius: 12px;
            color: #64748b;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.2s;
        }

        .nav-pills .nav-link.active {
            background: var(--QBIC-primary);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.2);
        }

        .forensic-snapshot {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 15px;
            border: 1px dashed #cbd5e1;
        }

        .form-label-premium {
            font-weight: 700;
            color: #334155;
            font-size: 12px;
            margin-bottom: 5px;
            display: block;
        }

        .premium-input {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px;
            transition: all 0.2s;
        }

        .table-modern thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            border: none;
        }
    </style>

    <div class="row">
        <!-- LEFT COLUMN: CARPET SUMMARY & FORENSIC SNAPSHOT -->
        <div class="col-md-4">
            <div class="glass-card p-4 mb-4 text-center">
                @if($carpet->carpet_image)
                    <img src="/{{$carpet->carpet_image}}" class="img-fluid rounded-lg shadow-sm mb-3"
                        style="max-height: 300px; object-fit: cover;">
                @else
                    <div class="bg-light rounded-lg d-flex align-items-center justify-content-center mb-3"
                        style="height: 200px;">
                        <i class="feather icon-image text-muted" style="font-size: 40px;"></i>
                    </div>
                @endif
                <h4 class="font-weight-bold mb-1">{{ $carpet->carpet_no }}</h4>
                <div class="badge badge-pill {{ $carpet->status == 1 ? 'badge-success' : 'badge-primary' }} px-3 py-2">
                    {{ $carpet->status == 1 ? 'خریداری شده (Purchased)' : 'در وضعیت دیگر' }}
                </div>
            </div>

            <!-- FORENSIC FX SNAPSHOT -->
            <div class="glass-card p-4 mb-4">
                <h6 class="font-weight-bold text-primary mb-3"><i class="feather icon-shield mr-2"></i> Forensic FX Snapshot
                </h6>
                <div class="forensic-snapshot">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="small text-muted">Original Currency:</span>
                        <span class="font-weight-bold text-dark">{{ $carpet->currency_code ?? 'USD' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Unit Price (Native):</span>
                        <span class="font-weight-bold">{{ number_format($carpet->original_price ?? $carpet->price, 2) }}
                            {{ $carpet->currency_code ?? '$' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-primary font-weight-bold">USD Normalization:</span>
                        <span class="text-primary font-weight-bold">${{ number_format($carpet->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-success">AFN Value:</span>
                        <span class="text-success font-weight-bold">{{ number_format($carpet->total_price_af, 0) }}
                            AFN</span>
                    </div>
                    <div class="mt-3 pt-3 border-top small text-muted text-center">
                        <i class="feather icon-clock mr-1"></i> Snapshot Rate: 1 USD =
                        {{ number_format($carpet->exchange_rate, 4) }} {{ $carpet->currency_code ?? 'USD' }}
                    </div>
                </div>
            </div>

            <!-- QUICK STATS -->
            <div class="glass-card p-4">
                <h6 class="font-weight-bold text-dark mb-3"><i class="feather icon-info mr-2"></i> مشخصات عمومی</h6>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted small">فروشنده (Vendor):</td>
                        <td class="text-right font-weight-bold">{{ $carpet->agent->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small">نوعیت:</td>
                        <td class="text-right font-weight-bold">{{ $carpet->type->carpet_type ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small">کوالتی:</td>
                        <td class="text-right font-weight-bold">{{ $carpet->quality->quality ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small">مساحت:</td>
                        <td class="text-right font-weight-bold">{{ $carpet->area }} m²</td>
                    </tr>
                    <tr>
                        <td class="text-muted small">ابعاد:</td>
                        <td class="text-right font-weight-bold" dir="ltr">{{ $carpet->height }} x {{ $carpet->width }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small">نقشه:</td>
                        <td class="text-right font-weight-bold">{{ $carpet->map_number }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- RIGHT COLUMN: TABS & CONTENT -->
        <div class="col-md-8">
            @if(session("status"))
                <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('status') }}</div>
            @endif
            @if(session("error"))
                <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
            @endif

            <div class="glass-card p-4">
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item mr-2">
                        <a class="nav-link active" id="pills-specs-tab" data-toggle="pill" href="#specs">
                            <i class="feather icon-list mr-2"></i> Specifications
                        </a>
                    </li>
                    <li class="nav-item mr-2">
                        <a class="nav-link" id="pills-checkbook-tab" data-toggle="pill" href="#checkbook">
                            <i class="feather icon-book mr-2"></i> Check Book
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- TAB 1: SPECIFICATIONS -->
                    <div class="tab-pane fade show active" id="specs" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h5 class="font-weight-bold mb-0">جزییات خرید و مشخصات فیزیکی</h5>
                            <button class="btn btn-light-secondary btn-sm rounded-pill" onclick="window.print()">
                                <i class="feather icon-printer mr-1"></i> چاپ جزئیات
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label-premium">زمینه (Field)</label>
                                <div class="bg-light p-3 rounded-lg font-weight-bold text-dark">{{ $carpet->field }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label-premium">حاشیه (Margin)</label>
                                <div class="bg-light p-3 rounded-lg font-weight-bold text-dark">{{ $carpet->margin }}</div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label-premium">شماره فرمایش (Order No)</label>
                                <div class="bg-light p-3 rounded-lg font-weight-bold text-dark">
                                    {{ $carpet->carpet_order->order_number ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label-premium">تاریخ خرید</label>
                                <div class="bg-light p-3 rounded-lg font-weight-bold text-dark">{{ $carpet->date }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label-premium">گودام فعلی</label>
                                <div class="bg-light p-3 rounded-lg font-weight-bold text-dark">
                                    {{ $carpet->warehouse->name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 border rounded-lg bg-light-primary border-primary border-dashed">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="font-weight-bold text-primary mb-1">ارزش کل محصولات (Inventory Valuation)
                                    </h6>
                                    <p class="text-muted small mb-0">مجموع ارزش ثبت شده در دفتر کل (General Ledger) بر اساس
                                        نرخ برابری Forensic FX</p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <h3 class="font-weight-bold text-primary mb-0">
                                        ${{ number_format($carpet->total_price, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: CHECK BOOK -->
                    <div class="tab-pane fade" id="checkbook" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h5 class="font-weight-bold mb-0">مدیریت چک بٌک (Acquisition Verification)</h5>
                            @if($carpetCheckBook)
                                <button class="btn btn-info btn-sm rounded-pill" id="editCheckBtn">
                                    <i class="feather icon-edit-2 mr-1"></i> ویرایش چک بٌک
                                </button>
                            @endif
                        </div>

                        @if(!$carpetCheckBook)
                            <div class="bg-light p-4 rounded-lg border border-dashed">
                                <h6 class="font-weight-bold mb-4 text-secondary">ثبت چک بٌک برای تایید نهایی ابعاد</h6>
                                <form action="/dashboard/check-book" method="post">
                                    @csrf
                                    <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                                    <input type="hidden" name="agent_id" value="{{$carpet->agent_id}}">
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label class="form-label-premium">ارز انتخاب شده (Currency)</label>
                                            <select name="currency_id" id="check_currency_id"
                                                class="form-control border-primary">
                                                @foreach($currencies as $curr)
                                                    <option value="{{ $curr->id }}" data-rate="{{ $curr->exchange_rate }}"
                                                        data-code="{{ $curr->code }}">{{ $curr->code }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="exchange_rate" id="check_exchange_rate" value="1">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="form-label-premium">مبلغ کچایی (Deduction)</label>
                                            <input type="number" step="0.01" name="kachaee_amount_input"
                                                id="check_kachaee_input" class="form-control premium-input border-primary"
                                                value="0">
                                            <input type="hidden" name="kachaee_amount" id="final_kachaee_native" value="0">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="form-label-premium">معادل دالر (USD)</label>
                                            <input type="text" name="kachaee_dollar_amount" id="check_kachaee_usd"
                                                class="form-control premium-input bg-light" readonly value="0">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="form-label-premium">نمبر چک بٌک</label>
                                            <input type="text" name="check_number" value="{{$CheckNo}}"
                                                class="form-control premium-input">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label class="form-label-premium">حساب بدهکار (Debit: Deduction/Repair)</label>
                                            <select name="debit_account_id" class="form-control select2">
                                                @foreach($expenseAccounts as $acc)
                                                    <option value="{{$acc->id}}" {{ $acc->account_code == '5100' ? 'selected' : '' }}>
                                                        {{$acc->account_name}} ({{$acc->account_code}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="form-label-premium">حساب بستانکار (Credit: Inventory)</label>
                                            <select name="credit_account_id" class="form-control select2">
                                                @foreach($inventoryAccounts as $acc)
                                                    <option value="{{$acc->id}}" {{ ($carpet->override_inventory_account_id == $acc->id) ? 'selected' : '' }}>
                                                        {{$acc->account_name}} ({{$acc->account_code}})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label class="form-label-premium">طول نهایی</label>
                                            <input type="number" step="0.01" name="height" id="check_height"
                                                value="{{$carpet->height}}" class="form-control premium-input">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="form-label-premium">عرض نهایی</label>
                                            <input type="number" step="0.01" name="width" id="check_width"
                                                value="{{$carpet->width}}" class="form-control premium-input">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="form-label-premium">مساحت نهایی</label>
                                            <input type="number" step="0.01" name="area" id="check_area"
                                                value="{{$carpet->area}}" class="form-control premium-input bg-light" readonly>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label class="form-label-premium">تاریخ تایید</label>
                                            <input type="date" name="date" value="{{ date('Y-m-d') }}"
                                                class="form-control premium-input">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-lg px-4 shadow">ذخیره و تایید
                                        ابعاد</button>
                                </form>
                            </div>
                        @else
                            <div id="checkView">
                                <table class="table table-bordered table-modern">
                                    <tr>
                                        <th class="bg-light w-25">نمبر چک بٌک</th>
                                        <td>{{ $carpetCheckBook->check_number }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">ابعاد نهایی</th>
                                        <td dir="ltr" class="font-weight-bold text-dark">{{ $carpetCheckBook->height }} x
                                            {{ $carpetCheckBook->width }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">مساحت نهایی</th>
                                        <td class="font-weight-bold text-primary">{{ $carpetCheckBook->area }} m²</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">تاریخ تایید</th>
                                        <td>{{ $carpetCheckBook->date }}</td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Area calculation for Check Book
            $('#check_height, #check_width').on('input', function () {
                let h = parseFloat($('#check_height').val()) || 0;
                let w = parseFloat($('#check_width').val()) || 0;
                $('#check_area').val((h * w).toFixed(2));
            });

            // FX calculation for Check Book Deduction
            $('#check_currency_id, #check_kachaee_input').on('change input', function () {
                let rate = parseFloat($('#check_currency_id option:selected').data('rate')) || 1;
                let nativeVal = parseFloat($('#check_kachaee_input').val()) || 0;
                $('#check_exchange_rate').val(rate);
                $('#final_kachaee_native').val(nativeVal);
                $('#check_kachaee_usd').val((nativeVal * rate).toFixed(2));
            });
        });
    </script>
@endsection