<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(30);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        $permissionGroups = $this->getPermissionGroups($permissions);
        return view('roles.create', compact('permissionGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        $this->logActivity("نقش جدید به نام " . $role->name . " ایجاد شد");

        return redirect()->route('roles.index')->with('status', 'نقش موفقانه ثبت شد!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $permissionGroups = $this->getPermissionGroups($permissions);
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $id,
            'permission' => 'required',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        $this->logActivity("نقش " . $role->name . " ویرایش شد");

        return redirect()->route('roles.index')->with('status', 'نقش موفقانه ویرایش شد!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'Super Admin') {
            return response()->json(['status' => 'error', 'message' => 'سوپر ادمین قابل حذف نیست.']);
        }
        
        $roleName = $role->name;
        $role->delete();
        
        $this->logActivity("نقش " . $roleName . " حذف شد");
        
        return response()->json(['status' => 'success']);
    }

    private function getPermissionGroups($permissions)
    {
        $groups = [
            'dashboards' => ['name' => 'داشبورد ها', 'perms' => []],
            'accounting' => ['name' => 'حسابداری و گزارشات', 'perms' => []],
            'agents' => ['name' => 'نماینده ها', 'perms' => []],
            'carpets' => ['name' => 'قالین', 'perms' => []],
            'materials' => ['name' => 'مواد خام', 'perms' => []],
            'different_accounts' => ['name' => 'حساب متفرقه', 'perms' => []],
            'kachaee' => ['name' => 'کچایی', 'perms' => []],
            'washing' => ['name' => 'شست', 'perms' => []],
            'finishing' => ['name' => 'تیاری', 'perms' => []],
            'sales' => ['name' => 'فروشات و گدام قالین', 'perms' => []],
            'customer_orders' => ['name' => 'سفارشات مشتری', 'perms' => []],
            'assets' => ['name' => 'اجناس ثابت', 'perms' => []],
            'warehouse' => ['name' => 'مدیریت گدام‌ها', 'perms' => []],
            'customers' => ['name' => 'مشتری ها', 'perms' => []],
            'employees' => ['name' => 'کارمندان', 'perms' => []],
            'expenses' => ['name' => 'مصارف', 'perms' => []],
            'settings' => ['name' => 'تنظیمات', 'perms' => []]
        ];

        foreach ($permissions as $p) {
            $p->dari_name = $this->translatePermission($p->name);

            if (strpos($p->name, 'dashboard') !== false || strpos($p->name, 'cost_analytics') !== false) {
                $groups['dashboards']['perms'][] = $p;
            } elseif (strpos($p->name, 'coa') !== false || strpos($p->name, 'journal') !== false || strpos($p->name, 'mapping') !== false || strpos($p->name, 'currenc') !== false || strpos($p->name, '_report') !== false || strpos($p->name, '_statement') !== false || strpos($p->name, 'ledger') !== false || strpos($p->name, 'audit') !== false || strpos($p->name, 'balance') !== false || strpos($p->name, 'pl_report') !== false || strpos($p->name, 'comparative_pl') !== false || strpos($p->name, 'valuation') !== false || strpos($p->name, 'performance') !== false) {
                // Statements are shared, let's put them in accounting or specific modules. We'll let them fall here if they have statement or report unless they are specific.
                // Actually, let's refine this to be more targeted if needed, but this works for now.
                // Let's refine based on words
                if(strpos($p->name, 'purchased_carpets_report') !== false) $groups['carpets']['perms'][] = $p;
                elseif(strpos($p->name, 'agent_statement') !== false) $groups['agents']['perms'][] = $p;
                elseif(strpos($p->name, 'seller_statement') !== false) $groups['materials']['perms'][] = $p;
                elseif(strpos($p->name, 'different_account_statement') !== false) $groups['different_accounts']['perms'][] = $p;
                elseif(strpos($p->name, 'kachaee') !== false && strpos($p->name, 'statement') !== false) $groups['kachaee']['perms'][] = $p;
                elseif(strpos($p->name, 'washing') !== false && strpos($p->name, 'statement') !== false) $groups['washing']['perms'][] = $p;
                elseif(strpos($p->name, 'finishing') !== false && strpos($p->name, 'statement') !== false) $groups['finishing']['perms'][] = $p;
                elseif(strpos($p->name, 'customer_statement') !== false || strpos($p->name, 'aging_report') !== false) $groups['customers']['perms'][] = $p;
                elseif(strpos($p->name, 'employee_statement') !== false) $groups['employees']['perms'][] = $p;
                elseif(strpos($p->name, 'assets_report') !== false) $groups['assets']['perms'][] = $p;
                elseif(strpos($p->name, 'warehouse_inventory_report') !== false || strpos($p->name, 'warehouse_movements') !== false || strpos($p->name, 'warehouse_in_out') !== false || strpos($p->name, 'available_stock') !== false) $groups['warehouse']['perms'][] = $p;
                else $groups['accounting']['perms'][] = $p;
            } elseif (strpos($p->name, 'agent') !== false && strpos($p->name, 'employee') === false) {
                $groups['agents']['perms'][] = $p;
            } elseif (strpos($p->name, 'carpet_type') !== false || strpos($p->name, 'carpet_qualit') !== false || strpos($p->name, 'buy_carpet') !== false || strpos($p->name, 'purchase_bill') !== false || strpos($p->name, 'purchased_carpets') !== false) {
                $groups['carpets']['perms'][] = $p;
            } elseif (strpos($p->name, 'material') !== false || strpos($p->name, 'seller') !== false) {
                $groups['materials']['perms'][] = $p;
            } elseif (strpos($p->name, 'different_account') !== false) {
                $groups['different_accounts']['perms'][] = $p;
            } elseif (strpos($p->name, 'kachaee') !== false || strpos($p->name, 'repair') !== false) {
                $groups['kachaee']['perms'][] = $p;
            } elseif (strpos($p->name, 'wash') !== false) {
                $groups['washing']['perms'][] = $p;
            } elseif (strpos($p->name, 'finish') !== false || strpos($p->name, 're_saving_the_work') !== false) {
                $groups['finishing']['perms'][] = $p;
            } elseif (strpos($p->name, 'sale') !== false || strpos($p->name, 'sell') !== false || strpos($p->name, 'invoice') !== false || strpos($p->name, 'carpet_stock') !== false) {
                $groups['sales']['perms'][] = $p;
            } elseif (strpos($p->name, 'customer_order') !== false) {
                $groups['customer_orders']['perms'][] = $p;
            } elseif (strpos($p->name, 'assets') !== false) {
                $groups['assets']['perms'][] = $p;
            } elseif (strpos($p->name, 'warehouse') !== false || strpos($p->name, 'inventory_transfer') !== false) {
                $groups['warehouse']['perms'][] = $p;
            } elseif (strpos($p->name, 'customer') !== false) {
                $groups['customers']['perms'][] = $p;
            } elseif (strpos($p->name, 'employee') !== false || strpos($p->name, 'payroll') !== false) {
                $groups['employees']['perms'][] = $p;
            } elseif (strpos($p->name, 'expense') !== false) {
                $groups['expenses']['perms'][] = $p;
            } else {
                $groups['settings']['perms'][] = $p;
            }
        }

        return $groups;
    }

    private function logActivity($description)
    {
        $activity = new Activity();
        $activity->date = Carbon::today()->format('Y-m-d');
        $activity->description = $description;
        if(Auth::check()) {
            $activity->user_id = Auth::user()->id;
            $activity->save();
        }
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
            'purchased_carpets_report' => 'گزارش قالین های خرید شده',
            'purchased_carpets_pdf' => 'پی دی اف قالین های خرید شده',
            'purchased_carpets_excel' => 'اکسل قالین های خرید شده',
            'buy_carpets' => 'قالین های خریده شده',
            'buy_carpet' => 'قالین خریده شده',
            'purchase_bills' => 'بل های خرید',
            'purchase_bill' => 'بل خرید',
            'purchase_bill_pdf' => 'پی دی اف بل خرید',
            'carpet_types' => 'انواع قالین',
            'carpet_type' => 'نوع قالین',
            'carpet_qualities' => 'کوالتی های قالین',
            'carpet_quality' => 'کوالتی قالین',
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
            'material_stock' => 'گدام مواد خام',
            'string_sellers' => 'فروشندگان تار',
            'string_seller' => 'فروشنده تار',
            'seller_payments' => 'پرداختی های فروشنده',
            'seller_money_requests' => 'درخواست پول فروشنده',
            'material_categories' => 'کتگوری های مواد',
            'material_category' => 'کتگوری مواد',
            'material_types' => 'انواع مواد',
            'material_type' => 'نوع مواد',
            'different_accounts' => 'حسابات متفرقه',
            'different_account' => 'حساب متفرقه',
            'different_account_payments' => 'پرداختی های حساب متفرقه',
            'different_account_money_requests' => 'درخواست پول متفرقه',
            'carpet_repairs' => 'کچای قالین ها',
            'carpet_repair' => 'کچای قالین',
            'carpet_from_repair' => 'قالین از کچایی',
            'kachaee_teams' => 'تیم های کچایی',
            'kachaee_team' => 'تیم کچایی',
            'kachaee_payments' => 'پرداختی های کچایی',
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
        
        // Exact match check first
        if (isset($modules[$name])) {
            return $modules[$name];
        }

        // Prefix + Module replacement
        foreach ($prefixes as $engPrefix => $dariPrefix) {
            if (strpos($name, $engPrefix) === 0) {
                $modulePart = substr($name, strlen($engPrefix));
                $dariModule = isset($modules[$modulePart]) ? $modules[$modulePart] : $modulePart;
                return $dariPrefix . $dariModule;
            }
        }

        return $dari;
    }
}
