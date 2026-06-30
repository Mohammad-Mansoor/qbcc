import re

with open('app/Http/Controllers/FinishingTeamPaymentController.php', 'r') as f:
    content = f.read()

# Fix dynamic totals queries
content = re.sub(
    r"(FinishingTeamPayment::(?:where|with|select)[^;]*?)where\('status',\s*1\)",
    r"\1where('status', '!=', 2)",
    content
)

# Fix paginate queries
content = re.sub(
    r"(\$payments\s*=\s*FinishingTeamPayment::where\('team_id',[^;]*?where\('finish_number',\s*'General'\))(->orderBy\('date',\s*'DESC'\)->paginate\(\d+\);)",
    r"\1->where('status', '!=', 2)\2",
    content
)

# Fix postPaymentToAccounting
content = content.replace(
    "'source_id' => $payment->id,",
    "'source_id' => $payment->id,\n                'source_type' => 'App\\\\FinishingTeamPayment',"
)

# Fix destroy method
destroy_code = """    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = FinishingTeamPayment::find($id);
            $team_name = DB::table('finishing_teams')->where('id', $payment->team_id)->first();

            // Check Permissions
            if (!Auth::user()->can('cancel_finishing_payment')) {
                abort(403, 'شما صلاحیت ابطال پرداخت‌های بخش تیاری را ندارید.');
            }

            // Reverse Accounting Entry (Only if approved)
            if ($payment->status == 1) {
                $this->accountingService->reverseTransactionBySource($payment->id, 'Finishing Team Payment Deleted', 'App\\\\FinishingTeamPayment');
            }

            // Reverse Allocations
            if ($payment->is_advance) {
                $allocations = \App\FinishingPaymentAllocation::where('finishing_payment_id', $payment->id)->get();
                foreach ($allocations as $allocation) {
                    $this->accountingService->reverseTransactionBySource($allocation->id, 'Finishing Allocation Deleted', 'App\\\\FinishingPaymentAllocation');
                    $allocation->delete();
                }
            }

            $payment->status = 2; // 2 = Cancelled
            $payment->save();

            $activity = new Activity();
            $activity->date = Carbon::today()->format('Y-m-d');
            $activity->description = "ابطال پرداخت تیاری: " . $team_name->name . " مبلغ " . $payment->original_amount . " " . $payment->currency_code;
            $activity->user_id = Auth::user()->id;
            $activity->save();

            return redirect()->back()->with('status', 'پرداخت با موفقیت ابطال گردید!');
        });
    }"""

# Find and replace the entire destroy method
content = re.sub(
    r"    public function destroy\(\$id\)\n    \{.*?(?=\n    \})    \}",
    destroy_code,
    content,
    flags=re.DOTALL
)

with open('app/Http/Controllers/FinishingTeamPaymentController.php', 'w') as f:
    f.write(content)

print("Replacement complete")
