@extends('dsh.master')
@section('title' , 'ثبت انتقال گدام')
@section('content')

<style>
    .glass-card {
        background: white;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 30px;
    }
    
    .glass-header {
        background: #f8fafc;
        padding: 20px 25px;
        border-bottom: 1px solid rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .glass-header h4 {
        margin: 0;
        font-weight: 700;
        color: #1e293b;
        font-size: 1.2rem;
    }
    
    .custom-input {
        border-radius: 8px;
        border: 2px solid #e8f5e9;
        padding: 10px 15px;
        transition: all 0.2s;
    }
    .custom-input:focus {
        border-color: #43a047;
        box-shadow: 0 0 0 0.2rem rgba(67, 160, 71, 0.1);
    }

    .form-section-title {
        color: #2e7d32;
        font-weight: 700;
        border-bottom: 2px solid #e8f5e9;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .btn-premium {
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 25px;
        transition: all 0.2s;
    }
    
    .finance-box {
        background: #fdfdfe;
        border: 1px dashed #cfd8dc;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .item-select-row {
        transition: background-color 0.2s;
    }
    .item-select-row:hover {
        background-color: #f1f8e9;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="glass-card">
            <div class="glass-header">
                <h4><i class="fa fa-exchange text-primary mr-2"></i> ثبت انتقال جنس بین گدام‌ها</h4>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('accounting.transfers.store') }}" method="POST" id="transferForm">
                    @csrf
                    
                    <h6 class="form-section-title"><i class="fa fa-info-circle mr-2"></i> مشخصات مکتوب انتقال</h6>
                    
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted small">نمبر مکتوب</label>
                                <input type="text" name="transfer_number" value="{{ $transferNo }}" class="form-control custom-input font-weight-bold text-dark" readonly required>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted small">تاریخ انتقال</label>
                                <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" class="form-control custom-input" required>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted small">نوعیت جنس انتقالی</label>
                                <select name="item_type" id="item_type" class="form-control custom-input select2" required>
                                    <option value="" disabled selected>-- انتخاب کنید --</option>
                                    <option value="carpet">قالین (Carpet)</option>
                                    <option value="yarn">تار (Yarn)</option>
                                    <option value="dye">رنگ (Dye)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted small">توضیحات</label>
                                <input type="text" name="description" class="form-control custom-input" placeholder="مثلا: انتقال قالین ها به گدام نمایشگاه">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted small">گدام مبدا (Source Warehouse)</label>
                                <select name="source_warehouse_id" id="source_warehouse_id" class="form-control custom-input select2" disabled required>
                                    <option value="" disabled selected>-- ابتدا نوعیت جنس را انتخاب کنید --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="font-weight-bold text-muted small">گدام مقصد (Destination Warehouse)</label>
                                <select name="destination_warehouse_id" id="destination_warehouse_id" class="form-control custom-input select2" disabled required>
                                    <option value="" disabled selected>-- ابتدا گدام مبدا را انتخاب کنید --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Items Selection Table -->
                    <div id="itemsSelectionSection" style="display: none;">
                        <h6 class="form-section-title"><i class="fa fa-list mr-2"></i> انتخاب اقلام موجود در گدام مبدا</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-striped" id="itemsTable">
                                <thead class="bg-light">
                                    <tr id="tableHeader">
                                        <!-- Dynamic Header -->
                                    </tr>
                                </thead>
                                <tbody id="itemsList">
                                    <!-- Dynamic Rows Loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Final Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-lg-12 text-right">
                            <button class="btn btn-primary btn-premium shadow-sm" type="submit" id="submitBtn" disabled>
                                <i class="fa fa-save mr-2"></i> تایید نهایی و صدور سند انتقال
                            </button>
                            <a href="{{ route('accounting.transfers.index') }}" class="btn btn-light btn-premium shadow-sm ml-2">انصراف</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-plugins')
<script>
    $(document).ready(function() {
        // Init select2 safely
        if($.fn.select2) {
            $('.select2').select2({ width: '100%', dir: 'rtl' });
        }

        const warehouses = @json($warehouses);
        
        // Step 1: Filter warehouses based on selected item type
        $('#item_type').on('change', function() {
            const type = $(this).val();
            let warehouseSubtype = 'carpet';
            
            if (type === 'yarn') warehouseSubtype = 'yarn';
            else if (type === 'dye') warehouseSubtype = 'dye';
            
            const filteredWh = warehouses.filter(w => w.subtype === warehouseSubtype);
            
            let options = '<option value="" disabled selected>-- انتخاب کنید --</option>';
            filteredWh.forEach(w => {
                options += `<option value="${w.id}">${w.name} (${w.location || ''})</option>`;
            });
            
            $('#source_warehouse_id').html(options).prop('disabled', false).trigger('change');
            $('#destination_warehouse_id').html('<option value="" disabled selected>-- ابتدا گدام مبدا را انتخاب کنید --</option>').prop('disabled', true);
            $('#itemsSelectionSection').hide();
            $('#submitBtn').prop('disabled', true);
        });

        // Step 2: Set target warehouses (exclude source warehouse)
        $('#source_warehouse_id').on('change', function() {
            const sourceId = $(this).val();
            if (!sourceId) return;

            const type = $('#item_type').val();
            let warehouseSubtype = 'carpet';
            if (type === 'yarn') warehouseSubtype = 'yarn';
            else if (type === 'dye') warehouseSubtype = 'dye';

            const filteredWh = warehouses.filter(w => w.subtype === warehouseSubtype && w.id != sourceId);
            
            let options = '<option value="" disabled selected>-- انتخاب کنید --</option>';
            filteredWh.forEach(w => {
                options += `<option value="${w.id}">${w.name} (${w.location || ''})</option>`;
            });
            
            $('#destination_warehouse_id').html(options).prop('disabled', false);
            
            loadWarehouseStock(sourceId, type);
        });

        // Step 3: Load items inside source warehouse via AJAX
        function loadWarehouseStock(warehouseId, type) {
            $('#itemsList').html('<tr><td colspan="10" class="text-muted"><i class="fa fa-spinner fa-spin mr-2"></i> در حال بارگذاری موجودی گدام...</td></tr>');
            $('#itemsSelectionSection').show();
            
            $.ajax({
                url: "{{ route('accounting.transfers.api.items') }}",
                data: { warehouse_id: warehouseId, item_type: type },
                dataType: 'json',
                success: function(response) {
                    let html = '';
                    let headerHtml = '';
                    
                    if (type === 'carpet') {
                        headerHtml = `
                            <th style="width: 50px;">انتخاب</th>
                            <th>نمبر قالین</th>
                            <th>مساحت (m²)</th>
                            <th>مرحله / وضعیت</th>
                        `;
                        
                        if (response.items.length === 0) {
                            html = '<tr><td colspan="4" class="text-muted p-4">هیچ قالینی در این گدام یافت نشد.</td></tr>';
                            $('#submitBtn').prop('disabled', true);
                        } else {
                            response.items.forEach((item, index) => {
                                html += `
                                    <tr class="item-select-row">
                                        <td>
                                            <input type="checkbox" name="items[${index}][id]" value="${item.carpet_id}" class="item-checkbox">
                                        </td>
                                        <td class="font-weight-bold">${item.carpet_no}</td>
                                        <td>${parseFloat(item.area).toFixed(2)} m²</td>
                                        <td><span class="badge badge-info">${item.status_txt}</span></td>
                                    </tr>
                                `;
                            });
                            $('#submitBtn').prop('disabled', false);
                        }
                    } else {
                        headerHtml = `
                            <th>نام جنس</th>
                            <th>موجودی فعلی (کیلوگرم)</th>
                            <th style="width: 250px;">مقدار انتقالی (کیلوگرم)</th>
                        `;
                        
                        if (response.items.length === 0) {
                            html = '<tr><td colspan="3" class="text-muted p-4">هیچ موجودی برای مواد خام در این گدام وجود ندارد.</td></tr>';
                            $('#submitBtn').prop('disabled', true);
                        } else {
                            response.items.forEach((item, index) => {
                                html += `
                                    <tr>
                                        <td class="font-weight-bold">${item.name}</td>
                                        <td class="text-primary font-weight-bold">${item.available_qty.toFixed(2)} کیلوگرم</td>
                                        <td>
                                            <input type="hidden" name="items[${index}][id]" value="${item.material_type_id}">
                                            <input type="number" step="0.01" name="items[${index}][quantity]" 
                                                class="form-control custom-input qty-input" 
                                                placeholder="0.00" 
                                                min="0" max="${item.available_qty}" 
                                                data-available="${item.available_qty}">
                                        </td>
                                    </tr>
                                `;
                            });
                            $('#submitBtn').prop('disabled', false);
                        }
                    }
                    
                    $('#tableHeader').html(headerHtml);
                    $('#itemsList').html(html);
                },
                error: function() {
                    $('#itemsList').html('<tr><td colspan="10" class="text-danger"><i class="fa fa-exclamation-triangle"></i> خطا در دریافت اطلاعات موجودی.</td></tr>');
                }
            });
        }

        // Form Validation on Submit
        $('#transferForm').on('submit', function(e) {
            const type = $('#item_type').val();
            
            if (type === 'carpet') {
                const checked = $('.item-checkbox:checked').length;
                if (checked === 0) {
                    e.preventDefault();
                    if(typeof swal !== 'undefined') {
                        swal("توجه", "لطفا حداقل یک قالین را برای انتقال انتخاب کنید.", "warning");
                    } else {
                        alert("لطفا حداقل یک قالین را برای انتقال انتخاب کنید.");
                    }
                    return false;
                }
            } else {
                let hasQty = false;
                let error = false;
                
                $('.qty-input').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    const max = parseFloat($(this).data('available'));
                    
                    if (val > 0) hasQty = true;
                    if (val > max) {
                        error = true;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                if (error) {
                    e.preventDefault();
                    if(typeof swal !== 'undefined') {
                        swal("خطا", "مقدار انتقالی نمیتواند از مقدار موجودی بیشتر باشد.", "error");
                    } else {
                        alert("مقدار انتقالی نمیتواند از مقدار موجودی بیشتر باشد.");
                    }
                    return false;
                }
                
                if (!hasQty) {
                    e.preventDefault();
                    if(typeof swal !== 'undefined') {
                        swal("توجه", "لطفا مقدار انتقالی را برای حداقل یکی از مواد وارد کنید.", "warning");
                    } else {
                        alert("لطفا مقدار انتقالی را برای حداقل یکی از مواد وارد کنید.");
                    }
                    return false;
                }
            }
        });
    });
</script>
@endsection
