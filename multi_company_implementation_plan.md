# Dynamic Multi-Company Branding Implementation

This implementation plan outlines how we will update the single codebase to dynamically support multiple companies using `.env` configurations.

## User Review Required

> [!IMPORTANT]
> Please review this plan to ensure the approach aligns with your expectations. If approved, I will begin implementing these changes.

## Open Questions

> [!TIP]
> - Do you want me to update the `.env.example` file to include these new variables so that future developers know they exist?
> - For the companies other than Qasemi Brothers, the text below the logo (company name and description) will still be displayed in the reports, but dynamically loaded. Is this correct?

## Proposed Changes

### Configuration

#### [NEW] config/company.php
We will create a new configuration file to read from the `.env` file. This allows us to safely fallback to Qasemi Brothers if a variable is missing.
- Maps `COMPANY_NAME` to `config('company.name')`
- Maps `COMPANY_DESC` to `config('company.description')`
- Maps `ERP_NAME` to `config('company.erp_name')`
- Maps `COMPANY_HEADER_PATH` to `config('company.header_path')`
- Maps `COMPANY_FOOTER_PATH` to `config('company.footer_path')`
- Maps `COMPANY_LOGO_PATH` to `config('company.logo_path')`

---

### Controllers

We will update the controllers that generate PDFs and pass images to views. They will be updated to fetch the paths from `config('company.*')` rather than hardcoded `images/header.png`.
They will also pass a new variable `$logoBase64` to the views.

#### [MODIFY] app/Http/Controllers/RawMaterialPurchaseBillController.php
#### [MODIFY] app/Http/Controllers/InvoiceController.php
#### [MODIFY] app/Http/Controllers/Accounting/ReportController.php
#### [MODIFY] app/Http/Controllers/Accounting/EntityStatementController.php
#### [MODIFY] app/Http/Controllers/Accounting/WarehouseController.php
#### [MODIFY] app/Http/Controllers/Accounting/WarehouseReportController.php
#### [MODIFY] app/Http/Controllers/Accounting/JournalController.php
#### [MODIFY] app/Http/Controllers/ProductionBatchController.php
#### [MODIFY] app/Http/Controllers/AssetsReportController.php
#### [MODIFY] app/Http/Controllers/AccountingReportController.php

---

### Views (Blade Templates)

We will search for all instances of "شرکت صنعتی برادران قاسمی" (and variations) and replace them with `{{ config('company.name') }}`.
We will update the headers in the PDF views to support conditional rendering:

```blade
@if(config('company.header_path') && isset($headerBase64) && $headerBase64)
    <img src="data:image/png;base64,{{ $headerBase64 }}" class="header-img" alt="Header">
@elseif(config('company.logo_path') && isset($logoBase64) && $logoBase64)
    <img src="data:image/png;base64,{{ $logoBase64 }}" style="max-height: 100px; display: block; margin: 0 auto;" alt="Logo">
@endif
```
The footer will be conditionally rendered based on whether the config for it is set and the image exists.

#### [MODIFY] resources/views/invoices/invoice_pdf.blade.php
#### [MODIFY] resources/views/sales/pdf_sales.blade.php
#### [MODIFY] resources/views/... (all other PDF and report views containing hardcoded names or headers)

---

## Example `.env` Setups for your deployments

**For Qasemi Brothers:**
```env
COMPANY_NAME="شرکت صنعتی برادران قاسمی"
COMPANY_DESC="تولید و صادر کننده انواع مختلف قالین و گیلم های دست بافت افغانستان"
COMPANY_HEADER_PATH="images/header.png"
COMPANY_FOOTER_PATH="images/footer.png"
COMPANY_LOGO_PATH=""
```

**For Atlas Manan:**
```env
COMPANY_NAME="شرکت تولید قالین اطلس منان"
COMPANY_DESC="توضیحات اطلس منان"
COMPANY_HEADER_PATH=""
COMPANY_FOOTER_PATH=""
COMPANY_LOGO_PATH="images/multi_comapany_files/atlas_manan_logo.jpeg"
```

## Verification Plan

### Manual Verification
- We will deploy the code and check a sample PDF report (e.g. Sales Report or Invoice).
- We will manually test toggling the `.env` variables to simulate Atlas Manan and Qasemi Brothers and ensure the PDF headers and footers adjust correctly.
