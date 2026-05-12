<?php
$file = '/home/anonymous/projects/public_html/app/Http/Controllers/DifferentAccountController.php';
$content = file_get_contents($file);

// Replace show method
$oldShow = '/public function show\(\$id\)\s*\{\s*\$account = DifferentAccount::find\(\$id\);\s*\$payments = DifferentAccountPayment::where\(\'account_id\',\$id\)->orderBy\(\'created_at\',\'DESC\'\)->paginate\(30\);\s*\$total = DifferentAccountTotal::where\(\'account_id\',\$id\)->sum\(\'total\'\);\s*\$debits = DifferentAccountPayment::where\(\'type\',\'=\',\'گرفت\'\)->where\(\'account_id\',\$id\)->where\(\'status\',1\)->sum\(\'amount\'\);\s*\$credits = DifferentAccountPayment::where\(\'type\',\'=\',\'رسید\'\)->where\(\'account_id\',\$id\)->where\(\'status\',1\)->sum\(\'amount\'\);\s*\$paymentEdit = \'\';\s*return view\(\'different-account\.account-payment\',compact\(\'account\',\'payments\',\'total\',\'debits\',\'credits\',\'paymentEdit\'\)\);\s*\}/';

$newShow = 'public function show($id)
    {
        $account = DifferentAccount::find($id);
        $payments = DifferentAccountPayment::where(\'account_id\',$id)->orderBy(\'created_at\',\'DESC\')->paginate(30);
        $totals = \App\DifferentAccountTotal::where(\'account_id\', $id)->get();
        $paymentEdit = \'\';
        return view(\'different-account.account-payment\',compact(\'account\',\'payments\',\'totals\',\'paymentEdit\'));
    }';

$content = preg_replace($oldShow, $newShow, $content);

// Replace show_all_payment method
$oldShowAll = '/public function show_all_payment\(\$account_id\)\{\s*\$account = DifferentAccount::find\(\$account_id\);\s*\$payments = DifferentAccountPayment::where\(\'account_id\',\$account_id\)->orderBy\(\'created_at\',\'DESC\'\)->get\(\);\s*\$total = DifferentAccountTotal::where\(\'account_id\',\$account_id\)->sum\(\'total\'\);\s*\$debits = DifferentAccountPayment::where\(\'type\',\'=\',\'گرفت\'\)->where\(\'account_id\',\$account_id\)->where\(\'status\',1\)->sum\(\'amount\'\);\s*\$credits = DifferentAccountPayment::where\(\'type\',\'=\',\'رسید\'\)->where\(\'account_id\',\$account_id\)->where\(\'status\',1\)->sum\(\'amount\'\);\s*\$paymentEdit = \'\';\s*\$all = \'\';\s*return view\(\'different-account\.account-payment\',compact\(\'account\',\'payments\',\'total\',\'debits\',\'credits\',\'paymentEdit\',\'all\'\)\);\s*\}/';

$newShowAll = 'public function show_all_payment($account_id){
        $account = DifferentAccount::find($account_id);
        $payments = DifferentAccountPayment::where(\'account_id\',$account_id)->orderBy(\'created_at\',\'DESC\')->get();
        $totals = \App\DifferentAccountTotal::where(\'account_id\', $account_id)->get();
        $paymentEdit = \'\';
        $all = \'\';
        return view(\'different-account.account-payment\',compact(\'account\',\'payments\',\'totals\',\'paymentEdit\',\'all\'));
    }';

$content = preg_replace($oldShowAll, $newShowAll, $content);

file_put_contents($file, $content);
echo "Updated DifferentAccountController\n";
