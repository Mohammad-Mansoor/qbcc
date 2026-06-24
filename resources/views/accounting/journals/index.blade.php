@extends('dsh.master')

@section('content')
<style>
  /* Premium Glassmorphism & Custom Elements */
  .modern-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.06) !important;
    backdrop-filter: blur(12px);
    overflow: hidden;
    margin-bottom: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .modern-card:hover {
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1) !important;
  }
  .modern-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
    padding: 20px 24px !important;
    border-bottom: none !important;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .modern-title {
    color: #ffffff !important;
    font-weight: 700 !important;
    margin: 0 !important;
    font-size: 1.25rem !important;
    letter-spacing: 0.5px;
  }
  .modern-table {
    width: 100%;
    margin-top: 15px;
    border-collapse: separate !important;
    border-spacing: 0 8px !important;
  }
  .modern-table th {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 0.85rem;
    padding: 16px 12px !important;
    border: none !important;
    text-align: right;
  }
  .modern-table tbody tr {
    background: #ffffff;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.01);
    cursor: pointer;
  }
  .modern-table tbody tr:hover {
    background: #f8fafc !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
  }
  .modern-table tbody td {
    padding: 14px 12px !important;
    vertical-align: middle !important;
    border-top: 1px solid #f1f5f9 !important;
    border-bottom: 1px solid #f1f5f9 !important;
    font-size: 0.9rem;
    color: #334155;
  }
  .modern-table tbody td:first-child {
    border-left: 1px solid #f1f5f9 !important;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
  }
  .modern-table tbody td:last-child {
    border-right: 1px solid #f1f5f9 !important;
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
  }
  
  /* Modern Actions Buttons */
  .btn-modern-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    font-size: 0.85rem !important;
    border-radius: 50% !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer;
  }
  .btn-modern-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12) !important;
    color: #ffffff !important;
  }
  
  .btn-details {
    background: linear-gradient(135deg, #00c6ff, #0072ff) !important;
  }
  
  /* Inputs Customization */
  .modern-search {
    border-radius: 10px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 10px 14px !important;
    font-size: 0.9rem !important;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.01) !important;
    transition: all 0.3s ease !important;
    width: 100%;
  }
  .modern-search:focus {
    border-color: #2a5298 !important;
    box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1) !important;
    outline: none;
  }
  
  /* ANIMATED STATS CARDS */
  .stat-card {
    border: none;
    border-radius: 16px;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    cursor: default;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,0.05);
  }
  .stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
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
  .stat-card-orange {
    background: linear-gradient(135deg, #f59e0b, #d97706);
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

@php
  $global_posted = \App\LedgerTransaction::where('status', 'posted')->count();
  $global_reversed = \App\LedgerTransaction::where('status', 'reversed')->count();
  $global_turnover = \App\LedgerEntry::sum('base_debit');
  $lock_date = \DB::table('financial_settings')->where('key', 'financial_lock_date')->value('value') ?? 'تعیین نشده';
@endphp

<div class="container-fluid">
    <br>
    
    <!-- METRICS OVERVIEW ROW -->
    <div class="row mb-4 hideOnPrint">
      <!-- Total Posted Vouchers -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-blue p-4 h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-card-val">{{ $global_posted }}</div>
              <div class="stat-card-lbl">سندهای فعال (Active Vouchers)</div>
            </div>
            <div class="stat-card-icon">
              <i class="feather icon-check-circle"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Reversed Vouchers -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-orange p-4 h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-card-val">{{ $global_reversed }}</div>
              <div class="stat-card-lbl">سندهای ابطال شده (Reversed Vouchers)</div>
            </div>
            <div class="stat-card-icon">
              <i class="feather icon-rotate-ccw"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Base Turnover -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-green p-4 h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-card-val">${{ number_format($global_turnover, 2) }}</div>
              <div class="stat-card-lbl">حجم کل معاملات به دالر (GL Turnover)</div>
            </div>
            <div class="stat-card-icon">
              <i class="feather icon-activity"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Locked Status -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-indigo p-4 h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-card-val" style="font-size: 1.4rem; padding-top: 8px;">{{ $lock_date }}</div>
              <div class="stat-card-lbl">تاریخ قفل مالی (Fiscal Lock Date)</div>
            </div>
            <div class="stat-card-icon">
              <i class="feather icon-lock"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Premium Header & Search Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold mb-1">روزنامچه کل (General Ledger)</h3>
                            <p class="text-muted mb-0">مشاهده و مدیریت تمامی تراکنش‌های مالی ثبت شده در سیستم</p>
                        </div>
                        <div class="col-md-6 text-right">
                            @can('create_journal')
                            <a href="{{ route('accounting.journals.create') }}" class="btn btn-primary shadow-sm px-4" style="border-radius: 10px;">
                                <i class="feather icon-plus mr-2"></i>ثبت سند جدید (Journal Entry)
                            </a>
                            @endcan
                        </div>
                    </div>
                    
                    <hr class="opacity-10 mb-4">

                    <!-- Advanced Filter Bar -->
                    <form action="{{ route('accounting.journals.index') }}" method="GET">
                        <div class="row">
                            <!-- 1. Text Search -->
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-muted">🔍 جستجوی عمومی و زنده (سند/تفصیلات):</label>
                                <input type="text" name="search" id="live-journal-search" value="{{ request('search') }}" class="form-control modern-search" placeholder="نمبر سند...">
                            </div>

                            <!-- 2. Date Range Start -->
                            <div class="col-md-2 mb-3">
                                <label class="small font-weight-bold text-muted">از تاریخ:</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control modern-search">
                            </div>

                            <!-- 3. Date Range End -->
                            <div class="col-md-2 mb-3">
                                <label class="small font-weight-bold text-muted">الی تاریخ:</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control modern-search">
                            </div>

                            <!-- 4. Journal Type Selector -->
                            <div class="col-md-2 mb-3">
                                <label class="small font-weight-bold text-muted">نوعیت:</label>
                                <select name="journal_type" class="form-control modern-search" onchange="this.form.submit();">
                                    <option value="">همه</option>
                                    <option value="sales" {{ request('journal_type') == 'sales' ? 'selected' : '' }}>فروشات</option>
                                    <option value="purchase" {{ request('journal_type') == 'purchase' ? 'selected' : '' }}>خریداری</option>
                                    <option value="journal" {{ request('journal_type') == 'journal' ? 'selected' : '' }}>روزنامچه</option>
                                </select>
                            </div>

                            <!-- 5. Voucher Status Selector -->
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-muted">حالت (Status):</label>
                                <select name="status" class="form-control modern-search" onchange="this.form.submit();">
                                    <option value="">همه</option>
                                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>تایید شده</option>
                                    <option value="reversed" {{ request('status') == 'reversed' ? 'selected' : '' }}>ابطال شده</option>
                                </select>
                            </div>

                            <!-- 6. Account Filter -->
                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold text-muted">📊 فیلتر بر اساس حساب دفتر کل:</label>
                                <select name="account_id" id="account_filter" class="form-control select2" onchange="this.form.submit();">
                                    <option value="">همه حساب ها</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 7. Value Range Start -->
                            <div class="col-md-2 mb-3">
                                <label class="small font-weight-bold text-muted">کمترین مبلغ (USD):</label>
                                <input type="number" step="any" name="min_amount" value="{{ request('min_amount') }}" class="form-control modern-search" placeholder="مثلاً: 100">
                            </div>

                            <!-- 8. Value Range End -->
                            <div class="col-md-2 mb-3">
                                <label class="small font-weight-bold text-muted">بیشترین مبلغ (USD):</label>
                                <input type="number" step="any" name="max_amount" value="{{ request('max_amount') }}" class="form-control modern-search" placeholder="مثلاً: 5000">
                            </div>

                            <!-- 9. Currency Audit Filter -->
                            <div class="col-md-3 mb-3">
                                <label class="small font-weight-bold text-muted">💱 فیلتر چند اسعاری:</label>
                                <div class="form-group pt-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="mixed_currency" name="mixed_currency" value="1" {{ request('mixed_currency') ? 'checked' : '' }} onchange="this.form.submit();">
                                        <label class="custom-control-label font-weight-bold text-muted small" for="mixed_currency">فقط اسناد غیر دالری (USD Override)</label>
                                    </div>
                                </div>
                            </div>

                            <!-- 10. Party Type Filter -->
                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold text-muted">نوعیت طرف معامله (Party Type):</label>
                                <select id="filter_party_type" name="party_type" class="form-control modern-search" onchange="filterPartyTypeChanged(this)">
                                    <option value="">همه</option>
                                    <option value="App\Customer" {{ request('party_type') == 'App\Customer' ? 'selected' : '' }}>مشتری (Customer)</option>
                                    <option value="App\Agents" {{ request('party_type') == 'App\Agents' ? 'selected' : '' }}>نماینده (Agent)</option>
                                    <option value="App\OfficeEmployee" {{ request('party_type') == 'App\OfficeEmployee' ? 'selected' : '' }}>کارمند (Employee)</option>
                                    <option value="App\StringSeller" {{ request('party_type') == 'App\StringSeller' ? 'selected' : '' }}>فروشنده تار (String Seller)</option>
                                    <option value="App\WashingTeam" {{ request('party_type') == 'App\WashingTeam' ? 'selected' : '' }}>تیم شستشو (Washing Team)</option>
                                    <option value="App\FinishingTeam" {{ request('party_type') == 'App\FinishingTeam' ? 'selected' : '' }}>تیم پرداخت (Finishing Team)</option>
                                    <option value="App\Kachaee" {{ request('party_type') == 'App\Kachaee' ? 'selected' : '' }}>تیم کچه ای (Kachaee)</option>
                                    <option value="App\NewDifferentAccount" {{ request('party_type') == 'App\NewDifferentAccount' ? 'selected' : '' }}>حساب متفرقه جدید (New Different Account)</option>
                                    <option value="App\DifferentAccount" {{ request('party_type') == 'App\DifferentAccount' ? 'selected' : '' }}>حساب متفرقه (Different Account)</option>
                                </select>
                            </div>

                            <!-- 11. Party Filter -->
                            <div class="col-md-5 mb-3">
                                <label class="small font-weight-bold text-muted">طرف معامله (Party):</label>
                                <select id="filter_party_id" name="party_id" class="form-control select2-party" onchange="this.form.submit();" {{ !request('party_type') ? 'disabled' : '' }}>
                                    <option value="">همه طرف های معامله</option>
                                    @if(request('party_type') && request('party_id'))
                                        @php
                                            $partyName = '';
                                            $pt = request('party_type');
                                            $pid = request('party_id');
                                            if ($pt == 'App\Customer') {
                                                $partyName = \App\Customer::find($pid)->name ?? '';
                                            } elseif ($pt == 'App\Agents') {
                                                $agent = \App\Agents::find($pid);
                                                $partyName = $agent ? $agent->name . ($agent->email ? " ({$agent->email})" : "") : "";
                                            } elseif ($pt == 'App\OfficeEmployee') {
                                                $partyName = \App\OfficeEmployee::find($pid)->name ?? '';
                                            } elseif ($pt == 'App\StringSeller') {
                                                $partyName = \App\StringSeller::find($pid)->name ?? '';
                                            } elseif ($pt == 'App\WashingTeam') {
                                                $partyName = \App\WashingTeam::find($pid)->name ?? '';
                                            } elseif ($pt == 'App\FinishingTeam') {
                                                $partyName = \App\FinishingTeam::find($pid)->name ?? '';
                                            } elseif ($pt == 'App\Kachaee') {
                                                $partyName = \App\Kachaee::find($pid)->name ?? '';
                                            } elseif ($pt == 'App\NewDifferentAccount') {
                                                $partyName = \App\NewDifferentAccount::find($pid)->name ?? '';
                                            } elseif ($pt == 'App\DifferentAccount') {
                                                $partyName = \App\DifferentAccount::find($pid)->name ?? '';
                                            }
                                        @endphp
                                        <option value="{{ $pid }}" selected>{{ $partyName }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-dark shadow-sm px-4 rounded-pill">
                                    <i class="feather icon-filter"></i> تطبیق فیلترها (Filter)
                                </button>
                                @if(request()->fullUrl() != url('/dashboard/accounting/journals'))
                                    <a href="/dashboard/accounting/journals" class="btn btn-outline-danger px-4 rounded-pill">
                                        <i class="feather icon-x-circle"></i> پاک کردن
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table modern-table" id="journal-table-main">
                    <thead>
                        <tr>
                            <th>تاریخ</th>
                            <th>شناسه سند (GL ID)</th>
                            <th>نمبر سند (Ref)</th>
                            <th>تفصیلات (Description)</th>
                            <th>نوعیت</th>
                            <th class="text-center">حالت</th>
                            <th class="text-right">مجموع دیبت</th>
                            <th class="text-right px-4">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($transactions->count() > 0)
                            @foreach($transactions as $tx)
                            <tr class="journal-row-click"
                                data-journal-id="{{ $tx->journal_id ?? $tx->id }}"
                                data-ref="{{ $tx->reference }}"
                                data-date="{{ $tx->date }}"
                                data-desc="{{ $tx->description }}"
                                data-url="{{ route('accounting.journals.show', $tx->id) }}"
                                data-entries="{{ json_encode($tx->entries->map(function($e) {
                                    return [
                                        'code' => $e->account->account_code,
                                        'name' => $e->account->account_name,
                                        'debit' => $e->debit,
                                        'credit' => $e->credit,
                                        'currency' => $e->currency_code,
                                        'base_debit' => $e->base_debit,
                                        'base_credit' => $e->base_credit
                                    ];
                                })) }}">
                                <td>{{ $tx->date }}</td>
                                <td class="font-weight-bold text-dark">{{ $tx->journal_id ?? $tx->id }}</td>
                                <td class="font-weight-bold text-primary">{{ $tx->reference }}</td>
                                <td>
                                    <span class="text-dark d-block" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $tx->description }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light-info text-capitalize">{{ $tx->journal_type }}</span>
                                </td>
                                <td>
                                    @if($tx->status == 'posted')
                                        <span class="badge badge-success rounded-pill px-3">تایید شده</span>
                                    @else
                                        <span class="badge badge-danger rounded-pill px-3">ابطال شده</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-success" style="font-size: 1.05rem;">
                                    ${{ number_format($tx->entries->sum('base_debit'), 2) }}
                                </td>
                                <td class="text-right px-4">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <button type="button" class="btn-modern-action btn-details mr-1 btn-quick-view" 
                                                data-journal-id="{{ $tx->journal_id ?? $tx->id }}"
                                                data-ref="{{ $tx->reference }}"
                                                data-date="{{ $tx->date }}"
                                                data-desc="{{ $tx->description }}"
                                                data-url="{{ route('accounting.journals.show', $tx->id) }}"
                                                data-entries="{{ json_encode($tx->entries->map(function($e) {
                                                    return [
                                                        'code' => $e->account->account_code,
                                                        'name' => $e->account->account_name,
                                                        'debit' => $e->debit,
                                                        'credit' => $e->credit,
                                                        'currency' => $e->currency_code,
                                                        'base_debit' => $e->base_debit,
                                                        'base_credit' => $e->base_credit
                                                    ];
                                                })) }}"
                                                title="نمای سریع">
                                            <i class="feather icon-eye"></i>
                                        </button>
                                        <a href="{{ route('accounting.journals.show', $tx->id) }}" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" title="مشاهده صفحه کامل">
                                            <i class="feather icon-external-link"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="no-records-row">
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="feather icon-search fa-3x mb-3 d-block opacity-25"></i>
                                    هیچ سند حسابداری با این مشخصات یافت نشد
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $transactions->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- QUICK VIEW MODAL -->
<div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title font-weight-bold text-white"><i class="feather icon-eye"></i> جزئیات ردیف‌های سند حسابداری</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-md-4 col-4 text-left">
                        <span class="text-muted d-block small">شناسه سند (GL ID):</span>
                        <strong class="text-dark h5" id="qv-journal-id"></strong>
                    </div>
                    <div class="col-md-4 col-4 text-left">
                        <span class="text-muted d-block small">نمبر سند (Ref):</span>
                        <strong class="text-primary h5" id="qv-ref"></strong>
                    </div>
                    <div class="col-md-4 col-4 text-right">
                        <span class="text-muted d-block small">تاریخ (Date):</span>
                        <strong class="text-dark h5" id="qv-date"></strong>
                    </div>
                </div>
                <div class="p-3 mb-3 bg-light rounded" style="border-right: 4px solid #007bff; text-align: right;">
                    <span class="text-muted d-block small">تفصیلات کلی (Narrative):</span>
                    <p class="mb-0 text-dark font-weight-bold" id="qv-desc" style="font-size: 1rem;"></p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center" id="qv-table">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>کد حساب</th>
                                <th>حساب (Account Detail)</th>
                                <th>دیبت (Debit)</th>
                                <th>کریدت (Credit)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <a href="#" id="qv-details-btn" class="btn btn-primary rounded-pill px-4"><i class="feather icon-external-link"></i> مشاهده صفحه کامل</a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">بستن</button>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-light-info { background: #e0f7fa; color: #00838f; border-radius: 5px; padding: 5px 10px; }
    .btn-icon { width: 35px; height: 35px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>
@endsection

@section('footer-plugins')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script>
    function filterPartyTypeChanged(selectEl) {
        let partySelect = document.querySelector('#filter_party_id');
        let partyType = selectEl.value;

        // Destroy existing select2 if initialized
        if ($(partySelect).hasClass("select2-hidden-accessible")) {
            $(partySelect).select2('destroy');
        }

        if (!partyType) {
            partySelect.innerHTML = '<option value="">همه طرف های معامله</option>';
            partySelect.disabled = true;
            // Submit form to apply "all"
            selectEl.form.submit();
            return;
        }

        partySelect.disabled = false;
        partySelect.innerHTML = '<option value="">همه طرف های معامله</option>';

        // Initialize Select2 with AJAX to load parties dynamically
        $(partySelect).select2({
            placeholder: '-- انتخاب طرف معامله --',
            allowClear: true,
            ajax: {
                url: '{{ route("accounting.journals.api.parties") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        type: partyType,
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
        
        // Trigger opening the dropdown immediately
        $(partySelect).select2('open');
    }

    $(document).ready(function() {
        // Initialize Select2
        $('#account_filter').select2({
            width: '100%'
        });

        // Initialize filter party select if type is already selected
        if ($('#filter_party_type').val()) {
            $('#filter_party_id').select2({
                placeholder: '-- انتخاب طرف معامله --',
                allowClear: true,
                ajax: {
                    url: '{{ route("accounting.journals.api.parties") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            type: $('#filter_party_type').val(),
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });
        }

        // Clickable row to trigger quick view modal
        $('.journal-row-click').on('click', function(e) {
            if ($(e.target).closest('button, a').length === 0) {
                $(this).find('.btn-quick-view').click();
            }
        });

        // Populating the quick view modal
        $(document).on('click', '.btn-quick-view', function(e) {
            e.stopPropagation();
            var journalId = $(this).data('journal-id');
            var ref = $(this).data('ref');
            var date = $(this).data('date');
            var desc = $(this).data('desc');
            var url = $(this).data('url');
            var entries = $(this).data('entries');
            
            $('#qv-journal-id').text(journalId);
            $('#qv-ref').text(ref || '-');
            $('#qv-date').text(date);
            $('#qv-desc').text(desc);
            $('#qv-details-btn').attr('href', url);
            
            var tbody = $('#qv-table tbody');
            tbody.empty();
            
            entries.forEach(function(entry) {
                var debitText = entry.debit > 0 ? parseFloat(entry.debit).toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ' + entry.currency : '-';
                if (entry.currency !== 'USD' && entry.debit > 0) {
                    debitText += '<br><small class="text-muted">($' + parseFloat(entry.base_debit).toLocaleString(undefined, {minimumFractionDigits: 2}) + ')</small>';
                }
                
                var creditText = entry.credit > 0 ? parseFloat(entry.credit).toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ' + entry.currency : '-';
                if (entry.currency !== 'USD' && entry.credit > 0) {
                    creditText += '<br><small class="text-muted">($' + parseFloat(entry.base_credit).toLocaleString(undefined, {minimumFractionDigits: 2}) + ')</small>';
                }
                
                tbody.append('<tr>' +
                    '<td class="font-weight-bold">' + entry.code + '</td>' +
                    '<td class="text-right">' + entry.name + '</td>' +
                    '<td class="text-right font-weight-bold text-success">' + debitText + '</td>' +
                    '<td class="text-right font-weight-bold text-primary">' + creditText + '</td>' +
                '</tr>');
            });
            
            $('#quickViewModal').modal('show');
        });

        // Live client-side search keyup filter
        $('#live-journal-search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#journal-table-main tbody tr').filter(function() {
                if ($(this).hasClass('no-records-row')) return;
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $('#journal-table-main').DataTable({
            dom: 'Bfrtip',
            order: [],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="feather icon-file-text"></i> EXCEL',
                    className: 'btn btn-success rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - گزارش روزنامچه',
                    messageTop: 'گزارش تمامی تراکنش‌های مالی ثبت شده',
                    exportOptions: { columns: ':not(:last-child)' },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row c[r^="A1"]', sheet).attr('s', '51');
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="feather icon-file"></i> PDF',
                    className: 'btn btn-danger rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - گزارش روزنامچه',
                    exportOptions: { columns: ':not(:last-child)' },
                    customize: function(doc) {
                        doc.defaultStyle.font = 'Arial';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        doc.styles.tableHeader.fillColor = '#333';
                        doc.styles.tableHeader.color = 'white';
                        doc.styles.tableHeader.alignment = 'center';
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="feather icon-printer"></i> PRINT',
                    className: 'btn btn-dark rounded-pill px-4 shadow-sm',
                    title: 'QASIMI BROTHERS CARPET CO. - گزارش روزنامچه',
                    exportOptions: { columns: ':not(:last-child)' }
                }
            ],
            paging: false,
            searching: false, // We use live search and server search instead
            info: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.12.1/i18n/fa.json'
            }
        });
    });
</script>
<style>
    .dt-buttons { margin-bottom: 15px; margin-left: 20px; }
    .dataTables_filter { margin-right: 20px; }
</style>
@endsection
