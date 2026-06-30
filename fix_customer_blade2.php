<?php
$content = file_get_contents('resources/views/customers/customer-payment.blade.php');

// We need to fix lines 665 to 667.
// Currently it is:
//                             </table>
//                                             <!-- Tab 3: Reconciliations -->
//                     <div class="tab-pane fade" id="customer-reconciliation" role="tabpanel">

$content = preg_replace(
    "/<\/table>\s*<!-- Tab 3: Reconciliations -->/",
    "</table>\n                        </div>\n                    </div>\n\n                    <!-- Tab 3: Reconciliations -->",
    $content
);

file_put_contents('resources/views/customers/customer-payment.blade.php', $content);
echo "Fixed!";
?>
