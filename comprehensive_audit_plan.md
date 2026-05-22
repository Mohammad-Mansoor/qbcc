# Comprehensive ERP Integration & Audit Plan (Exhaustive)

This document is the source of truth for the 100% verification of the QBCC Forensic ERP. We will verify every single item from the sidebar one by one.

## Verification Criteria (The 5 Pillars)
1.  **Finance**: Automatic transaction triggering in the General Ledger.
2.  **Accounting**: Accurate Double-Entry (Debit/Credit) mapping.
3.  **Inventory**: Real-time stock movement and WAC valuation.
4.  **Warehouse**: Location tracking and transfers.
5.  **Multi-Currency**: Forensic FX snapshots (AFN/USD/EUR/PKR) and USD normalization.

---

## 🛠 Exhaustive Module Checklist

### 1. Agents (نماینده)
- [ ] 1.1 لیست نماینده ها (Agent List)
- [ ] 1.2 لیست درخواست های پول (Agent Money Request List)

### 2. Miscellaneous Accounts (حساب متفرقه)
- [ ] 2.1 حساب متفرقه (Different Account)
- [ ] 2.2 لیست درخواست های پول (Different Account Money Request List)

### 3. Carpets (قالین)
- [ ] 3.1 قالین های قراردادی (Contract Carpets)
- [ ] 3.2 قالین های وزنی (Weight Carpets)
- [ ] 3.3 قالین های خرید شده (Purchased Carpets)
- [ ] 3.4 چک بٌک شده ها (Checked/Booked Carpets)

### 4. Kachaee - Repair (کچایی)
- [x] 4.1 کچای قالین ها (Carpet Repair)
- [x] 4.2 تیم کچایی (Kachaee Team)
- [x] 4.3 لیست درخواست های پول (Kachaee Money Request List)

### 5. Washing (شست)
- [x] 5.1 شست قالین ها (Carpet Wash)
- [x] 5.2 تیم شست (Washing Team)
- [x] 5.3 لیست درخواست های پول (Washing Money Request List)

### 6. Finishing (تیاری)
- [x] 6.1 بخش های تیاری (Finishing Center)
- [x] 6.2 تیم تیاری (Finishing Team)
- [x] 6.3 لیست درخواست های دوباره تیاری (Refinish Request List)
- [x] 6.4 لیست درخواست های پول (Finishing Money Request List)

### 7. Carpet Stock (گدام قالین)
- [x] 7.1 گدام قالین آماده به فروش (Ready Carpet Stock)

### 8. Sales (فروشات)
- [x] 8.1 لیست فروشات (Sales List)
- [x] 8.2 پکینگ لیست (Packing List)
- [x] 8.3 لیست انوایس ها (Invoices)

### 9. Cash Book (دخل و خرچ پول)
- [ ] 9.1 اضافه کردن پول به دخل (Add Office Credit)
- [ ] 9.2 لیست درخواست های پول (Office Money Request List)
- [ ] 9.3 **مصارف (Office Cash Book / Expenses) - HARDENED**

### 10. Monthly Expenses (مصارف ماهانه)
- [ ] 10.1 مصارف ماهانه (Monthly Expense Accounts)

### 11. Orders (سفارشات)
- [ ] 11.1 سفارشات (Customer Account for Orders)

### 12. Fixed Assets (اجناس ثابت شرکت)
- [ ] 12.1 اجناس ثابت شرکت (Assets Accounts)

### 13. Thread / Material (تار)
- [ ] 13.1 **خرید تار (Material Purchase) - HARDENED**
- [ ] 13.2 لیست درخواست خرید تار (Purchase Material Request List)
- [ ] 13.3 **فروش تار (Material Sales) - HARDENED**
- [ ] 13.4 لیست درخواست فروش تار (Material Sale Request List)
- [ ] 13.5 گدام تار (Material Stock)
- [ ] 13.6 فروشنده تار (String Seller)
- [ ] 13.7 لیست درخواست پول فروشنده تار (String Seller Request List)
- [ ] 13.8 نوعیت مواد (Material Types)
- [ ] 13.9 دخل و خرچ تار (Material Accounts)
- [ ] 13.10 لیست درخواست اکونت های تار (Material Account Request List)

### 14. Office Employees (کارمندان دفتر)
- [ ] 14.1 لیست کارمندان (Office Employees)
- [ ] 14.2 لیست درخواست پول کارمندان (Employee Request List)
- [ ] 14.3 دیپارتمنت کارمندان (Employee Department)

### 15. Customers (مشتری ها)
- [ ] 15.1 لیست مشتریان (Customers List)
- [ ] 15.2 لیست درخواست پول مشتریان (Customer Request List)

### 16. Accounting System (سیستم حسابداری)
- [ ] 16.1 لایحه حسابات (COA)
- [ ] 16.2 روزنامچه عمومی (GL)
- [ ] 16.3 مدیریت گدام‌ها (Locations)
- [ ] 16.4 تنظیمات محاسباتی (Mappings)
- [ ] 16.5 مدیریت اسعار (Currencies - Forensic FX)

### 17. Financial Reports (گزارشات مالی و تحلیلی)
- [ ] 17.1 مفاد و ضرر (P&L)
- [ ] 17.2 ترازنامه (Balance Sheet)
- [ ] 17.3 تحلیل مقایسوی عملکرد (Comparative Analysis)
- [ ] 17.4 جریان وجوه نقد (Cash Flow)
- [ ] 17.5 تحلیل اسعار و نقدینگی (FX Exposure)

### 18. Operational & Audit Reports (گزارشات عملیاتی و تفتیش)
- [ ] 18.1 ارزش پولی موجودی گدام (Inventory Valuation)
- [ ] 18.2 عملکرد دیپارتمنت‌ها (Cost Center Performance)
- [ ] 18.3 تفتیش اصلاحات و ریورس (Audit Corrections)
- [ ] 18.4 دفتر تفصیلی حساب (Account Ledger)
- [ ] 18.5 صورت حساب مشتری (Customer Statement)

### 19. Legacy Reports (گزارشات عمومی)
- [ ] 19.1 صورت حساب نماینده ها (Agent Balance Report)
- [ ] 19.2 صورت حساب متفرقه جدید (Different Account Balance Report)
- [ ] 19.3 صورت حساب تیم کچایی (Kachaee Team Balance Report)
- [ ] 19.4 صورت حساب تیم شست (Washing Team Balance Report)
- [ ] 19.5 صورت حساب تیم تیاری (Finishing Team Balance Report)
- [ ] 19.6 صورت حساب تار فروش ها (String Seller Balance Report)
- [ ] 19.7 صورت حساب مشتری ها (Customer Balance Report)
- [ ] 19.8 گزارش مصارف (Expense Report)
- [ ] 19.9 گزارش خرید قالین (Purchase Carpet Report)
- [ ] 19.10 گزارش فروشات (Sales Report)
- [ ] 19.11 **بیلان آزمایشی (Trial Balance) - HARDENED**
- [ ] 19.12 **صورت سود و زیان (Income Statement) - HARDENED**
- [ ] 19.13 تحلیل بدهی مشتریان (AR Aging)
- [ ] 19.14 گزارش موجودی گدام (Inventory Report ERP)
- [ ] 19.15 گزارش سرمایه در حال کار (WIP Report)

### 20. Settings (تنظیمات)
- [ ] 20.1 کاربران سیستم (Users)
- [ ] 20.2 دفترچه تلفون (Phone Book)
- [ ] 20.3 نوعیت قالین (Carpet Types)
- [ ] 20.4 کوالتی قالین (Carpet Qualities)
- [ ] 20.5 ولایات (Provinces)
- [ ] 20.6 کارگرها (Agent Employees)
- [ ] 20.7 شماره فرمایش (Carpet Orders)
- [ ] 20.8 نمایش فعالیت ها (Activities)

---

## 🚦 Execution Protocol
We will work through this list in order. For each item:
1.  **Code Analysis**: I will verify the underlying logic for the 5 Pillars.
2.  **Report**: I will confirm if the item is 100% integrated or needs work.
3.  **Approval**: You will confirm before we move to the next item.

**Current Task**: Initial Analysis of **1.1 Agent List & 1.2 Agent Money Requests**.
