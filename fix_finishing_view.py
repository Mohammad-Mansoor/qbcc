import re

with open('resources/views/finishing-center/finishing-payment.blade.php', 'r') as f:
    content = f.read()

# 1. Add where status != 2 to Advances query
content = re.sub(
    r"(\$finishingAdvances\s*=\s*\\App\\FinishingTeamPayment::where\('team_id',\s*\$team->id\))([^;]*?)(->orderBy\('date',\s*'DESC'\)->get\(\);)",
    r"\1\2->where('status', '!=', 2)\3",
    content
)

# Also fix the blade inline PHP query if it exists
content = re.sub(
    r"(\$advances\s*=\s*\\App\\FinishingTeamPayment::where\('team_id',\s*\$team->id\)\s*->where\('is_advance',\s*true\))(\s*->orderBy\('date',\s*'DESC'\)\s*->get\(\);)",
    r"\1->where('status', '!=', 2)\2",
    content
)

# 2. Update Reconciliations Tab to include Direct Payments and change title
recon_tab = """<!-- Tab 5: Finishing Reconciliation (Allocations History) -->
                <div class="tab-pane fade" id="finishing_reconciliation" role="tabpanel">
                    @php
                        $finishingAllocations = \App\FinishingPaymentAllocation::whereHas('payment', function($q) use ($team) {
                            $q->where('team_id', $team->id)->where('status', '!=', 2);
                        })->with(['payment', 'allocatable'])->orderBy('id', 'DESC')->get();

                        $directPayments = \App\FinishingTeamPayment::where('team_id', $team->id)
                            ->where('finish_number', '!=', 'General')
                            ->where('is_advance', 0)
                            ->where('status', '!=', 2)
                            ->orderBy('date', 'DESC')->get();
                    @endphp
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #01579b 0%, #0277bd 100%);">
                            <h5><i class="fa fa-undo mr-2"></i> تاریخچه تخصیص و تصفیه پیش‌پرداخت‌ها (Reconciliation History)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover mb-0 text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ تخصیص (Date)</th>
                                            <th>منبع پول (Payment Source)</th>
                                            <th>مقصد تصفیه (Allocated To)</th>
                                            <th>شرح (Description)</th>
                                            <th>مقدار تخصیص (Allocated Amount)</th>
                                            <th>نرخ تبادله (Exchange Rate)</th>
                                            <th>معادل دالر (Base USD)</th>
                                            <th class="hideOnPrint">عملیات (Action)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Direct Payments Section -->
                                        @if(count($directPayments) > 0)
                                        <tr class="bg-light">
                                            <td colspan="8" class="font-weight-bold text-center text-primary">
                                                <i class="fa fa-arrow-circle-down mr-1"></i> پرداخت‌های مستقیم روی بیل (Direct Payments)
                                            </td>
                                        </tr>
                                        @foreach($directPayments as $dp)
                                        <tr>
                                            <td>{{ $dp->date }}</td>
                                            <td>
                                                <span class="badge badge-info px-3 py-2">
                                                    مستقیم (Direct Payment)
                                                </span>
                                            </td>
                                            <td><strong>بل نمبر: {{ $dp->finish_number }}</strong></td>
                                            <td>{{ $dp->description }}</td>
                                            <td class="font-weight-bold" style="direction: ltr;">{{ number_format($dp->original_amount, 2) }} {{ $dp->currency_code }}</td>
                                            <td class="text-muted" style="direction: ltr;">{{ number_format($dp->exchange_rate, 4) }}</td>
                                            <td class="font-weight-bold" style="direction: ltr;">$ {{ number_format($dp->base_amount, 2) }}</td>
                                            <td class="hideOnPrint">
                                                @can('cancel_finishing_payment')
                                                <form action="/dashboard/finishing-payments/{{ $dp->id }}" method="POST" style="display:inline-block;" onsubmit="return confirm('آیا مطمئن هستید؟ این پرداخت به طور کامل باطل و از حسابات مالی برگشت داده می‌شود.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fa fa-times-circle"></i> ابطال
                                                    </button>
                                                </form>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif

                                        <!-- Allocations Section -->
                                        @if(count($finishingAllocations) > 0)
                                        <tr class="bg-light">
                                            <td colspan="8" class="font-weight-bold text-center text-primary">
                                                <i class="fa fa-link mr-1"></i> تخصیص پیش‌پرداخت‌ها (Advance Allocations)
                                            </td>
                                        </tr>
                                        @foreach($finishingAllocations as $alloc)
                                        <tr>
                                            <td>{{ $alloc->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="#" class="text-decoration-none">
                                                    <i class="fa fa-share-square-o mr-1"></i>
                                                    <strong>F-PAY-{{ $alloc->finishing_payment_id }}</strong>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary px-2 py-1">بیل تیاری: {{ $alloc->allocatable->reference_number ?? 'N/A' }}</span>
                                            </td>
                                            <td>تخصیص به بیل {{ $alloc->allocatable->reference_number ?? 'N/A' }}</td>
                                            <td class="font-weight-bold text-success" style="direction: ltr;">
                                                {{ number_format($alloc->allocated_amount, 2) }} {{ $alloc->payment->currency_code }}
                                            </td>
                                            <td class="text-muted" style="direction: ltr;">{{ number_format($alloc->exchange_rate, 4) }}</td>
                                            <td class="font-weight-bold" style="direction: ltr;">$ {{ number_format($alloc->base_allocated_amount, 2) }}</td>
                                            <td class="hideOnPrint">
                                                @can('cancel_finishing_payment')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-allocation-btn" 
                                                        data-id="{{ $alloc->id }}" 
                                                        data-amount="{{ $alloc->allocated_amount }}"
                                                        data-currency="{{ $alloc->payment->currency_code }}">
                                                    <i class="fa fa-trash"></i> لغو تخصیص
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                        
                                        @if(count($finishingAllocations) == 0 && count($directPayments) == 0)
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fa fa-folder-open-o fa-2x mb-2 d-block"></i>
                                                هیچ تاریخچه تخصیص یا پرداخت مستقیمی یافت نشد
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>"""

# Replace the Reconciliations tab
content = re.sub(
    r"<!-- Tab 5: Finishing Reconciliation \(Allocations History\) -->.*?</div>\s*</div>\s*</div>\s*</div>",
    recon_tab,
    content,
    flags=re.DOTALL
)

# 3. Add cancel permission to delete buttons in Wage Ledger (Tab 3)
content = re.sub(
    r"<button type=\"button\" class=\"btn btn-sm btn-outline-danger delete-btn\".*?</button>",
    r"@can('cancel_finishing_payment')\n                                                    \g<0>\n                                                @endcan",
    content,
    flags=re.DOTALL
)

# 4. Remove printStatement Javascript
content = re.sub(
    r"    function printStatement\(\) \{\s*window\.print\(\);\s*\}",
    "",
    content
)

# 5. Hide Delete Button logic in javascript based on permission - no wait, better to just let blade handle it
# Wait, deletePayment AJAX in Javascript is still fine to keep.

# 6. Remove GL Statement Tab completely
content = re.sub(
    r"<!-- Tab 6: Unified GL Statement -->.*?</div>\s*</div>\s*</div>\s*</div>",
    "",
    content,
    flags=re.DOTALL
)

# Remove the li for GL Statement
content = re.sub(
    r"<li class=\"nav-item premium-nav-item\">\s*<a class=\"nav-link premium-nav-link\" data-toggle=\"tab\" href=\"#gl_statement\".*?</li>",
    "",
    content,
    flags=re.DOTALL
)


with open('resources/views/finishing-center/finishing-payment.blade.php', 'w') as f:
    f.write(content)

print("Replacement complete")
