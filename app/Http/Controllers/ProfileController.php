<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Activity;

class ProfileController extends Controller
{
    /**
     * Show the user profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $rawPermissions = $user->getAllPermissions()->pluck('name');
        
        $permissions = [];
        foreach($rawPermissions as $perm) {
            $permissions[] = $this->translatePermission($perm);
        }
        
        $activities = Activity::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('profile.index', compact('user', 'permissions', 'activities'));
    }

    private function translatePermission($name)
    {
        $prefixes = [
            'view_' => 'مشاهده ',
            'create_' => 'ایجاد ',
            'edit_' => 'ویرایش ',
            'delete_' => 'حذف ',
            'export_' => 'استخراج ',
            'print_' => 'چاپ ',
            'approve_' => 'تایید ',
            'reject_' => 'رد ',
            'manage_' => 'مدیریت ',
            'sell_' => 'فروش ',
            'close_' => 'بستن ',
            'return_' => 'برگشت ',
            'reverse_' => 'برگشت ',
            're_saving_' => 'ثبت مجدد ',
            'deactivate_' => 'غیرفعال سازی ',
            'assign_' => 'تخصیص ',
            'run_' => 'اجرای ',
            'send_' => 'ارسال '
        ];

        $modules = [
            'production_dashboard' => 'داشبورد تولید',
            'inventory_dashboard' => 'داشبورد گدام',
            'finance_dashboard' => 'داشبورد مالی',
            'sales_dashboard' => 'داشبورد فروشات',
            'purchases_dashboard' => 'داشبورد خرید',
            'cost_analytics' => 'تحلیل مصارف',
            'coa' => 'لایحه حسابات (COA)',
            'journals' => 'روزنامچه ها',
            'journal' => 'روزنامچه',
            'mapping_rules' => 'تنظیمات محاسباتی',
            'currencies' => 'مدیریت اسعار (Forensic FX)',
            'currency' => 'ارز (Currency)',
            'pl_report' => 'راپور مفاد و ضرر',
            'balance_sheet' => 'ترازنامه',
            'trial_balance' => 'بیلان آزمایشی',
            'comparative_pl' => 'عملکرد مقایسوی',
            'cash_flow_report' => 'جریان وجوه نقد',
            'fx_exposure_report' => 'تحلیل اسعار',
            'purchased_carpets_report' => 'گزارش قالین‌های خریداری شده',
            'purchased_carpets_pdf' => 'گزارش قالین‌ها (PDF)',
            'purchased_carpets_excel' => 'گزارش قالین‌ها (Excel)',
            'inventory_valuation' => 'ارزش گدام',
            'cost_center_performance' => 'عملکرد دیپارتمنت',
            'audit_corrections' => 'تفتیش اصلاحات',
            'account_ledger' => 'دفتر تفصیلی حساب (Ledger)',
            'customer_statement' => 'صورت حساب مشتری',
            'agent_statement' => 'صورت حساب نماینده',
            'agent_statement_pdf' => 'پی دی اف صورت حساب نماینده',
            'agent_statement_excel' => 'اکسل صورت حساب نماینده',
            'employee_statement' => 'صورت حساب کارمند',
            'seller_statement' => 'صورت حساب فروشنده',
            'different_account_statement' => 'صورت حساب متفرقه',
            'different_account_statement_pdf' => 'پی دی اف صورت حساب متفرقه',
            'different_account_statement_excel' => 'اکسل صورت حساب متفرقه',
            'kachaee_statement' => 'صورت حساب کچایی',
            'kachaee_statement_pdf' => 'پی دی اف صورت حساب کچایی',
            'kachaee_statement_excel' => 'اکسل صورت حساب کچایی',
            'washing_statement' => 'صورت حساب شست',
            'washing_statement_pdf' => 'پی دی اف صورت حساب شست',
            'washing_statement_excel' => 'اکسل صورت حساب شست',
            'finishing_statement' => 'صورت حساب تیاری',
            'agents' => 'نماینده ها',
            'agent' => 'نماینده',
            'agent_accounts' => 'حسابات نماینده',
            'agent_carpets' => 'قالین های نماینده',
            'agent_money_requests' => 'درخواست پول نماینده',
            'cancel_agent_payment' => 'لغو پرداخت نماینده',
            'buy_carpets' => 'قالین های خریده شده',
            'buy_carpet' => 'قالین خریده شده',
            'purchase_bills' => 'بل های خرید',
            'purchase_bill' => 'بل خرید',
            'purchase_bill_pdf' => 'پی دی اف بل خرید',
            'carpet_types' => 'انواع قالین',
            'carpet_type' => 'نوع قالین',
            'carpet_qualities' => 'کوالتی های قالین',
            'carpet_quality' => 'کوالتی قالین',
            'approve_carpet_requests' => 'تایید درخواست های قالین',
            'material_purchases' => 'خریدهای مواد خام',
            'material_purchase' => 'خرید مواد خام',
            'raw_material_bills' => 'بل های خرید مواد',
            'raw_material_bill' => 'بل خرید مواد',
            'raw_material_bill_pdf' => 'پی دی اف بل خرید مواد',
            'raw_material_bill_excel' => 'اکسل بل خرید مواد',
            'purchase_material_requests' => 'درخواست های خرید مواد',
            'material_sales' => 'فروشات مواد خام',
            'material_sale' => 'فروش مواد خام',
            'material_sale_requests' => 'درخواست های فروش مواد',
            'approve_material_account_requests' => 'تایید درخواست پول حسابات اجناس',
            'material_stock' => 'گدام مواد خام',
            'string_sellers' => 'فروشندگان تار',
            'string_seller' => 'فروشنده تار',
            'seller_payments' => 'پرداختی های فروشنده',
            'cancel_seller_payment' => 'لغو پرداخت فروشنده',
            'seller_money_requests' => 'درخواست پول فروشنده',
            'material_categories' => 'کتگوری های مواد',
            'material_category' => 'کتگوری مواد',
            'material_types' => 'انواع مواد',
            'material_type' => 'نوع مواد',
            'different_accounts' => 'حسابات متفرقه',
            'different_account' => 'حساب متفرقه',
            'different_account_payments' => 'پرداختی های حساب متفرقه',
            'edit_approved_different_account_payments' => 'ویرایش پرداخت‌های تایید شده متفرقه',
            'different_account_money_requests' => 'درخواست پول متفرقه',
            'carpet_repairs' => 'کچای قالین ها',
            'carpet_repair' => 'کچای قالین',
            'carpet_from_repair' => 'قالین از کچایی',
            'kachaee_teams' => 'تیم های کچایی',
            'kachaee_team' => 'تیم کچایی',
            'kachaee_payments' => 'پرداختی های کچایی',
            'cancel_kachaee_payment' => 'لغو پرداخت کچایی',
            'kachaee_team_statement' => 'صورت حساب تیم کچایی',
            'kachaee_batches' => 'نمبرهای کچایی',
            'kachaee_batch' => 'نمبر کچایی',
            'kachaee_batch_status' => 'وضعیت نمبر کچایی',
            'kachaee_batches_pdf' => 'پی دی اف نمبرهای کچایی',
            'kachaee_batches_excel' => 'اکسل نمبرهای کچایی',
            'kachaee_money_requests' => 'درخواست پول کچایی',
            'carpet_washes' => 'شست قالین ها',
            'carpet_wash' => 'شست قالین',
            'carpet_from_wash' => 'قالین از شست',
            'washing_teams' => 'تیم های شست',
            'washing_team' => 'تیم شست',
            'washing_payments' => 'پرداختی های شست',
            'cancel_washing_payment' => 'لغو پرداخت شست',
            'washing_team_statement' => 'صورت حساب تیم شست',
            'washing_batches' => 'نمبرهای شست',
            'washing_batch' => 'نمبر شست',
            'washing_batch_status' => 'وضعیت نمبر شست',
            'washing_batches_pdf' => 'پی دی اف نمبرهای شست',
            'washing_batches_excel' => 'اکسل نمبرهای شست',
            'washing_money_requests' => 'درخواست پول شست',
            'finishing_centers' => 'بخش های تیاری',
            'finishing_work' => 'کار تیاری',
            'the_work' => 'کار',
            'finishing_teams' => 'تیم های تیاری',
            'finishing_team' => 'تیم تیاری',
            'finishing_payments' => 'پرداختی های تیاری',
            'cancel_finishing_payment' => 'لغو پرداخت تیاری',
            'return_carpet_from_finishing' => 'برگشت قالین از تیاری',
            'finishing_team_statement' => 'صورت حساب تیم تیاری',
            'finishing_batches' => 'نمبرهای تیاری',
            'finishing_batch' => 'نمبر تیاری',
            'finishing_batch_status' => 'وضعیت نمبر تیاری',
            'finishing_batches_pdf' => 'پی دی اف نمبرهای تیاری',
            'finishing_batches_excel' => 'اکسل نمبرهای تیاری',
            'refinish_requests' => 'درخواست های دوباره تیاری',
            'finishing_money_requests' => 'درخواست پول تیاری',
            'carpet_stock' => 'قالین آماده فروش',
            'carpet_stock_details' => 'جزییات قالین آماده فروش',
            'carpet_from_stock' => 'قالین از گدام',
            'sales' => 'فروشات',
            'sale' => 'فروش',
            'edit_sale' => 'ویرایش فروش',
            'delete_sale' => 'حذف فروش',
            'invoices' => 'انوایس ها',
            'invoice' => 'انوایس',
            'customer_orders' => 'سفارشات مشتری',
            'customer_order' => 'سفارش مشتری',
            'customer_order_details' => 'جزییات سفارش مشتری',
            'receive_customer_order_alerts' => 'دریافت هشدارهای سفارش مشتری',
            'assets_accounts' => 'حسابات اجناس',
            'assets_account' => 'حساب اجناس',
            'assets_report' => 'گزارش اجناس ثابت',
            'assets_report_pdf' => 'پی دی اف گزارش اجناس',
            'assets_report_excel' => 'اکسل گزارش اجناس',
            'warehouse_inventory_report' => 'گزارش موجودی گدام',
            'warehouses' => 'گدام ها',
            'warehouse' => 'گدام',
            'warehouse_in_out_report' => 'گزارش ورود و خروج گدام',
            'warehouse_available_stock' => 'موجودی در دسترس گدام',
            'inventory_transfers' => 'انتقالات گدام',
            'inventory_transfer' => 'انتقال گدام',
            'warehouse_movements' => 'ورودی و خروجی گدام',
            'warehouse_movements_pdf' => 'پی دی اف ورودی خروجی گدام',
            'warehouse_movements_excel' => 'اکسل ورودی خروجی گدام',
            'customers' => 'مشتری ها',
            'customer' => 'مشتری',
            'customer_payments' => 'پرداختی های مشتری',
            'cancel_customer_payment' => 'لغو پرداخت مشتری',
            'customer_statement_pdf' => 'پی دی اف صورت حساب مشتری',
            'customer_statement_excel' => 'اکسل صورت حساب مشتری',
            'customer_money_requests' => 'درخواست پول مشتری',
            'ar_aging_report' => 'گزارش تحلیل بدهی',
            'monthly_expenses' => 'مصارف ماهانه',
            'monthly_expense' => 'مصرف ماهانه',
            'monthly_expense_payments' => 'پرداختی مصارف ماهانه',
            'employees' => 'کارمندان',
            'employee' => 'کارمند',
            'employee_payments' => 'پرداختی کارمند',
            'payroll' => 'معاشات',
            'payroll_slip' => 'بل معاش',
            'employee_money_requests' => 'درخواست پول کارمند',
            'employee_departments' => 'دیپارتمنت های کارمندان',
            'employee_department' => 'دیپارتمنت کارمند',
            'users' => 'کاربران',
            'user' => 'کاربر',
            'roles' => 'نقش ها',
            'roles_and_permissions' => 'نقش ها و دسترسی ها',
            'phone_book' => 'دفترچه تلفون',
            'provinces' => 'ولایات',
            'province' => 'ولایت',
            'agent_employees' => 'کارگرهای نماینده',
            'agent_employee' => 'کارگر نماینده',
            'activities' => 'فعالیت ها',
            'carpet_to_kachaee' => 'قالین به کچایی',
            'carpet_to_washing' => 'قالین به شست',
            'carpet_to_finishing' => 'قالین به تیاری'
        ];

        $dari = $name;
        
        if (isset($modules[$name])) {
            return $modules[$name];
        }

        foreach ($prefixes as $engPrefix => $dariPrefix) {
            if (strpos($name, $engPrefix) === 0) {
                $modulePart = substr($name, strlen($engPrefix));
                $dariModule = isset($modules[$modulePart]) ? $modules[$modulePart] : $modulePart;
                return $dariPrefix . $dariModule;
            }
        }

        return ucwords(str_replace('_', ' ', $name));
    }
}
