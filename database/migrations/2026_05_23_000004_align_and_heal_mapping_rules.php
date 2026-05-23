<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\MappingRule;
use App\ChartOfAccount;

class AlignAndHealMappingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         $rules = [
             // --- Sales ---
             [
                 'transaction_type' => 'sale',
                 'condition' => 'credit',
                 'debit_code' => '1300', // Accounts Receivable
                 'credit_code' => '4000', // Sales Revenue
                 'template' => 'فروش قالین (Carpet Sale)'
             ],
             
             // --- Customer Payments ---
             [
                 'transaction_type' => 'customer_payment',
                 'condition' => 'رسید', 
                 'debit_code' => '1000', // Cash
                 'credit_code' => '1300', // Accounts Receivable
                 'template' => 'رسید پول از مشتری (Customer Payment)'
             ],
             [
                 'transaction_type' => 'customer_payment',
                 'condition' => 'گرفت',
                 'debit_code' => '1300', // Accounts Receivable
                 'credit_code' => '1000', // Cash
                 'template' => 'استرداد پول به مشتری (Refund/Withdrawal to Customer)'
             ],
 
             // --- Agent Payments ---
             [
                 'transaction_type' => 'agent_payment',
                 'condition' => 'رسید', 
                 'debit_code' => '1000', // Cash
                 'credit_code' => '1300', // Accounts Receivable
                 'template' => 'رسید پول از نماینده (Agent Receipt)'
             ],
             [
                 'transaction_type' => 'agent_payment',
                 'condition' => 'گرفت',
                 'debit_code' => '1300', // Accounts Receivable
                 'credit_code' => '1000', // Cash
                 'template' => 'پرداخت پول به نماینده (Agent Payment)'
             ],
 
             // --- Material Purchases ---
             [
                 'transaction_type' => 'material_purchase',
                 'condition' => 'credit',
                 'debit_code' => '5000', // COGS
                 'credit_code' => '2100', // Accounts Payable
                 'template' => 'خریداری مواد (Material Purchase)'
             ],
 
             // --- Production Costs ---
             [
                 'transaction_type' => 'washing',
                 'condition' => 'credit',
                 'debit_code' => '5000', // COGS
                 'credit_code' => '2100', // Accounts Payable
                 'template' => 'هزینه شست (Washing Cost)'
             ],
             [
                 'transaction_type' => 'finishing',
                 'condition' => 'credit',
                 'debit_code' => '5000', // COGS
                 'credit_code' => '2100', // Accounts Payable
                 'template' => 'هزینه تیاری (Finishing Cost)'
             ],
 
             // --- Supplier/Team Payments ---
             [
                 'transaction_type' => 'seller_payment',
                 'condition' => 'گرفت',
                 'debit_code' => '2100', // Accounts Payable
                 'credit_code' => '1000', // Cash
                 'template' => 'پرداخت پول به فروشنده (Seller Payment)'
             ],
             [
                 'transaction_type' => 'washing_payment',
                 'condition' => 'گرفت',
                 'debit_code' => '2100', // Accounts Payable
                 'credit_code' => '1000', // Cash
                 'template' => 'پرداخت پول به تیم شست (Washing Team Payment)'
             ],
             [
                 'transaction_type' => 'finishing_payment',
                 'condition' => 'گرفت',
                 'debit_code' => '2100', // Accounts Payable
                 'credit_code' => '1000', // Cash
                 'template' => 'پرداخت پول به تیم تیاری (Finishing Team Payment)'
             ],
             [
                 'transaction_type' => 'kachaee_payment',
                 'condition' => 'گرفت',
                 'debit_code' => '2100', // Accounts Payable
                 'credit_code' => '1000', // Cash
                 'template' => 'پرداخت پول به بخش کچایی (Kachaee Payment)'
             ],
             [
                 'transaction_type' => 'kachaee_payment',
                 'condition' => 'رسید',
                 'debit_code' => '1000', // Cash
                 'credit_code' => '2100', // Accounts Payable
                 'template' => 'رسید پول از بخش کچایی (Kachaee Receipt)'
             ],
 
             // --- Expenses (Operational) ---
             [
                 'transaction_type' => 'expense',
                 'condition' => 'خوراکه',
                 'debit_code' => '6000', // Operational Expenses
                 'credit_code' => '1000', // Cash
                 'template' => 'هزینه خوراکه (Food Expense)'
             ],
             [
                 'transaction_type' => 'expense',
                 'condition' => 'کرایه و برق',
                 'debit_code' => '6000', 
                 'credit_code' => '1000', 
                 'template' => 'هزینه کرایه و برق (Rent/Electricity Expense)'
             ],
             [
                 'transaction_type' => 'expense',
                 'condition' => 'ترانسپورت',
                 'debit_code' => '6000', 
                 'credit_code' => '1000', 
                 'template' => 'هزینه ترانسپورت (Transport Expense)'
             ],
             [
                 'transaction_type' => 'expense',
                 'condition' => 'متفرقه',
                 'debit_code' => '6000', 
                 'credit_code' => '1000', 
                 'template' => 'هزینه های متفرقه (Misc Expense)'
             ],
 
             // --- Employee Salaries ---
             [
                 'transaction_type' => 'employee_payment',
                 'condition' => 'گرفت',
                 'debit_code' => '6000', // Salary Expense (Direct hit)
                 'credit_code' => '1000', // Cash
                 'template' => 'پرداخت معاش کارمند (Employee Salary Payment)'
             ],
             [
                 'transaction_type' => 'employee_payment',
                 'condition' => 'رسید',
                 'debit_code' => '1000', // Cash
                 'credit_code' => '6000', // Reverse Salary Expense
                 'template' => 'رسید پول از کارمند (Refund from Employee)'
             ],
 
             // --- Office Cash (Credit/Debit) ---
             [
                 'transaction_type' => 'office_credit',
                 'condition' => 'deposit',
                 'debit_code' => '1000', // Cash
                 'credit_code' => '3000', // Equity (assuming direct investment)
                 'template' => 'تزریق سرمایه به دخل (Capital/Cash Injection)'
             ],
             [
                 'transaction_type' => 'office_debit',
                 'condition' => 'withdrawal',
                 'debit_code' => '6000', // Generic Expense
                 'credit_code' => '1000', // Cash
                 'template' => 'برداشت پول از دخل (Cash Withdrawal)'
             ],
 
             // --- Fixed Assets (Ajnas) ---
             [
                 'transaction_type' => 'ajnas_account',
                 'condition' => 'purchase',
                 'debit_code' => '1500', // Fixed Assets
                 'credit_code' => '1000', // Cash
                 'template' => 'خریداری اجناس و جایداد (Fixed Asset Purchase)'
             ],
 
             // --- Different Accounts (Miscellaneous) ---
             [
                 'transaction_type' => 'different_account',
                 'condition' => 'رسید',
                 'debit_code' => '1000', // Cash
                 'credit_code' => '2100', // Accounts Payable
                 'template' => 'رسید متفرقه (Miscellaneous Receipt)'
             ],
             [
                 'transaction_type' => 'different_account',
                 'condition' => 'گرفت',
                 'debit_code' => '2100', // Accounts Payable
                 'credit_code' => '1000', // Cash
                 'template' => 'پرداخت متفرقه (Miscellaneous Payment)'
             ],
         ];
 
         $resolveAccount = function($code) {
             $exact = ChartOfAccount::where('account_code', $code)->first();
             if ($exact) return $exact;
 
             $fallbacks = [
                 '1000' => '1100', // Cash
                 '4000' => '4100', // Sales Revenue
                 '5000' => '5100', // COGS
                 '6000' => '6500', // Other/Operational Expenses
                 '3000' => '3100', // Capital
             ];
 
             if (isset($fallbacks[$code])) {
                 $fallbackAcc = ChartOfAccount::where('account_code', $fallbacks[$code])->first();
                 if ($fallbackAcc) return $fallbackAcc;
             }
 
             if (strpos($code, '6') === 0) {
                 $expense = ChartOfAccount::where('account_type', 'Expense')->orderBy('account_code')->first();
                 if ($expense) return $expense;
             }
 
             return null;
         };
 
         foreach ($rules as $rule) {
             $slug = '';
             switch ($rule['condition']) {
                 case 'خوراکه': $slug = 'EXP_FOOD'; break;
                 case 'ترانسپورت': $slug = 'EXP_TRANS'; break;
                 case 'متفرقه': $slug = 'EXP_MISC'; break;
                 case 'گرفت': $slug = 'PYMT_OUT'; break;
                 case 'رسید': $slug = 'PYMT_IN'; break;
                 case 'deposit': $slug = 'CASH_IN'; break;
                 case 'withdrawal': $slug = 'CASH_OUT'; break;
                 case 'purchase': $slug = 'ASSET_PURCH'; break;
                 default: $slug = strtoupper(str_replace(' ', '_', $rule['transaction_type'] . '_' . $rule['condition']));
             }
 
             $existing = MappingRule::where('transaction_type', $rule['transaction_type'])
                 ->where('condition', $rule['condition'])
                 ->first();
 
             $debit = $resolveAccount($rule['debit_code']);
             $credit = $resolveAccount($rule['credit_code']);
 
             if ($existing) {
                 // Rule exists. Update mapping_key but preserve customized debit/credit accounts
                 $updateData = ['mapping_key' => $slug];
 
                 // If debit or credit is not set or invalid (e.g. points to non-existent account), fill with default
                 if (!$existing->debit_account_id || !ChartOfAccount::find($existing->debit_account_id)) {
                     if ($debit) {
                         $updateData['debit_account_id'] = $debit->id;
                     }
                 }
                 if (!$existing->credit_account_id || !ChartOfAccount::find($existing->credit_account_id)) {
                     if ($credit) {
                         $updateData['credit_account_id'] = $credit->id;
                     }
                 }
 
                 $existing->update($updateData);
             } else {
                 // Rule does not exist. Create it with defaults.
                 if ($debit && $credit) {
                     MappingRule::create([
                         'transaction_type' => $rule['transaction_type'],
                         'condition' => $rule['condition'],
                         'mapping_key' => $slug,
                         'debit_account_id' => $debit->id,
                         'credit_account_id' => $credit->id,
                         'description_template' => $rule['template']
                     ]);
                 }
             }
         }
 
         // Clean up any remaining null mapping keys in the database using the standard slug format
         MappingRule::whereNull('mapping_key')->get()->each(function($rule) {
             $slug = '';
             switch ($rule->condition) {
                 case 'خوراکه': $slug = 'EXP_FOOD'; break;
                 case 'ترانسپورت': $slug = 'EXP_TRANS'; break;
                 case 'متفرقه': $slug = 'EXP_MISC'; break;
                 case 'گرفت': $slug = 'PYMT_OUT'; break;
                 case 'رسید': $slug = 'PYMT_IN'; break;
                 case 'deposit': $slug = 'CASH_IN'; break;
                 case 'withdrawal': $slug = 'CASH_OUT'; break;
                 case 'purchase': $slug = 'ASSET_PURCH'; break;
                 default: $slug = strtoupper(str_replace(' ', '_', $rule->transaction_type . '_' . $rule->condition));
             }
             $rule->update(['mapping_key' => $slug]);
         });
     }
 
     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         // Non-destructive, nothing to revert
     }
}
