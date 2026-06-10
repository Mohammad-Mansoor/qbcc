# Implementation Details - Transitioning Check-Book to Purchase Invoices

This document describes the step-by-step implementation details to refactor the legacy `check-book` logic into a structured, UI-manageable `Purchase Invoice` module. All operations are designed to preserve legacy structures, multi-currency (Forensic FX) normalization, and general ledger postings.

---

## 1. Database Schema Migrations

Two migrations are required to establish the new `PurchaseInvoice` entity and associate purchased carpets with it.

### Migration A: Create `purchase_invoices` Table
Create a new table for the purchase invoice entity. This table supports status management (`open` / `closed`) and links each invoice to a seller agent.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number');
            $table->unsignedBigInteger('agent_id');
            $table->string('status')->default('open'); // 'open' or 'closed'
            $table->date('date');
            $table->foreign('agent_id')->references('agent_id')->on('agents')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_invoices');
    }
}
```

### Migration B: Add `purchase_invoice_id` to `carpets` Table
Add a foreign key column on the `carpets` table to link multiple carpets to a single purchase invoice.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseInvoiceIdToCarpetsTable extends Migration
{
    public function up()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_invoice_id')->nullable()->after('agent_id');
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('carpets', function (Blueprint $table) {
            $table->dropForeign(['purchase_invoice_id']);
            $table->dropColumn('purchase_invoice_id');
        });
    }
}
```

---

## 2. Model Relational Updates

Define the new model class and update the existing relationships on `Carpet` and `Agents` models.

### New Model Class: `App\PurchaseInvoice`
Create the class file `app/PurchaseInvoice.php`:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(Agents::class, 'agent_id', 'agent_id');
    }

    public function carpets()
    {
        return $this->hasMany(Carpet::class, 'purchase_invoice_id', 'carpet_id');
    }
}
```

### Updates in `app/Carpet.php`
Add the inverse relationship:

```php
    public function purchase_invoice() {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id', 'id');
    }
```

### Updates in `app/Agents.php`
Add the relationship to fetch invoices for an agent:

```php
    public function purchase_invoices() {
        return $this->hasMany(PurchaseInvoice::class, 'agent_id', 'agent_id');
    }
```

---

## 3. Controller Modifications

### A. Updating `CarpetCheckBookController`
Modify `app/Http/Controllers/CarpetCheckBookController.php` to handle `PurchaseInvoice` CRUD.

```php
<?php

namespace App\Http\Controllers;

use App\Agents;
use App\PurchaseInvoice;
use App\Carpet;
use Illuminate\Http\Request;

class CarpetCheckBookController extends Controller
{
    // List all purchase invoices and pass seller agents to support new invoice creation form
    public function index()
    {
        $invoices = PurchaseInvoice::with('agent.user')->get();
        $agents = Agents::where('contract_type', 'carpet seller')->get();
        return view('carpet-check-book.index', compact('invoices', 'agents'));
    }

    // Handle purchase invoice creation
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'agent_id' => 'required|exists:agents,agent_id',
            'date' => 'required|date',
        ]);

        $data['status'] = 'open';

        PurchaseInvoice::create($data);

        return redirect()->back()->with('status', 'انوایس خرید جدید با موفقیت ایجاد شد!');
    }

    // View carpets under a specific purchase invoice with totals/counts
    public function show($id)
    {
        $invoice = PurchaseInvoice::with('agent.user')->findOrFail($id);
        $carpets = Carpet::where('purchase_invoice_id', $id)->with('type', 'quality')->get();

        return view('carpet-check-book.check-number-list', compact('invoice', 'carpets'));
    }

    // Close purchase invoice to prevent further addition of carpets
    public function closeInvoice($id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        $invoice->status = 'closed';
        $invoice->save();

        return redirect()->back()->with('status', 'انوایس خرید با موفقیت بسته شد!');
    }
}
```

### B. Route Configuration
Add a route for closing the invoice in `routes/web.php`:
```php
Route::post('check-book/{id}/close', 'CarpetCheckBookController@closeInvoice')->name('check-book.close');
```

### C. Updates in `CarpetsController`
Inject invoice logic into the Direct Purchase registry (`list-buy-carpet` view lifecycle).

1. **Load invoices in View Handlers** (`listBuyCarpet`, `show_all_buy_carpet`, `search_buy_carpet`, `editBuyCarpet`):
   ```php
   $purchaseInvoices = \App\PurchaseInvoice::where('status', 'open')->with('agent.user')->get();
   // Pass $purchaseInvoices to carpets.list-buy-carpet view
   ```

2. **Save invoice assignment inside `PostBuyCarpet` and `UpdatetBuyCarpet`**:
   Add `'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id'` to validation functions.
   Add safety guards before persistence to ensure closed invoices cannot accept additions:
   ```php
   if ($request->purchase_invoice_id) {
       $invoice = \App\PurchaseInvoice::findOrFail($request->purchase_invoice_id);
       if ($invoice->status === 'closed') {
           return redirect()->back()->withErrors(['purchase_invoice_id' => 'این انوایس بسته شده است و امکان اضافه کردن قالین جدید به آن وجود ندارد.']);
       }
   }
   $data['purchase_invoice_id'] = $request->purchase_invoice_id;
   ```

---

## 4. Frontend View Redesigns

### A. Sidebar Label Update (`resources/views/dsh/master.blade.php`)
Rename the sidebar module name from "چک بٌک شده ها" to "انوایس‌های خرید":
```diff
- <li><a href="/dashboard/check-book">چک بٌک شده ها</a></li>
+ <li><a href="/dashboard/check-book">انوایس‌های خرید (Purchase Invoices)</a></li>
```

### B. Purchase Invoice Index (`resources/views/carpet-check-book/index.blade.php`)
Provide the invoice overview list and creation modal:
```html
@extends('dsh.master')
@section('title' , 'انوایس‌های خرید')
@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5>مدیریت انوایس‌های خرید (Purchase Invoices)</h5>
          <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createInvoiceModal">
            <i class="fa fa-plus"></i> ایجاد انوایس خرید جدید
          </button>
        </div>
        <div class="card-body">
          <table class="table table-hover table-xs text-right">
            <thead>
            <tr>
              <th>آی دی</th>
              <th>نمبر انوایس</th>
              <th>نام نماینده (فروشنده)</th>
              <th>تاریخ</th>
              <th>وضعیت</th>
              <th>عملیات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($invoices as $invoice)
              <tr>
                <td>{{ $invoice->id }}</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->agent->user->name ?? 'N/A' }}</td>
                <td>{{ $invoice->date }}</td>
                <td>
                  <span class="badge {{ $invoice->status == 'open' ? 'badge-success' : 'badge-danger' }}">
                    {{ $invoice->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}
                  </span>
                </td>
                <td>
                  <a class="btn btn-warning btn-sm" href="/dashboard/check-book/{{ $invoice->id }}">مشاهده جزئیات</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">هیچ انوایس خریدی در سیستم ثبت نشده است.</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: CREATE INVOICE -->
  <div class="modal fade" id="createInvoiceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">ایجاد انوایس خرید جدید</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="/dashboard/check-book" method="post">
          @csrf
          <div class="modal-body text-right">
            <div class="form-group">
              <label>نمبر انوایس</label>
              <input type="text" name="invoice_number" class="form-control" required placeholder="INV-2026-001">
            </div>
            <div class="form-group">
              <label>نماینده (فروشنده قالین)</label>
              <select name="agent_id" class="form-control" required>
                <option value="">انتخاب فروشنده...</option>
                @foreach($agents as $agent)
                  <option value="{{ $agent->agent_id }}">{{ $agent->user->name }} ({{ $agent->account_no }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>تاریخ</label>
              <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
            <button type="submit" class="btn btn-primary">ثبت انوایس</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
```

### C. Purchase Invoice Details (`resources/views/carpet-check-book/check-number-list.blade.php`)
Display metadata, list carpets, calculate totals, and support invoice closure.
```html
@extends('dsh.master')
@section('content')
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-sm-6 text-right">
              <h4 class="font-weight-bold text-primary mb-3">انوایس خرید: {{ $invoice->invoice_number }}</h4>
              <table class="table table-sm table-borderless">
                <tr><td class="font-weight-bold w-25">تاریخ انوایس:</td><td>{{ $invoice->date }}</td></tr>
                <tr><td class="font-weight-bold">فروشنده:</td><td>{{ $invoice->agent->user->name ?? 'N/A' }}</td></tr>
                <tr>
                  <td class="font-weight-bold">وضعیت:</td>
                  <td>
                    <span class="badge {{ $invoice->status == 'open' ? 'badge-success' : 'badge-danger' }}">
                      {{ $invoice->status == 'open' ? 'باز (Open)' : 'بسته (Closed)' }}
                    </span>
                  </td>
                </tr>
              </table>
            </div>
            <div class="col-sm-6 text-left">
              @if($invoice->status == 'open')
                <form action="{{ route('check-book.close', $invoice->id) }}" method="post" onsubmit="return confirm('پس از بستن انوایس، دیگر امکان اضافه کردن قالین جدید به آن نخواهد بود. آیا ادامه می‌دهید؟')">
                  @csrf
                  <button type="submit" class="btn btn-danger shadow rounded-lg px-4">
                    <i class="feather icon-lock mr-1"></i> بستن انوایس (Close Invoice)
                  </button>
                </form>
              @endif
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-sm table-hover text-right" style="direction: rtl;">
              <thead>
              <tr class="bg-light">
                <th>نمبر قالین</th>
                <th>نوعیت قالین</th>
                <th>طول</th>
                <th>عرض</th>
                <th>مساحت نهایی</th>
                <th class="text-left">قیمت فی متر (Native)</th>
                <th class="text-left">قیمت کل (USD Normalized)</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($carpets as $carpet)
                <tr>
                  <td>{{ $carpet->carpet_no }}</td>
                  <td>{{ $carpet->type->carpet_type ?? 'N/A' }}</td>
                  <td>{{ $carpet->height }} m</td>
                  <td>{{ $carpet->width }} m</td>
                  <td>{{ $carpet->area }} m²</td>
                  <td class="text-left">{{ number_format($carpet->original_price ?? $carpet->price, 2) }} {{ $carpet->currency_code ?? '$' }}</td>
                  <td class="text-left" style="direction: ltr;">${{ number_format($carpet->total_price, 2) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">هیچ قالینی به این انوایس اضافه نشده است.</td>
                </tr>
              @endforelse
              </tbody>
            </table>
          </div>

          <hr class="my-4">
          <div class="row">
            <div class="col-lg-4 col-md-5 col-sm-6">
              <h6 class="font-weight-bold text-dark mb-3">خلاصه مالی و فیزیکی انوایس:</h6>
              <table class="table table-sm table-bordered">
                <tr>
                  <td class="font-weight-bold bg-light">تعداد کل قالین‌ها (Quantity)</td>
                  <td class="font-weight-bold text-center">{{ $carpets->count() }} Pcs</td>
                </tr>
                <tr>
                  <td class="font-weight-bold bg-light">مجموع مساحت (Total Area)</td>
                  <td class="font-weight-bold text-center text-primary">{{ number_format($carpets->sum('area'), 2) }} m²</td>
                </tr>
                <tr>
                  <td class="font-weight-bold bg-light">مجموع قیمت کل (Total Price USD)</td>
                  <td class="font-weight-bold text-center text-success" style="direction: ltr;">${{ number_format($carpets->sum('total_price'), 2) }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
```

### D. Carpet Registration Selector (`resources/views/carpets/list-buy-carpet.blade.php`)
Inject the selection drop-down directly in the registry form modal:
```html
<div class="col-md-4 form-group">
    <label class="form-label-premium">انتخاب انوایس خرید (Purchase Invoice)</label>
    <select name="purchase_invoice_id" class="form-control select2">
        <option value="">بدون انوایس خرید...</option>
        @foreach($purchaseInvoices as $inv)
            <option value="{{ $inv->id }}" {{ ($editCarpet && $editCarpet->purchase_invoice_id == $inv->id) ? 'selected' : '' }}>
                {{ $inv->invoice_number }} ({{ $inv->agent->user->name ?? 'N/A' }})
            </option>
        @endforeach
    </select>
</div>
```
