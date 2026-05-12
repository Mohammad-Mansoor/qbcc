<?php
$file = '/home/anonymous/projects/public_html/resources/views/different-account/account-payment.blade.php';
$content = file_get_contents($file);

// Replace the first form inputs to include currency_code and exchange_rate
$oldFormInputs = '/<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">\s*<label class="pull-left">مقدار پول به دالر<\/label>\s*<input type="text" name="amount"\s*placeholder="مبلغ پول به دالر" class="form-control">\s*@error\(\'amount\'\) <p class="text-danger">\s*\{\{trans\(\'message\.\'\.\$message\)\}\}<\/p>\s*@enderror\s*<\/div>/';

$newFormInputs = '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <label class="pull-left">مقدار پول</label>
                                <input type="text" name="amount" placeholder="مبلغ پول" class="form-control" required>
                                @error(\'amount\') <p class="text-danger">{{trans(\'message.\'.$message)}}</p> @enderror
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <label class="pull-left">ارز</label>
                                <select name="currency_code" id="currency_code" class="form-control" required>
                                    <option value="USD">USD (دالر)</option>
                                    <option value="AFN">AFN (افغانی)</option>
                                    <option value="PKR">PKR (کلدار)</option>
                                    <option value="EUR">EUR (یورو)</option>
                                </select>
                                @error(\'currency_code\') <p class="text-danger">{{trans(\'message.\'.$message)}}</p> @enderror
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <label class="pull-left">نرخ تبدیل (به USD)</label>
                                <input type="text" name="exchange_rate" id="exchange_rate" value="1.000000" class="form-control" required readonly>
                                @error(\'exchange_rate\') <p class="text-danger">{{trans(\'message.\'.$message)}}</p> @enderror
                              </div>';

$content = preg_replace($oldFormInputs, $newFormInputs, $content);

// Replace the second form inputs (Edit form)
$oldFormInputsEdit = '/<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">\s*<label class="pull-right">مقدار پول به دالر<\/label>\s*<input type="text" name="amount" value="\{\{\$paymentEdit->amount\}\}"\s*class="form-control">\s*@error\(\'amount\'\) <p class="text-danger">\s*\{\{trans\(\'message\.\'\.\$message\)\}\}<\/p>\s*@enderror\s*<\/div>/';

$newFormInputsEdit = '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <label class="pull-right">مقدار پول</label>
                                <input type="text" name="amount" value="{{$paymentEdit->amount}}" class="form-control" required>
                                @error(\'amount\') <p class="text-danger">{{trans(\'message.\'.$message)}}</p> @enderror
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <label class="pull-right">ارز</label>
                                <select name="currency_code" id="currency_code_edit" class="form-control" required>
                                    <option value="USD" {{ $paymentEdit->currency_code == \'USD\' ? \'selected\' : \'\' }}>USD (دالر)</option>
                                    <option value="AFN" {{ $paymentEdit->currency_code == \'AFN\' ? \'selected\' : \'\' }}>AFN (افغانی)</option>
                                    <option value="PKR" {{ $paymentEdit->currency_code == \'PKR\' ? \'selected\' : \'\' }}>PKR (کلدار)</option>
                                    <option value="EUR" {{ $paymentEdit->currency_code == \'EUR\' ? \'selected\' : \'\' }}>EUR (یورو)</option>
                                </select>
                                @error(\'currency_code\') <p class="text-danger">{{trans(\'message.\'.$message)}}</p> @enderror
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <label class="pull-right">نرخ تبدیل</label>
                                <input type="text" name="exchange_rate" id="exchange_rate_edit" value="{{$paymentEdit->exchange_rate}}" class="form-control" required {{ $paymentEdit->currency_code == \'USD\' ? \'readonly\' : \'\' }}>
                                @error(\'exchange_rate\') <p class="text-danger">{{trans(\'message.\'.$message)}}</p> @enderror
                              </div>';

$content = preg_replace($oldFormInputsEdit, $newFormInputsEdit, $content);

// Update table headers
$oldHeaders = '/<td><b>رسید\(دالر\)<\/b><\/td>\s*<td><b>گرفت\(دالر\)<\/b><\/td>/';
$newHeaders = '<td><b>رسید</b></td>
                        <td><b>گرفت</b></td>
                        <td><b>ارز</b></td>
                        <td><b>نرخ تبدیل</b></td>
                        <td><b>معادل دالر</b></td>';
$content = preg_replace($oldHeaders, $newHeaders, $content);

// Update table body
$oldBody = '/@if\(\$pa->type == \'رسید\'\)\s*<td>\{\{\$pa->amount\}\}<\/td>\s*@else\s*<td>0<\/td>\s*@endif\s*@if\(\$pa->type == \'گرفت\'\)\s*<td>\{\{\$pa->amount\}\}<\/td>\s*@else\s*<td>0<\/td>\s*@endif/';

$newBody = '@if($pa->type == \'رسید\')
                            <td>{{$pa->amount}}</td>
                          @else
                            <td>0</td>
                          @endif
                          @if($pa->type == \'گرفت\')
                            <td>{{$pa->amount}}</td>
                          @else
                            <td>0</td>
                          @endif
                          <td><span class="badge badge-info">{{$pa->currency_code}}</span></td>
                          <td>{{$pa->exchange_rate}}</td>
                          <td>{{$pa->base_amount}} USD</td>';

$content = preg_replace($oldBody, $newBody, $content);

// Replace totals summary
$oldSummary = '/<tr>\s*<td><b>\{\{\$debits\}\} <\/b><\/td>\s*<td><b>گرفت ها\(دالر\)<\/b><\/td>\s*<\/tr>\s*<tr>\s*<td><b>\{\{\$credits\}\} <\/b><\/td>\s*<td><b>رسیدات\(دالر\)<\/b><\/td>\s*<\/tr>\s*<tr>\s*<td style="direction: ltr"><b> \{\{\$credits - \$debits\}\} <\/b><\/td>\s*<td><b>صرف بیلانس\(دالر\)<\/b><\/td>\s*<\/tr>/';

$newSummary = '<tr><td colspan="5" style="background:#eee;text-align:center;"><b>خلاصه حساب بر اساس ارز</b></td></tr>
                      @foreach($totals as $t)
                      <tr>
                        <td colspan="3"><b>رسیدات: {{$t->total}} | گرفت ها: {{$t->paid}}</b></td>
                        <td colspan="2"><b>بیلانس ({{$t->currency_code}}): <span dir="ltr">{{$t->remaining}}</span></b></td>
                      </tr>
                      @endforeach';

$content = preg_replace($oldSummary, $newSummary, $content);

// Add JS for currency dropdown
$js = '<script>
      $(document).ready(function () {
          // Logic for Create Form
          $("#currency_code").change(function() {
              if($(this).val() === "USD") {
                  $("#exchange_rate").val("1.000000").attr("readonly", true);
              } else {
                  $("#exchange_rate").attr("readonly", false);
              }
          });
          
          // Logic for Edit Form
          $("#currency_code_edit").change(function() {
              if($(this).val() === "USD") {
                  $("#exchange_rate_edit").val("1.000000").attr("readonly", true);
              } else {
                  $("#exchange_rate_edit").attr("readonly", false);
              }
          });
      });
  </script>';

$content = str_replace('@endsection', $js . "\n@endsection", $content);

file_put_contents($file, $content);
echo "Updated account-payment.blade.php\n";
