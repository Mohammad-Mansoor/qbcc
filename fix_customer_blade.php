<?php
$content = file_get_contents('resources/views/customers/customer-payment.blade.php');

$recon_tab_link = <<<HTML
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="sales-tab" data-toggle="tab" href="#sales-invoices" role="tab" style="background: transparent; border: none; padding: 15px 20px;">
                                <i class="fa fa-shopping-cart"></i> انوایس‌های فروش قالین (Sales Invoices)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-white-50" id="recon-tab" data-toggle="tab" href="#customer-reconciliation" role="tab" style="background: transparent; border: none; padding: 15px 20px;">
                                <i class="fa fa-undo"></i> تصفیه‌ها (Reconciliations)
                            </a>
                        </li>
HTML;

$content = preg_replace(
    "/<li class=\"nav-item\">\s*<a class=\"nav-link font-weight-bold text-white-50\" id=\"sales-tab\" data-toggle=\"tab\" href=\"#sales-invoices\" role=\"tab\" style=\"background: transparent; border: none; padding: 15px 20px;\">\s*<i class=\"fa fa-shopping-cart\"><\/i> انوایس‌های فروش قالین \(Sales Invoices\)\s*<\/a>\s*<\/li>/",
    $recon_tab_link,
    $content
);


$recon_tab_content = <<<HTML
                    <!-- Tab 3: Reconciliations -->
                    <div class="tab-pane fade" id="customer-reconciliation" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table table-hover mb-0 text-right">
                                <thead>
                                    <tr class="text-right">
                                        <th>تاریخ (Date)</th>
                                        <th>سند پرداخت (Payment)</th>
                                        <th>تخصیص به انوایس (Allocated Invoice)</th>
                                        <th>مبلغ پرداختی (Amount)</th>
                                        <th>ارز (Currency)</th>
                                        <th>نرخ ارز (Exchange Rate)</th>
                                        <th>معادل دالر (Base USD)</th>
                                        <th class="hideOnPrint text-center">عملیات (Action)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(\$allocatedPayments as \$allocPay)
                                    <tr class="text-right">
                                        <td>{{ \$allocPay->date }}</td>
                                        <td>
                                            <span class="font-weight-bold">PAY-{{ \$allocPay->id }}</span><br>
                                            <small class="text-muted">{{ \$allocPay->description }}</small>
                                        </td>
                                        <td>
                                            @foreach(\$allocPay->allocations as \$allocation)
                                                <span class="badge badge-info mb-1">
                                                    انوایس {{ \$allocation->invoice->invoice_no ?? 'N/A' }} 
                                                    (\$ {{ number_format(\$allocation->amount_applied, 2) }})
                                                </span><br>
                                            @endforeach
                                        </td>
                                        <td class="font-weight-bold" style="direction: ltr;">{{ number_format(\$allocPay->original_amount ?? (\$allocPay->amount > 0 ? \$allocPay->amount : \$allocPay->amount_af), 2) }}</td>
                                        <td><span class="badge badge-light border text-dark">{{ \$allocPay->currency_code ?? (\$allocPay->amount > 0 ? 'USD' : 'AFN') }}</span></td>
                                        <td class="text-muted" style="direction: ltr;">{{ number_format(\$allocPay->exchange_rate ?? (\$allocPay->amount > 0 ? 1.0 : (1 / (\$allocPay->dollar_rate > 0 ? \$allocPay->dollar_rate : 1))), 4) }}</td>
                                        <td class="font-weight-bold text-primary" style="direction: ltr;">$ {{ number_format(\$allocPay->base_amount ?? (\$allocPay->amount > 0 ? \$allocPay->amount : (\$allocPay->amount_af * (\$allocPay->exchange_rate ?? 1.0))), 2) }}</td>
                                        <td class="hideOnPrint text-center">
                                            @can('cancel_customer_payment')
                                            <button type="button" onclick="deletePayment({{\$allocPay->id}}, {{\$allocPay->customer_id}})" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i> ابطال
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">هیچ پرداخت تخصیص یافته‌ای یافت نشد.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
HTML;

$content = preg_replace(
    "/<\/div>\s*<\/div>\s*<\/div>\s*<div class=\"p-3\">/",
    $recon_tab_content . "\n                <div class=\"p-3\">",
    $content
);

file_put_contents('resources/views/customers/customer-payment.blade.php', $content);
echo "Blade updated";
?>
