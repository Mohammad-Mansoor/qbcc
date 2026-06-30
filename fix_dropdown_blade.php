<?php
$content = file_get_contents('resources/views/customers/customer-payment.blade.php');

$jsData = <<<HTML
        // Accounting Data injected from backend
        const accountsData = {
            'رسید': {
                debit: [
                    @foreach(\$allowedDebitAccounts as \$acc)
                    { id: {{ \$acc->id }}, text: "بدهکار: {{ \$acc->account_name }}", selected: {{ (\$mapping && \$mapping->debit_account_id == \$acc->id) ? 'true' : 'false' }} },
                    @endforeach
                ],
                credit: [
                    @foreach(\$allowedCreditAccounts as \$acc)
                    { id: {{ \$acc->id }}, text: "بستانکار: {{ \$acc->account_name }}", selected: {{ (\$mapping && \$mapping->credit_account_id == \$acc->id) ? 'true' : 'false' }} },
                    @endforeach
                ]
            },
            'گرفت': {
                debit: [
                    @foreach(\$allowedDebitAccountsOut as \$acc)
                    { id: {{ \$acc->id }}, text: "بدهکار: {{ \$acc->account_name }}", selected: {{ (\$mappingOut && \$mappingOut->debit_account_id == \$acc->id) ? 'true' : 'false' }} },
                    @endforeach
                ],
                credit: [
                    @foreach(\$allowedCreditAccountsOut as \$acc)
                    { id: {{ \$acc->id }}, text: "بستانکار: {{ \$acc->account_name }}", selected: {{ (\$mappingOut && \$mappingOut->credit_account_id == \$acc->id) ? 'true' : 'false' }} },
                    @endforeach
                ]
            }
        };

        function updateAccountingDropdowns(type, isEdit = false) {
            const data = accountsData[type];
            if (!data) return;

            const debitSelect = isEdit ? $('#override_debit_account_id_edit') : $('#override_debit_account_id');
            const creditSelect = isEdit ? $('#override_credit_account_id_edit') : $('#override_credit_account_id');

            debitSelect.empty();
            data.debit.forEach(acc => {
                debitSelect.append(new Option(acc.text, acc.id, false, acc.selected));
            });

            creditSelect.empty();
            data.credit.forEach(acc => {
                creditSelect.append(new Option(acc.text, acc.id, false, acc.selected));
            });
            
            debitSelect.trigger('change.select2');
            creditSelect.trigger('change.select2');
        }

        $('#payment_type').on('change', function() {
            updateAccountingDropdowns($(this).val(), false);
        });
        
        $('#payment_type_edit').on('change', function() {
            updateAccountingDropdowns($(this).val(), true);
        });

        // Initialize on load
        if ($('#payment_type').length) {
            updateAccountingDropdowns($('#payment_type').val(), false);
        }
HTML;

$content = str_replace(
    "$('#customerDetailTabs a').on('click', function (e) {", 
    $jsData . "\n\n        $('#customerDetailTabs a').on('click', function (e) {", 
    $content
);

file_put_contents('resources/views/customers/customer-payment.blade.php', $content);
echo "Blade Javascript successfully updated!";
?>
