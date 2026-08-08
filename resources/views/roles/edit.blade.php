@extends('dsh.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h5>ویرایش نقش</h5>
                </div>
                <div class="card-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>خطا!</strong> مشکلی در ورودی های شما وجود دارد.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @php
                        $sidebarPermissions = [
                            'view_coa', 'view_journals', 'view_mapping_rules', 'view_currencies', 'view_pl_report', 'view_balance_sheet', 'view_trial_balance', 'view_comparative_pl', 'view_cash_flow_report', 'view_fx_exposure_report', 'view_inventory_valuation', 'view_cost_center_performance', 'view_audit_corrections', 'view_account_ledger', 'view_customer_statement', 'view_agent_statement', 'view_different_account_statement', 'view_kachaee_team_statement', 'view_washing_team_statement', 'view_finishing_team_statement', 'view_seller_statement', 'view_employee_statement', 'view_agents', 'view_agent_money_requests', 'approve_agent_money_requests', 'view_buy_carpets', 'view_purchased_carpets_report', 'view_purchase_bills', 'view_carpet_types', 'view_carpet_qualities', 'view_material_purchases', 'view_material_stock', 'view_raw_material_bills', 'view_purchase_material_requests', 'view_material_sales', 'view_material_sale_requests', 'view_string_sellers', 'view_seller_money_requests', 'view_material_categories', 'view_material_types', 'view_different_accounts', 'view_different_account_money_requests', 'view_carpet_repairs', 'view_kachaee_teams', 'view_kachaee_batches', 'approve_kachaee_money_requests', 'view_carpet_washes', 'view_washing_teams', 'view_washing_batches', 'view_washing_money_requests', 'view_finishing_centers', 'view_finishing_teams', 'view_finishing_batches', 'view_refinish_requests', 'view_finishing_money_requests', 'view_employees', 'view_carpet_stock', 'view_sales', 'view_orders', 'view_assets_accounts', 'view_customers', 'view_customer_money_requests', 'view_production_dashboard', 'view_inventory_dashboard', 'view_finance_dashboard', 'view_sales_dashboard', 'view_purchases_dashboard', 'view_cost_analytics', 'view_monthly_expenses', 'view_payroll', 'run_payroll', 'view_payroll_slip', 'edit_payroll', 'delete_payroll', 'view_employee_money_requests', 'view_employee_departments', 'approve_refinish_requests', 'manage_roles_and_permissions', 'view_phone_book', 'view_provinces', 'view_agent_employees', 'view_activities',
                            // Warehouse sidebar navigation links only (these 4 correspond to sidebar menu items)
                            'view_warehouses', 'view_warehouse_inventory_report', 'view_inventory_transfers', 'view_warehouse_movements',
                        ];
                    @endphp

                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @method('PATCH')
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group fill">
                                    <label><strong>نام نقش:</strong></label>
                                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" placeholder="نام نقش را وارد کنید" required {{ $role->name == 'Super Admin' ? 'readonly' : '' }}>
                                    @if($role->name == 'Super Admin')
                                        <small class="text-danger">نام سوپر ادمین قابل تغییر نیست.</small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                                <h5><strong>دسترسی ها (Permissions):</strong></h5>
                                <hr>
                                <div class="row">
                                    @foreach($permissionGroups as $key => $group)
                                        @if(count($group['perms']) > 0)
                                            <div class="col-md-4 mb-4">
                                                <div class="card shadow-sm border-info">
                                                    <div class="card-header bg-info text-white py-2">
                                                        <h6 class="m-0 font-weight-bold">
                                                            <input type="checkbox" class="group-checkbox" id="group_{{ $key }}">
                                                            <label for="group_{{ $key }}" class="m-0 mr-2" style="cursor: pointer; user-select: none;">{{ $group['name'] }}</label>
                                                        </h6>
                                                    </div>
                                                    <div class="card-body p-3" style="max-height: 250px; overflow-y: auto;">
                                                        @foreach($group['perms'] as $permission)
                                                            <div class="custom-control custom-checkbox mb-2">
                                                                <input type="checkbox" name="permission[]" value="{{ $permission->id }}" 
                                                                    class="custom-control-input perm-checkbox group_{{ $key }}_checkbox" 
                                                                    id="perm_{{ $permission->id }}"
                                                                    {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                                    {{ $role->name == 'Super Admin' ? 'disabled' : '' }}>
                                                                <label class="custom-control-label" for="perm_{{ $permission->id }}" style="font-size: 13px; cursor: pointer; user-select: none; {{ in_array($permission->name, $sidebarPermissions) ? 'color: #4caf50; font-weight: bold;' : '' }}">{{ $permission->dari_name ?? $permission->name }}</label>
                                                                
                                                                @if($role->name == 'Super Admin' && in_array($permission->id, $rolePermissions))
                                                                    <input type="hidden" name="permission[]" value="{{ $permission->id }}">
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-3">
                                <a href="{{ route('roles.index') }}" class="btn btn-white">برگشت</a>
                                <button type="submit" class="btn btn-warning">ویرایش نقش</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize group checkboxes
        $('.group-checkbox').each(function() {
            var groupId = $(this).attr('id');
            var groupClass = groupId + '_checkbox';
            var allChecked = $('.' + groupClass).length > 0 && $('.' + groupClass).length === $('.' + groupClass + ':checked').length;
            $(this).prop('checked', allChecked);
        });

        // Select all permissions in a group
        $('.group-checkbox').change(function() {
            var groupId = $(this).attr('id');
            $('.' + groupId + '_checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
        });

        // Update group checkbox based on individual checkboxes
        $('.perm-checkbox').change(function() {
            var groupClass = $(this).attr('class').split(' ').find(c => c.endsWith('_checkbox') && c !== 'perm-checkbox');
            var groupId = groupClass.replace('_checkbox', '');
            
            var allChecked = $('.' + groupClass).length === $('.' + groupClass + ':checked').length;
            $('#' + groupId).prop('checked', allChecked);
        });
    });
</script>
@endsection
