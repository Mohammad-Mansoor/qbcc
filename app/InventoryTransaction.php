<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'item_id', 'warehouse_id', 'category_id', 'type', 'direction', 'quantity', 
        'area', 'unit_cost', 'total_cost', 'is_value_adjustment', 'reference_type', 
        'reference_id', 'status', 'created_by'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id', 'material_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Resolve the readable item name (Material name or Carpet type)
     */
    public function getItemNameAttribute()
    {
        if (!$this->item) {
            return 'آیتم نامشخص';
        }

        if ($this->item->type === 'App\MaterialType') {
            $type = MaterialType::find($this->item->ref_id);
            return $type ? $type->material_type : 'مواد خام نامشخص';
        } elseif ($this->item->type === 'App\Carpet') {
            $carpet = Carpet::with('type')->find($this->item->ref_id);
            if ($carpet) {
                $typeName = $carpet->type ? $carpet->type->carpet_type : 'قالین';
                return $typeName . ' (نمبر: ' . $carpet->carpet_no . ')';
            }
            return 'قالین نامشخص';
        }

        return 'آیتم نامشخص';
    }

    /**
     * Resolve Persian text for transaction types
     */
    public function getTypeFaAttribute()
    {
        $map = [
            'PURCHASE' => 'خرید مواد خام',
            'PROD_SERVICE' => 'خدمات تولیدی',
            'PROD_ISSUE' => 'مصرف مواد (تولید)',
            'PROD_FINISH' => 'ورود قالین از تولید',
            'SALE' => 'فروش',
            'TRANSFER_OUT' => 'انتقال (خروج)',
            'TRANSFER_IN' => 'انتقال (ورود)',
            'REVERSAL' => 'برگشتی / باطل شده',
            'WASH' => 'شستشو',
            'REPAIR' => 'ترمیم',
        ];
        return $map[strtoupper($this->type)] ?? $this->type;
    }

    /**
     * Resolve the source document/bill number
     */
    public function getReferenceCodeAttribute()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return 'N/A';
        }

        try {
            switch ($this->reference_type) {
                case 'App\PurchaseMaterial':
                    $pm = \App\PurchaseMaterial::with('purchaseBill')->find($this->reference_id);
                    return $pm && $pm->purchaseBill ? $pm->purchaseBill->bill_number : 'بل خرید مواد خام';
                    
                case 'App\MaterialSale':
                    $ms = \App\MaterialSale::with('invoice')->find($this->reference_id);
                    return $ms && $ms->invoice ? $ms->invoice->invoice_number : 'فروش مواد خام';
                    
                case 'App\WarehouseTransfer':
                    $wt = \App\WarehouseTransfer::find($this->reference_id);
                    return $wt ? $wt->transfer_number : 'سند انتقال';
                    
                case 'App\Carpet':
                    $c = \App\Carpet::find($this->reference_id);
                    return $c ? 'قالین ' . $c->carpet_no : 'قالین';
                    
                case 'App\CarpetWash':
                    $cw = \App\CarpetWash::find($this->reference_id);
                    return $cw ? $cw->wash_number : 'شستشو';
                    
                case 'App\CarpetRepair':
                    $cr = \App\CarpetRepair::find($this->reference_id);
                    return $cr ? $cr->kachaee_number : 'ترمیم';
                    
                case 'App\FinishingWork':
                    $fw = \App\FinishingWork::find($this->reference_id);
                    return $fw ? $fw->finish_number : 'تیاری';
                    
                case 'App\ProductionBatch':
                    $pb = \App\ProductionBatch::find($this->reference_id);
                    return $pb ? 'بچ تولیدی ' . $pb->batch_number : 'بچ تولیدی';
                    
                default:
                    $parts = explode('\\', $this->reference_type);
                    return end($parts) . ' #' . $this->reference_id;
            }
        } catch (\Exception $e) {
            return 'خطا در خواندن سند';
        }
    }
}
