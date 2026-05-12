<?php
$file = '/home/anonymous/projects/public_html/resources/views/different-account/accounts.blade.php';
$content = file_get_contents($file);

// 1. Update headers
$content = str_replace('<th>باقیات(دالر)</th>', '<th>باقیات</th>', $content);
$content = str_replace('<th>طلبات(دالر)</th>', '<th>طلبات</th>', $content);

// 2. Replace the body logic for each account role
$roles = ['center_accounts', 'froshat_accounts', 'mo_accounts', 'sp_accounts'];

foreach ($roles as $role) {
    // We are replacing the two <td> blocks that display remaining/talab and the global sum
    $pattern = '/@if\(\$account->total\)\s*@if\(\$account->total->remaining <= 0\)\s*<span style="display: none">\{\{\$remaining \+=\$account->total->remaining \}\}<\/span>\s*<td style="direction: ltr;color: red;">\{\{\$account->total->remaining \}\}<\/td>\s*@else\s*<td style="direction: ltr">0<\/td>\s*@endif\s*@else\s*<td><\/td>\s*@endif\s*@if\(\$account->total\)\s*@if\(\$account->total->remaining >= 0\)\s*<span style="display: none">\{\{\$talab \+= \$account->total->remaining \}\}<\/span>\s*<td style="direction: ltr;color: green;">\{\{\$account->total->remaining \}\}<\/td>\s*@else\s*<td style="direction: ltr">0<\/td>\s*@endif\s*@else\s*<td><\/td>\s*@endif/';

    $replacement = '
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining <= 0)
                          <div style="direction: ltr;color: red;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>
                    <td>
                      @foreach($account->totals as $t)
                        @if($t->remaining > 0)
                          <div style="direction: ltr;color: green;font-size: 11px;">{{$t->remaining}} {{$t->currency_code}}</div>
                        @endif
                      @endforeach
                    </td>';
    
    $content = preg_replace($pattern, $replacement, $content);
}

// 3. Remove the @php($remaining = 0) and the global sum row at the bottom.
// Multi-currency global sums require complex grouping which is beyond the scope of a simple blade list without controller prep.
// It's cleaner to remove the single global sum row.
$content = preg_replace('/@php\(\$remaining = 0\)\s*@php\(\$talab = 0\)/', '', $content);

$globalSumPattern1 = '/@if\(!isset\(\$search\)\)\s*<tr style="background: gainsboro">\s*<td><\/td>\s*<td><\/td>\s*<td><\/td>\s*<td>\{\{\$remaining\}\}<\/td>\s*<td style="direction: ltr">\{\{\$talab\}\}<\/td>\s*<td>مجموعه<\/td>\s*<\/tr>\s*@endif/';
$content = preg_replace($globalSumPattern1, '', $content);

$globalSumPattern2 = '/@if\(!isset\(\$search\)\)\s*<tr style="background: gainsboro">\s*<td><\/td>\s*<td><\/td>\s*<td><\/td>\s*<td><\/td>\s*<td>\{\{\$remaining\}\}<\/td>\s*<td style="direction: ltr">\{\{\$talab\}\}<\/td>\s*<td>مجموعه<\/td>\s*<\/tr>\s*@endif/';
$content = preg_replace($globalSumPattern2, '', $content);

file_put_contents($file, $content);
echo "accounts.blade.php updated\n";
