<?php
$content = file_get_contents('resources/views/finishing-center/finishing-payment.blade.php');

$recon_tab = <<<HTML
                <!-- Tab 5: Finishing Reconciliation (Allocations History) -->
                <div class="tab-pane fade" id="finishing_reconciliation" role="tabpanel">
                    @php
                        \$finishingAllocations = \App\FinishingPaymentAllocation::whereHas('payment', function(\$q) use (\$team) {
                            \$q->where('team_id', \$team->id)->where('status', '!=', 2);
                        })->with(['payment', 'allocatable'])->orderBy('id', 'DESC')->get();

                        \$directPayments = \App\FinishingTeamPayment::where('team_id', \$team->id)
                            ->where('finish_number', '!=', 'General')
                            ->where('is_advance', 0)
                            ->where('status', '!=', 2)
                            ->orderBy('date', 'DESC')->get();
                    @endphp
                    <div class="premium-card">
                        <div class="card-header-premium text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--secondary-amber) 0%, var(--accent-gold) 100%);">
                            <h5><i class="fa fa-undo mr-2"></i> تاریخچه تخصیص و تصفیه پیش‌پرداخت‌ها (Reconciliation History)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover mb-0 text-right">
                                    <thead>
                                        <tr>
                                            <th>تاریخ تخصیص (Allocation Date)</th>
                                            <th>سند پیش‌پرداخت (Source Advance)</th>
                                            <th>گروپ آماده‌سازی مقصد (Target Batch)</th>
                                            <th>مبلغ تخصیص (Allocated Amount)</th>
                                            <th>نرخ ارز (Exchange Rate)</th>
                                            <th>معادل دالر (Base USD Allocated)</th>
                                            <th class="hideOnPrint">عملیات (Action)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Direct Payments Section -->
                                        @if(count(\$directPayments) > 0)
                                        <tr class="bg-light">
                                            <td colspan="7" class="font-weight-bold text-center text-primary">
                                                <i class="fa fa-arrow-circle-down mr-1"></i> پرداخت‌های مستقیم روی بیل (Direct Payments)
                                            </td>
                                        </tr>
                                        @foreach(\$directPayments as \$dp)
                                        <tr>
                                            <td>{{ \$dp->date }}</td>
                                            <td>
                                                <span class="badge badge-info px-3 py-2">
                                                    مستقیم (Direct Payment)
                                                </span>
                                            </td>
                                            <td><strong>بل نمبر: {{ \$dp->finish_number }}</strong></td>
                                            <td class="font-weight-bold" style="direction: ltr;">{{ number_format(\$dp->original_amount, 2) }} {{ \$dp->currency_code }}</td>
                                            <td class="text-muted" style="direction: ltr;">{{ number_format(\$dp->exchange_rate, 4) }}</td>
                                            <td class="font-weight-bold" style="direction: ltr;">$ {{ number_format(\$dp->base_amount, 2) }}</td>
                                            <td class="hideOnPrint">
                                                @can('cancel_finishing_payment')
                                                <button type="button" onclick="deletePayment({{\$dp->id}}, {{\$dp->team_id}})" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa fa-trash"></i> ابطال
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif

                                        <!-- Allocations Section -->
                                        @if(count(\$finishingAllocations) > 0)
                                        <tr class="bg-light">
                                            <td colspan="7" class="font-weight-bold text-center text-primary">
                                                <i class="fa fa-link mr-1"></i> تخصیص پیش‌پرداخت‌ها (Advance Allocations)
                                            </td>
                                        </tr>
                                        @foreach(\$finishingAllocations as \$alloc)
                                        <tr>
                                            <td>{{ \$alloc->created_at ? \$alloc->created_at->format('Y-m-d') : '---' }}</td>
                                            <td>
                                                <a href="#" class="font-weight-bold">
                                                    F-PAY-{{ \$alloc->finishing_team_payment_id }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ \$alloc->payment->description ?? '' }}</small>
                                            </td>
                                            <td>
                                                @if(\$alloc->allocatable)
                                                    <span class="badge badge-info text-white">گروپ آماده‌سازی (تیاری)</span>
                                                    <strong>{{ \$alloc->allocatable->reference_number }}</strong>
                                                @else
                                                    <span class="text-danger">سند حذف شده</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-success" style="direction: ltr;">
                                                {{ number_format(\$alloc->allocated_amount, 2) }} {{ \$alloc->payment->currency_code ?? 'USD' }}
                                            </td>
                                            <td class="text-muted small" style="direction: ltr;">{{ number_format(\$alloc->exchange_rate, 4) }}</td>
                                            <td class="font-weight-bold text-dark" style="direction: ltr;">
                                                $ {{ number_format(\$alloc->base_allocated_amount, 2) }}
                                            </td>
                                            <td class="hideOnPrint">
                                                @can('cancel_finishing_payment')
                                                <button onclick="removeAllocation({{ \$alloc->id }})" class="btn btn-sm btn-outline-danger shadow-sm" title="حذف تخصیص">
                                                    <i class="fa fa-undo"></i> لغو تصفیه
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                        
                                        @if(count(\$finishingAllocations) == 0 && count(\$directPayments) == 0)
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
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
                </div>
HTML;

$content = preg_replace(
    "/<!-- Tab 5: Finishing Reconciliation \(Allocations History\) -->.*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s",
    $recon_tab,
    $content
);

file_put_contents('resources/views/finishing-center/finishing-payment.blade.php', $content);
echo "Replaced recon tab";
?>
