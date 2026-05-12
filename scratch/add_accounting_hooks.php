<?php
$file = '/home/anonymous/projects/public_html/app/Http/Controllers/CarpetsController.php';
$content = file_get_contents($file);

// Update UpdatetWeight
$content = str_replace(
    '$update = Carpet::where(\'carpet_id\', $carpet_id)->update($data);',
    '$oldWarehouse = $carpet->warehouse_id; $oldAccount = $carpet->override_inventory_account_id;
        $update = Carpet::where(\'carpet_id\', $carpet_id)->update($data);
        if ($update) { $this->syncAccounting(Carpet::find($carpet_id), $oldWarehouse, $oldAccount); }',
    $content
);

// Update UpdatetBuyCarpet
$content = str_replace(
    '$update = $carpet->update($data);',
    '$oldWarehouse = $carpet->warehouse_id; $oldAccount = $carpet->override_inventory_account_id;
        $update = $carpet->update($data);
        if ($update) { $this->syncAccounting($carpet, $oldWarehouse, $oldAccount); }',
    $content
);

// Update update (for contract)
// This is tricky because $update = $carpet->update($data); is used in many places.
// I'll use a more specific search.

$content = str_replace(
    '$update = $carpet->update($data);',
    '$oldWarehouse = $carpet->warehouse_id; $oldAccount = $carpet->override_inventory_account_id;
        $update = $carpet->update($data);
        if ($update) { $this->syncAccounting($carpet, $oldWarehouse, $oldAccount); }',
    $content
);

file_put_contents($file, $content);
echo "Hooks updated\n";
