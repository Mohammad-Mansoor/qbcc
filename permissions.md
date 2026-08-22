# Detailed Permissions Mapping

This document maps every module and link found in `master.blade.php` to the specific granular permissions needed in the proposed Spatie RBAC system. 
Permissions generally follow the standard `action_module` naming convention (e.g., `view_agents`, `create_agents`).

## 1. Dashboards
| Sidebar Link / Feature | Permission Required | Notes |
| :--- | :--- | :--- |
| داشبورد تولید (Production) | `view_production_dashboard` | |
| داشبورد گدام (Inventory) | `view_inventory_dashboard` | |
| داشبورد مالی (Finance) | `view_finance_dashboard` | |
| داشبورد فروشات (Sales) | `view_sales_dashboard` | |
| داشبورد خرید (Purchases) | `view_purchases_dashboard` | |
| تحلیل مصارف (Cost Analytics) | `view_cost_analytics` | |

## 2. Accounting System (سیستم حسابداری)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| لایحه حسابات (COA) | `view_coa`, `create_coa`, `edit_coa` |
| روزنامچه عمومی (GL) | `view_journals`, `create_journal`, `reverse_journal`, `print_journal` |
| تنظیمات محاسباتی | `view_mapping_rules`, `edit_mapping_rules` |
| مدیریت اسعار (Forensic FX) | `view_currencies`, `create_currency`, `edit_currency`, `delete_currency` |
| **Financial Reports (گزارشات مالی)** | |
| مفاد و ضرر (P&L) | `view_pl_report` |
| ترازنامه (بیلانس شیت) | `view_balance_sheet` |
| بیلان آزمایشی (Trial Balance) | `view_trial_balance` |
| تحلیل مقایسوی عملکرد | `view_comparative_pl` |
| جریان وجوه نقد (Cash Flow) | `view_cash_flow_report` |
| تحلیل اسعار و نقدینگی | `view_fx_exposure_report` |
| **Operational & Audit Reports** | |
| ارزش پولی موجودی گدام | `view_inventory_valuation` |
| عملکرد دیپارتمنت‌ها | `view_cost_center_performance` |
| تفتیش اصلاحات و ریورس | `view_audit_corrections` |
| دفتر تفصیلی حساب | `view_account_ledger` |
| صورت حساب ها (All Entities) | `view_customer_statement`, `view_agent_statement`, `view_employee_statement`, `view_seller_statement`, `view_different_account_statement`, `view_kachaee_statement`, `view_washing_statement`, `view_finishing_statement` |

## 3. Agents (نماینده)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| لیست نماینده ها | `view_agents`, `create_agent`, `edit_agent`, `delete_agent`, `manage_agent_accounts`, `deactivate_agent`, `view_agent_carpets` |
| صورت حساب نماینده | `view_agent_statement` |
| درخواست های پول نماینده | `view_agent_money_requests`, `approve_agent_money_requests`, `delete_agent_money_requests` |

## 4. Carpets (قالین)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| گزارش قالین های خرید شده | `view_purchased_carpets_report`, `export_purchased_carpets_pdf`, `export_purchased_carpets_excel` |
| لیست قالین های خرید شده | `view_buy_carpets`, `create_buy_carpet`, `edit_buy_carpet`, `print_buy_carpet` |
| بل‌های خرید (Purchase Bills) | `view_purchase_bills`, `create_purchase_bill`, `print_purchase_bill_pdf`, `close_purchase_bill` |
| نوعیت قالین (Types) | `view_carpet_types`, `create_carpet_type`, `edit_carpet_type`, `delete_carpet_type` |
| کوالتی قالین (Qualities) | `view_carpet_qualities`, `create_carpet_quality`, `edit_carpet_quality`, `delete_carpet_quality` |

## 5. Raw Materials (مواد خام)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| خرید مواد خام | `view_material_purchases`, `create_material_purchase`, `edit_material_purchase`, `delete_material_purchase` |
| بل‌های خرید مواد خام | `view_raw_material_bills`, `create_raw_material_bill`, `edit_raw_material_bill`, `close_raw_material_bill`, `delete_raw_material_bill`, `print_raw_material_bill_pdf`, `export_raw_material_bill_excel` |
| درخواست خرید مواد خام | `view_purchase_material_requests`, `approve_purchase_material_requests`, `reject_purchase_material_requests` |
| فروش مواد خام | `view_material_sales`, `create_material_sale`, `edit_material_sale` |
| درخواست فروش مواد خام | `view_material_sale_requests`, `approve_material_sale_requests`, `reject_material_sale_requests` |
| گدام مواد خام (Stock) | `view_material_stock`, `export_material_stock` |
| فروشنده مواد خام (Sellers) | `view_string_sellers`, `create_string_seller`, `edit_string_seller`, `manage_seller_payments` |
| صورت حساب فروشنده | `view_seller_statement` |
| درخواست پول فروشنده | `view_seller_money_requests`, `approve_seller_money_requests`, `reject_seller_money_requests` |
| کتگوری مواد | `view_material_categories`, `create_material_category`, `edit_material_category`, `delete_material_category` |
| نوعیت مواد | `view_material_types`, `create_material_type`, `edit_material_type`, `delete_material_type` |

## 6. Miscellaneous Accounts (حساب متفرقه)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| لیست حساب ها | `view_different_accounts`, `create_different_account`, `edit_different_account`, `delete_different_account`, `manage_different_account_payments` |
| صورت حساب متفرقه | `view_different_account_statement` |
| درخواست های پول | `view_different_account_money_requests`, `approve_different_account_money_requests`, `reject_different_account_money_requests` |

## 7. Kachaee / Repair (کچایی)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| کچای قالین ها | `view_carpet_repairs`, `create_carpet_repair`, `edit_carpet_repair`, `return_carpet_from_repair` |
| تیم کچایی | `view_kachaee_teams`, `create_kachaee_team`, `edit_kachaee_team`, `manage_kachaee_payments` |
| صورت حساب کچایی | `view_kachaee_team_statement` |
| نمبرهای کچایی (KCH) | `view_kachaee_batches`, `create_kachaee_batch`, `manage_kachaee_batch_status`, `print_kachaee_batches`, `export_kachaee_batches_pdf`, `export_kachaee_batches_excel` |
| درخواست های پول | `view_kachaee_money_requests`, `approve_kachaee_money_requests`, `reject_kachaee_money_requests` |

## 8. Washing (شست)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| شست قالین ها | `view_carpet_washes`, `create_carpet_wash`, `edit_carpet_wash`, `return_carpet_from_wash` |
| تیم شست | `view_washing_teams`, `create_washing_team`, `edit_washing_team`, `manage_washing_payments` |
| صورت حساب شست | `view_washing_team_statement` |
| نمبرهای شست (Wash) | `view_washing_batches`, `create_washing_batch`, `manage_washing_batch_status`, `print_washing_batches`, `export_washing_batches_pdf`, `export_washing_batches_excel` |
| درخواست های پول | `view_washing_money_requests`, `approve_washing_money_requests`, `reject_washing_money_requests` |

## 9. Finishing (تیاری)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| بخش های تیاری | `view_finishing_centers`, `create_finishing_work`, `re_saving_the_work` |
| تیم تیاری | `view_finishing_teams`, `create_finishing_team`, `edit_finishing_team`, `manage_finishing_payments` |
| صورت حساب تیاری | `view_finishing_team_statement` |
| نمبرهای تیاری (TA) | `view_finishing_batches`, `create_finishing_batch`, `manage_finishing_batch_status`, `print_finishing_batches`, `export_finishing_batches_pdf`, `export_finishing_batches_excel` |
| درخواست های دوباره تیاری | `view_refinish_requests`, `approve_refinish_requests`, `reject_refinish_requests` |
| درخواست های پول | `view_finishing_money_requests`, `approve_finishing_money_requests`, `reject_finishing_money_requests` |

## 10. Carpet Stock & Sales (قالین آماده به فروش و فروشات)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| قالین آماده به فروش | `view_carpet_stock`, `view_carpet_stock_details`, `sell_carpet_from_stock` |
| لیست فروشات | `view_sales`, `create_sale` |
| لیست انوایس ها | `view_invoices`, `create_invoice`, `edit_invoice`, `close_invoice`, `print_invoice` |

## 11. Customer Orders (سفارشات)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| لیست سفارشات | `view_customer_orders`, `create_customer_order`, `edit_customer_order`, `delete_customer_order`, `manage_customer_order_details`, `edit_customer_order_details`, `delete_customer_order_details` |

## 12. Assets (اجناس ثابت شرکت)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| حسابات اجناس | `view_assets_accounts`, `create_assets_account`, `edit_assets_account`, `delete_assets_account`, `manage_assets_account`, `edit_assets_account_details`, `delete_assets_account_details` |
| گزارش اجناس ثابت | `view_assets_report`, `export_assets_report_pdf`, `export_assets_report_excel` |

## 13. Warehouse Management (مدیریت گدام‌ها)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| گزارش موجودی گدام (ERP) | `view_warehouse_inventory_report` |
| مدیریت گدام‌ها (Locations) | `view_warehouses`, `create_warehouse`, `edit_warehouse`, `delete_warehouse`, `view_warehouse_in_out_report`, `view_warehouse_available_stock` |
| انتقال جنس بین گدام‌ها | `view_inventory_transfers`, `create_inventory_transfer`, `reverse_inventory_transfer` |
| گزارش ورودی و خروجی | `view_warehouse_movements`, `export_warehouse_movements_pdf`, `export_warehouse_movements_excel` |

## 14. Customers (مشتری ها)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| لیست مشتریان | `view_customers`, `create_customer`, `edit_customer`, `manage_customer_payments` |
| صورت حساب مشتری | `view_customer_statement`, `export_customer_statement_pdf`, `export_customer_statement_excel` |
| درخواست پول مشتریان | `view_customer_money_requests`, `approve_customer_money_requests`, `reject_customer_money_requests` |
| تحلیل بدهی مشتریان (Aging) | `view_ar_aging_report` |

## 15. Office Employees & Expenses (کارمندان دفتر و مصارف)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| مصارف ماهانه | `view_monthly_expenses`, `create_monthly_expense`, `manage_monthly_expense_payments` |
| لیست کارمندان | `view_employees`, `create_employee`, `edit_employee`, `manage_employee_payments` |
| صورت حساب کارمندان | `view_employee_statement` |
| اجرای معاشات (Payroll) | `view_payroll`, `run_payroll`, `view_payroll_slip` |
| درخواست پول کارمندان | `view_employee_money_requests`, `approve_employee_money_requests`, `reject_employee_money_requests` |
| دیپارتمنت کارمندان | `view_employee_departments`, `create_employee_department`, `edit_employee_department` |

## 16. Settings & General Admin (تنظیمات)
| Sidebar Link / Feature | Permissions Needed |
| :--- | :--- |
| کاربران سیستم (Users) | `view_users`, `create_user`, `edit_user`, `delete_user`, `assign_roles` |
| دفترچه تلفون (Phone Book) | `view_phone_book`, `create_phone_book` |
| ولایات (Provinces) | `view_provinces`, `create_province`, `edit_province` |
| کارگرها (Agent Employees) | `view_agent_employees`, `create_agent_employee`, `edit_agent_employee` |
| نمایش فعالیت ها (Audit Logs)| `view_activities`, `delete_activities` |
| **New Module:** Roles & Permissions| `manage_roles_and_permissions` (Super Admin Only) |
