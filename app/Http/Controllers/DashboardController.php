<?php

namespace App\Http\Controllers;



use App\AgentPayment;
use App\Agents;
use App\CarpetOrder;
use App\CarpetType;
use App\CustomerPayment;
use App\DifferentAccountPayment;
use App\DifferentAccountTotal;
use App\EmployeePayment;
use App\FinishingTeamPayment;
use App\KachaeePayment;
use App\SellerPayment;
use App\WashingPayment;
use Illuminate\Http\Request;
use App\Carpet;
use App\Currency;
use App\MaterialStock;
use App\MaterialCategory;
use App\OfficeCashBook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    
    public function search_this_month_carpet(Request $request){

        $general_cashbook = OfficeCashBook::where('user_role', 'SP')->sum('balance');
        $central_cashbook = OfficeCashBook::where('user_role', 'CO')->sum('balance');
        $sales_cashbook = OfficeCashBook::where('user_role', 'SO')->sum('balance');
        $all_office_cashbook = $general_cashbook + $central_cashbook + $sales_cashbook;


        $ready_to_sale_carpet = Carpet::where('status', 5)->get();
        $all_carpet_count = Carpet::where('status', '!=', 6)->get();
        $all_carpet_area = Carpet::where('status', '!=', 6)->sum('area');

        $sale_carpet_area = Carpet::whereIn('status', [3,13,4,5])->get();
        $central_carpet_area = Carpet::whereIn('status', [1, 2,12])->get();

        $stock = MaterialStock::all();
        if (count($stock) == 0) {
            $firstName = (object) ['material_category' => 'تار پخته'];

            $firstTotal = '0';
            $secondName = (object) ['material_category' => 'تار پشم'];
            $secondTotal = '0';
            $thirdName = (object) ['material_category' => 'تار ابریشم'];
            $thirdTotal = '0';

        } else {
            $ids = MaterialStock::distinct()->get('material_category');
            $id_count = $ids->count();

            if ($id_count == 1){
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $secondName = (object) ['material_category' => 'تار پشم'];
                $secondTotal = '0';
                $thirdName = (object) ['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            }elseif($id_count == 2){
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');
                $thirdName = (object) ['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            }else{
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');

                $third = $ids[2]->material_category;
                $thirdName = MaterialCategory::where('material_category_id', $third)->first('material_category');
                $thirdTotal = MaterialStock::where('material_category', $third)->sum('quantity');
            }




        }

        $loged_user_cashbook = OfficeCashBook::where('user_role', Auth::user()->role)->sum('balance');

        $currency = Currency::getLegacyAFNRate();

        /** talab start */

        $rasid_nomainda_us = AgentPayment::where('type','رسید')->sum('amount');
        $rasid_nomainda_af = AgentPayment::where('type','رسید')->sum('amount_af');
        $rasid_nomainda_total = $rasid_nomainda_us + $rasid_nomainda_af / $currency;

        $rasid_customer_us = CustomerPayment::where('type','رسید')->sum('amount');
        $rasid_customer_af = CustomerPayment::where('type','رسید')->sum('amount_af');
        $rasid_customer_total = $rasid_customer_us + $rasid_customer_af / $currency;

        $rasid_different_account = DifferentAccountPayment::where('type','رسید')->sum('amount');


        $rasid_employee_us = EmployeePayment::where('type','رسید')->sum('amount');
        $rasid_employee_af = EmployeePayment::where('type','رسید')->sum('amount_af');
        $rasid_employee_total = $rasid_employee_us + $rasid_employee_af / $currency;

        $rasid_finishing_us = FinishingTeamPayment::where('type','رسید')->sum('amount');
        $rasid_finishing_af = FinishingTeamPayment::where('type','رسید')->sum('amount_af');
        $rasid_finishing_total = $rasid_finishing_us + $rasid_finishing_af / $currency;

        $rasid_kachaee_us = KachaeePayment::where('type','رسید')->sum('amount');
        $rasid_kachaee_af = KachaeePayment::where('type','رسید')->sum('amount_af');
        $rasid_kachaee_total = $rasid_kachaee_us + $rasid_kachaee_af / $currency;


        $rasid_seller_us = SellerPayment::where('type','رسید')->sum('amount');
        $rasid_seller_af = SellerPayment::where('type','رسید')->sum('amount_af');
        $rasid_seller_total = $rasid_seller_us + $rasid_seller_af / $currency;

        $rasid_washing_us = WashingPayment::where('type','رسید')->sum('amount');
        $rasid_washing_af = WashingPayment::where('type','رسید')->sum('amount_af');
        $rasid_washing_total = $rasid_washing_us + $rasid_washing_af / $currency;

        $rasidat = $rasid_nomainda_total + $rasid_customer_total + $rasid_different_account + $rasid_employee_total + $rasid_finishing_total + $rasid_kachaee_total + $rasid_seller_total + $rasid_washing_total;
        /** talab end */



        /** Gerft ha start */

        $gerft_nomainda_us = AgentPayment::where('type','گرفت')->sum('amount');
        $gerft_nomainda_af = AgentPayment::where('type','گرفت')->sum('amount_af');
        $gerft_nomainda_total = $gerft_nomainda_us + $gerft_nomainda_af / $currency;

        $gerft_customer_us = CustomerPayment::where('type','گرفت')->sum('amount');
        $gerft_customer_af = CustomerPayment::where('type','گرفت')->sum('amount_af');
        $gerft_customer_total = $gerft_customer_us + $gerft_customer_af / $currency;

        $gerft_different_account = DifferentAccountPayment::where('type','گرفت')->sum('amount');


        $gerft_employee_us = EmployeePayment::where('type','گرفت')->sum('amount');
        $gerft_employee_af = EmployeePayment::where('type','گرفت')->sum('amount_af');
        $gerft_employee_total = $gerft_employee_us + $gerft_employee_af / $currency;

        $gerft_finishing_us = FinishingTeamPayment::where('type','گرفت')->sum('amount');
        $gerft_finishing_af = FinishingTeamPayment::where('type','گرفت')->sum('amount_af');
        $gerft_finishing_total = $gerft_finishing_us + $gerft_finishing_af / $currency;

        $gerft_kachaee_us = KachaeePayment::where('type','گرفت')->sum('amount');
        $gerft_kachaee_af = KachaeePayment::where('type','گرفت')->sum('amount_af');
        $gerft_kachaee_total = $gerft_kachaee_us + $gerft_kachaee_af / $currency;


        $gerft_seller_us = SellerPayment::where('type','گرفت')->sum('amount');
        $gerft_seller_af = SellerPayment::where('type','گرفت')->sum('amount_af');
        $gerft_seller_total = $gerft_seller_us + $gerft_seller_af / $currency;

        $gerft_washing_us = WashingPayment::where('type','گرفت')->sum('amount');
        $gerft_washing_af = WashingPayment::where('type','گرفت')->sum('amount_af');
        $gerft_washing_total = $gerft_washing_us + $gerft_washing_af / $currency;

        $gerft_ha = $gerft_nomainda_total + $gerft_customer_total + $gerft_different_account + $gerft_employee_total + $gerft_finishing_total + $gerft_kachaee_total + $gerft_seller_total + $gerft_washing_total;

        /** qarz end */


        $search = 1;

        $all_carpets_for_centeral = Carpet::whereMonth('date',$request->month)->whereYear('date',$request->year)->orderBy('carpet_no','ASC')->get();

        return view('dsh.dashboard', compact('general_cashbook', 'sales_cashbook', 'all_office_cashbook', 'central_carpet_area', 'sale_carpet_area', 'all_carpet_count', 'all_carpet_area', 'ready_to_sale_carpet'
            , 'central_cashbook', 'firstName',
            'firstTotal', 'secondName', 'secondTotal', 'thirdName', 'thirdTotal', 'loged_user_cashbook','all_carpets_for_centeral','rasidat','gerft_ha','search'

        ));

    }
    
    
    public function show_existing_carpet(){
        
       
     
         $existing_carpets  = DB::table('carpets')
            ->join('agents','carpets.agent_id','agents.agent_id')
            ->join('users','agents.user_id','users.id')
            ->join('carpet_orders','carpets.order_id','carpet_orders.id')
            ->join('carpet_types','carpets.type_id','carpet_types.carpet_type_id')
            ->join('qualities','carpets.quality_id','qualities.id')

            ->where('status','!=', 6)
            
            ->where('status','!=', 0)
            ->orderBy('carpet_no','ASC')
            ->paginate(450);

        return view('carpets.existing-carpets', compact('existing_carpets'));

    }
    
    
    public function Index()
    {

        $general_cashbook = OfficeCashBook::where('user_role', 'SP')->sum('balance');
        $central_cashbook = OfficeCashBook::where('user_role', 'CO')->sum('balance');
        $sales_cashbook = OfficeCashBook::where('user_role', 'SO')->sum('balance');
        $all_office_cashbook = $general_cashbook + $central_cashbook + $sales_cashbook;


        $ready_to_sale_carpet = Carpet::where('status', 5)->get();
        $all_carpet_count = Carpet::where('status', '!=', 6)->get();
        $all_carpet_area = Carpet::where('status', '!=', 6)->sum('area');

        $sale_carpet_area = Carpet::whereIn('status', [3,13,4,5])->get();
        $central_carpet_area = Carpet::whereIn('status', [1, 2,12])->get();








        $stock = MaterialStock::all();
        if (count($stock) == 0) {
            $firstName = (object) ['material_category' => 'تار پخته'];

            $firstTotal = '0';
            $secondName = (object) ['material_category' => 'تار پشم'];
            $secondTotal = '0';
            $thirdName = (object) ['material_category' => 'تار ابریشم'];
            $thirdTotal = '0';

        } else {
            $ids = MaterialStock::distinct()->get('material_category');
            $id_count = $ids->count();

            if ($id_count == 1){
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $secondName = (object) ['material_category' => 'تار پشم'];
                $secondTotal = '0';
                $thirdName = (object) ['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            }elseif($id_count == 2){
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');
                $thirdName = (object) ['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            }else{
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');

                $third = $ids[2]->material_category;
                $thirdName = MaterialCategory::where('material_category_id', $third)->first('material_category');
                $thirdTotal = MaterialStock::where('material_category', $third)->sum('quantity');
            }




        }

        $loged_user_cashbook = OfficeCashBook::where('user_role', Auth::user()->role)->sum('balance');

        $currency = Currency::getLegacyAFNRate();

        /** talab start */

        $rasid_nomainda_us = AgentPayment::where('type','رسید')->sum('amount');
        $rasid_nomainda_af = AgentPayment::where('type','رسید')->sum('amount_af');
        $rasid_nomainda_total = $rasid_nomainda_us + $rasid_nomainda_af / $currency;

        $rasid_customer_us = CustomerPayment::where('type','رسید')->sum('amount');
        $rasid_customer_af = CustomerPayment::where('type','رسید')->sum('amount_af');
        $rasid_customer_total = $rasid_customer_us + $rasid_customer_af / $currency;

        $rasid_different_account = DifferentAccountPayment::where('type','رسید')->sum('amount');


        $rasid_employee_us = EmployeePayment::where('type','رسید')->sum('amount');
        $rasid_employee_af = EmployeePayment::where('type','رسید')->sum('amount_af');
        $rasid_employee_total = $rasid_employee_us + $rasid_employee_af / $currency;

        $rasid_finishing_us = FinishingTeamPayment::where('type','رسید')->sum('amount');
        $rasid_finishing_af = FinishingTeamPayment::where('type','رسید')->sum('amount_af');
        $rasid_finishing_total = $rasid_finishing_us + $rasid_finishing_af / $currency;

        $rasid_kachaee_us = KachaeePayment::where('type','رسید')->sum('amount');
        $rasid_kachaee_af = KachaeePayment::where('type','رسید')->sum('amount_af');
        $rasid_kachaee_total = $rasid_kachaee_us + $rasid_kachaee_af / $currency;


        $rasid_seller_us = SellerPayment::where('type','رسید')->sum('amount');
        $rasid_seller_af = SellerPayment::where('type','رسید')->sum('amount_af');
        $rasid_seller_total = $rasid_seller_us + $rasid_seller_af / $currency;

        $rasid_washing_us = WashingPayment::where('type','رسید')->sum('amount');
        $rasid_washing_af = WashingPayment::where('type','رسید')->sum('amount_af');
        $rasid_washing_total = $rasid_washing_us + $rasid_washing_af / $currency;

        $rasidat = $rasid_nomainda_total + $rasid_customer_total + $rasid_different_account + $rasid_employee_total + $rasid_finishing_total + $rasid_kachaee_total + $rasid_seller_total + $rasid_washing_total;
        /** talab end */



        /** Gerft ha start */

        $gerft_nomainda_us = AgentPayment::where('type','گرفت')->sum('amount');
        $gerft_nomainda_af = AgentPayment::where('type','گرفت')->sum('amount_af');
        $gerft_nomainda_total = $gerft_nomainda_us + $gerft_nomainda_af / $currency;

        $gerft_customer_us = CustomerPayment::where('type','گرفت')->sum('amount');
        $gerft_customer_af = CustomerPayment::where('type','گرفت')->sum('amount_af');
        $gerft_customer_total = $gerft_customer_us + $gerft_customer_af / $currency;

        $gerft_different_account = DifferentAccountPayment::where('type','گرفت')->sum('amount');


        $gerft_employee_us = EmployeePayment::where('type','گرفت')->sum('amount');
        $gerft_employee_af = EmployeePayment::where('type','گرفت')->sum('amount_af');
        $gerft_employee_total = $gerft_employee_us + $gerft_employee_af / $currency;

        $gerft_finishing_us = FinishingTeamPayment::where('type','گرفت')->sum('amount');
        $gerft_finishing_af = FinishingTeamPayment::where('type','گرفت')->sum('amount_af');
        $gerft_finishing_total = $gerft_finishing_us + $gerft_finishing_af / $currency;

        $gerft_kachaee_us = KachaeePayment::where('type','گرفت')->sum('amount');
        $gerft_kachaee_af = KachaeePayment::where('type','گرفت')->sum('amount_af');
        $gerft_kachaee_total = $gerft_kachaee_us + $gerft_kachaee_af / $currency;


        $gerft_seller_us = SellerPayment::where('type','گرفت')->sum('amount');
        $gerft_seller_af = SellerPayment::where('type','گرفت')->sum('amount_af');
        $gerft_seller_total = $gerft_seller_us + $gerft_seller_af / $currency;

        $gerft_washing_us = WashingPayment::where('type','گرفت')->sum('amount');
        $gerft_washing_af = WashingPayment::where('type','گرفت')->sum('amount_af');
        $gerft_washing_total = $gerft_washing_us + $gerft_washing_af / $currency;

        $gerft_ha = $gerft_nomainda_total + $gerft_customer_total + $gerft_different_account + $gerft_employee_total + $gerft_finishing_total + $gerft_kachaee_total + $gerft_seller_total + $gerft_washing_total;

        /** qarz end */

        $all_carpets_for_centeral = Carpet::orderBy('carpet_no','ASC')->paginate(100);

        return view('dsh.dashboard', compact('general_cashbook', 'sales_cashbook', 'all_office_cashbook', 'central_carpet_area', 'sale_carpet_area', 'all_carpet_count', 'all_carpet_area', 'ready_to_sale_carpet'
            , 'central_cashbook', 'firstName',
            'firstTotal', 'secondName', 'secondTotal', 'thirdName', 'thirdTotal', 'loged_user_cashbook','all_carpets_for_centeral','rasidat','gerft_ha'

        ));

    }
    public function show_all()
    {

        $general_cashbook = OfficeCashBook::where('user_role', 'SP')->sum('balance');
        $central_cashbook = OfficeCashBook::where('user_role', 'CO')->sum('balance');
        $sales_cashbook = OfficeCashBook::where('user_role', 'SO')->sum('balance');
        $all_office_cashbook = $general_cashbook + $central_cashbook + $sales_cashbook;


        $ready_to_sale_carpet = Carpet::where('status', 5)->get();
        $all_carpet_count = Carpet::where('status', '!=', 6)->get();
        $all_carpet_area = Carpet::where('status', '!=', 6)->sum('area');

        $sale_carpet_area = Carpet::whereIn('status', [3,13,4,5])->get();
        $central_carpet_area = Carpet::whereIn('status', [1, 2,12])->get();








        $stock = MaterialStock::all();
        if (count($stock) == 0) {
            $firstName = (object) ['material_category' => 'تار پخته'];

            $firstTotal = '0';
            $secondName = (object) ['material_category' => 'تار پشم'];
            $secondTotal = '0';
            $thirdName = (object) ['material_category' => 'تار ابریشم'];
            $thirdTotal = '0';

        } else {
            $ids = MaterialStock::distinct()->get('material_category');
            $id_count = $ids->count();

            if ($id_count == 1){
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $secondName = (object) ['material_category' => 'تار پشم'];
                $secondTotal = '0';
                $thirdName = (object) ['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            }elseif($id_count == 2){
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');
                $thirdName = (object) ['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            }else{
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');

                $third = $ids[2]->material_category;
                $thirdName = MaterialCategory::where('material_category_id', $third)->first('material_category');
                $thirdTotal = MaterialStock::where('material_category', $third)->sum('quantity');
            }




        }

        $loged_user_cashbook = OfficeCashBook::where('user_role', Auth::user()->role)->sum('balance');

        $currency = Currency::getLegacyAFNRate();

        /** talab start */

        $rasid_nomainda_us = AgentPayment::where('type','رسید')->sum('amount');
        $rasid_nomainda_af = AgentPayment::where('type','رسید')->sum('amount_af');
        $rasid_nomainda_total = $rasid_nomainda_us + $rasid_nomainda_af / $currency;

        $rasid_customer_us = CustomerPayment::where('type','رسید')->sum('amount');
        $rasid_customer_af = CustomerPayment::where('type','رسید')->sum('amount_af');
        $rasid_customer_total = $rasid_customer_us + $rasid_customer_af / $currency;

        $rasid_different_account = DifferentAccountPayment::where('type','رسید')->sum('amount');


        $rasid_employee_us = EmployeePayment::where('type','رسید')->sum('amount');
        $rasid_employee_af = EmployeePayment::where('type','رسید')->sum('amount_af');
        $rasid_employee_total = $rasid_employee_us + $rasid_employee_af / $currency;

        $rasid_finishing_us = FinishingTeamPayment::where('type','رسید')->sum('amount');
        $rasid_finishing_af = FinishingTeamPayment::where('type','رسید')->sum('amount_af');
        $rasid_finishing_total = $rasid_finishing_us + $rasid_finishing_af / $currency;

        $rasid_kachaee_us = KachaeePayment::where('type','رسید')->sum('amount');
        $rasid_kachaee_af = KachaeePayment::where('type','رسید')->sum('amount_af');
        $rasid_kachaee_total = $rasid_kachaee_us + $rasid_kachaee_af / $currency;


        $rasid_seller_us = SellerPayment::where('type','رسید')->sum('amount');
        $rasid_seller_af = SellerPayment::where('type','رسید')->sum('amount_af');
        $rasid_seller_total = $rasid_seller_us + $rasid_seller_af / $currency;

        $rasid_washing_us = WashingPayment::where('type','رسید')->sum('amount');
        $rasid_washing_af = WashingPayment::where('type','رسید')->sum('amount_af');
        $rasid_washing_total = $rasid_washing_us + $rasid_washing_af / $currency;

        $rasidat = $rasid_nomainda_total + $rasid_customer_total + $rasid_different_account + $rasid_employee_total + $rasid_finishing_total + $rasid_kachaee_total + $rasid_seller_total + $rasid_washing_total;
        /** talab end */



        /** Gerft ha start */

        $gerft_nomainda_us = AgentPayment::where('type','گرفت')->sum('amount');
        $gerft_nomainda_af = AgentPayment::where('type','گرفت')->sum('amount_af');
        $gerft_nomainda_total = $gerft_nomainda_us + $gerft_nomainda_af / $currency;

        $gerft_customer_us = CustomerPayment::where('type','گرفت')->sum('amount');
        $gerft_customer_af = CustomerPayment::where('type','گرفت')->sum('amount_af');
        $gerft_customer_total = $gerft_customer_us + $gerft_customer_af / $currency;

        $gerft_different_account = DifferentAccountPayment::where('type','گرفت')->sum('amount');


        $gerft_employee_us = EmployeePayment::where('type','گرفت')->sum('amount');
        $gerft_employee_af = EmployeePayment::where('type','گرفت')->sum('amount_af');
        $gerft_employee_total = $gerft_employee_us + $gerft_employee_af / $currency;

        $gerft_finishing_us = FinishingTeamPayment::where('type','گرفت')->sum('amount');
        $gerft_finishing_af = FinishingTeamPayment::where('type','گرفت')->sum('amount_af');
        $gerft_finishing_total = $gerft_finishing_us + $gerft_finishing_af / $currency;

        $gerft_kachaee_us = KachaeePayment::where('type','گرفت')->sum('amount');
        $gerft_kachaee_af = KachaeePayment::where('type','گرفت')->sum('amount_af');
        $gerft_kachaee_total = $gerft_kachaee_us + $gerft_kachaee_af / $currency;


        $gerft_seller_us = SellerPayment::where('type','گرفت')->sum('amount');
        $gerft_seller_af = SellerPayment::where('type','گرفت')->sum('amount_af');
        $gerft_seller_total = $gerft_seller_us + $gerft_seller_af / $currency;

        $gerft_washing_us = WashingPayment::where('type','گرفت')->sum('amount');
        $gerft_washing_af = WashingPayment::where('type','گرفت')->sum('amount_af');
        $gerft_washing_total = $gerft_washing_us + $gerft_washing_af / $currency;

        $gerft_ha = $gerft_nomainda_total + $gerft_customer_total + $gerft_different_account + $gerft_employee_total + $gerft_finishing_total + $gerft_kachaee_total + $gerft_seller_total + $gerft_washing_total;

        /** qarz end */

        $all_carpets_for_centeral = Carpet::orderBy('carpet_no','ASC')->paginate(1500);




        return view('dsh.dashboard', compact('general_cashbook', 'sales_cashbook', 'all_office_cashbook', 'central_carpet_area', 'sale_carpet_area', 'all_carpet_count', 'all_carpet_area', 'ready_to_sale_carpet'
            , 'central_cashbook', 'firstName',
            'firstTotal', 'secondName', 'secondTotal', 'thirdName', 'thirdTotal', 'loged_user_cashbook','all_carpets_for_centeral','rasidat','gerft_ha'

        ));
    }

    /** function all carpet edit in dashboard */
    public function all_carpet_edit_dashboard($id){
        $orders = CarpetOrder::all();
        $types = CarpetType::all();
        $carpet = Carpet::find($id);

        $agents = Agents::all();

        return view('carpets.edit-all-carpet',compact('carpet','orders','types','agents'));

    }
    /** function for search all carpet dashboard */
    public function search_all_carpet_dashboard(Request $request)
    {


        $general_cashbook = OfficeCashBook::where('user_role', 'SP')->sum('balance');
        $central_cashbook = OfficeCashBook::where('user_role', 'CO')->sum('balance');
        $sales_cashbook = OfficeCashBook::where('user_role', 'SO')->sum('balance');
        $all_office_cashbook = $general_cashbook + $central_cashbook + $sales_cashbook;


        $ready_to_sale_carpet = Carpet::where('status', 5)->get();
        $all_carpet_count = Carpet::where('status', '!=', 6)->get();
        $all_carpet_area = Carpet::where('status', '!=', 6)->sum('area');

        $sale_carpet_area = Carpet::whereIn('status', [3,13,4,5])->get();
        $central_carpet_area = Carpet::whereIn('status', [1, 2,12])->get();








        $stock = MaterialStock::all();
        if (count($stock) == 0) {
            $firstName = (object) ['material_category' => 'تار پخته'];

            $firstTotal = '0';
            $secondName = (object) ['material_category' => 'تار پشم'];
            $secondTotal = '0';
            $thirdName = (object) ['material_category' => 'تار ابریشم'];
            $thirdTotal = '0';

        } else {
            $ids = MaterialStock::distinct()->get('material_category');
            $id_count = $ids->count();

            if ($id_count == 1){
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $secondName = (object) ['material_category' => 'تار پشم'];
                $secondTotal = '0';
                $thirdName = (object) ['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            }elseif($id_count == 2){
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');
                $thirdName = (object) ['material_category' => 'تار ابریشم'];
                $thirdTotal = '0';

            }else{
                $first = $ids[0]->material_category;
                $firstName = MaterialCategory::where('material_category_id', $first)->first('material_category');
                $firstTotal = MaterialStock::where('material_category', $first)->sum('quantity');

                $second = $ids[1]->material_category;
                $secondName = MaterialCategory::where('material_category_id', $second)->first('material_category');
                $secondTotal = MaterialStock::where('material_category', $second)->sum('quantity');

                $third = $ids[2]->material_category;
                $thirdName = MaterialCategory::where('material_category_id', $third)->first('material_category');
                $thirdTotal = MaterialStock::where('material_category', $third)->sum('quantity');
            }




        }

        $loged_user_cashbook = OfficeCashBook::where('user_role', Auth::user()->role)->sum('balance');

        $currency = Currency::getLegacyAFNRate();

        /** talab start */

        $rasid_nomainda_us = AgentPayment::where('type','رسید')->sum('amount');
        $rasid_nomainda_af = AgentPayment::where('type','رسید')->sum('amount_af');
        $rasid_nomainda_total = $rasid_nomainda_us + $rasid_nomainda_af / $currency;

        $rasid_customer_us = CustomerPayment::where('type','رسید')->sum('amount');
        $rasid_customer_af = CustomerPayment::where('type','رسید')->sum('amount_af');
        $rasid_customer_total = $rasid_customer_us + $rasid_customer_af / $currency;

        $rasid_different_account = DifferentAccountPayment::where('type','رسید')->sum('amount');


        $rasid_employee_us = EmployeePayment::where('type','رسید')->sum('amount');
        $rasid_employee_af = EmployeePayment::where('type','رسید')->sum('amount_af');
        $rasid_employee_total = $rasid_employee_us + $rasid_employee_af / $currency;

        $rasid_finishing_us = FinishingTeamPayment::where('type','رسید')->sum('amount');
        $rasid_finishing_af = FinishingTeamPayment::where('type','رسید')->sum('amount_af');
        $rasid_finishing_total = $rasid_finishing_us + $rasid_finishing_af / $currency;

        $rasid_kachaee_us = KachaeePayment::where('type','رسید')->sum('amount');
        $rasid_kachaee_af = KachaeePayment::where('type','رسید')->sum('amount_af');
        $rasid_kachaee_total = $rasid_kachaee_us + $rasid_kachaee_af / $currency;


        $rasid_seller_us = SellerPayment::where('type','رسید')->sum('amount');
        $rasid_seller_af = SellerPayment::where('type','رسید')->sum('amount_af');
        $rasid_seller_total = $rasid_seller_us + $rasid_seller_af / $currency;

        $rasid_washing_us = WashingPayment::where('type','رسید')->sum('amount');
        $rasid_washing_af = WashingPayment::where('type','رسید')->sum('amount_af');
        $rasid_washing_total = $rasid_washing_us + $rasid_washing_af / $currency;

        $rasidat = $rasid_nomainda_total + $rasid_customer_total + $rasid_different_account + $rasid_employee_total + $rasid_finishing_total + $rasid_kachaee_total + $rasid_seller_total + $rasid_washing_total;
        /** talab end */



        /** Gerft ha start */

        $gerft_nomainda_us = AgentPayment::where('type','گرفت')->sum('amount');
        $gerft_nomainda_af = AgentPayment::where('type','گرفت')->sum('amount_af');
        $gerft_nomainda_total = $gerft_nomainda_us + $gerft_nomainda_af / $currency;

        $gerft_customer_us = CustomerPayment::where('type','گرفت')->sum('amount');
        $gerft_customer_af = CustomerPayment::where('type','گرفت')->sum('amount_af');
        $gerft_customer_total = $gerft_customer_us + $gerft_customer_af / $currency;

        $gerft_different_account = DifferentAccountPayment::where('type','گرفت')->sum('amount');


        $gerft_employee_us = EmployeePayment::where('type','گرفت')->sum('amount');
        $gerft_employee_af = EmployeePayment::where('type','گرفت')->sum('amount_af');
        $gerft_employee_total = $gerft_employee_us + $gerft_employee_af / $currency;

        $gerft_finishing_us = FinishingTeamPayment::where('type','گرفت')->sum('amount');
        $gerft_finishing_af = FinishingTeamPayment::where('type','گرفت')->sum('amount_af');
        $gerft_finishing_total = $gerft_finishing_us + $gerft_finishing_af / $currency;

        $gerft_kachaee_us = KachaeePayment::where('type','گرفت')->sum('amount');
        $gerft_kachaee_af = KachaeePayment::where('type','گرفت')->sum('amount_af');
        $gerft_kachaee_total = $gerft_kachaee_us + $gerft_kachaee_af / $currency;


        $gerft_seller_us = SellerPayment::where('type','گرفت')->sum('amount');
        $gerft_seller_af = SellerPayment::where('type','گرفت')->sum('amount_af');
        $gerft_seller_total = $gerft_seller_us + $gerft_seller_af / $currency;

        $gerft_washing_us = WashingPayment::where('type','گرفت')->sum('amount');
        $gerft_washing_af = WashingPayment::where('type','گرفت')->sum('amount_af');
        $gerft_washing_total = $gerft_washing_us + $gerft_washing_af / $currency;

        $gerft_ha = $gerft_nomainda_total + $gerft_customer_total + $gerft_different_account + $gerft_employee_total + $gerft_finishing_total + $gerft_kachaee_total + $gerft_seller_total + $gerft_washing_total;

        /** qarz end */
        $search = $request->search;

        $all_carpets_for_centeral = Carpet::where('carpet_no', 'like','%'.$search.'%')
            ->orWhere('width', 'like', '%' .$search.'%')
            ->orWhere('height', 'like', '%'.$search.'%')
            ->orWhere('area', 'like', '%'.$search.'%')
            ->orWhere('field', 'like', '%'.$search.'%')
            ->orWhere('margin', 'like', '%'.$search.'%')
            ->orWhere('price', 'like', '%'.$search.'%')
            ->orWhere('total_price_af', 'like', '%'.$search.'%')
            ->orWhere('total_price', 'like', '%'.$search.'%')
            ->orWhere('map_number', 'like', '%'.$search.'%')
            ->orWhere('date', 'like', '%'.$search.'%')
            ->WhereHas('agent', function ($query) use ($search) {
                $query->WhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like','%'.$search.'%');
                });
            })
            ->orWhereHas('carpet_order', function ($query) use ($search) {
                $query->where('order_number', 'like', '%'.$search.'%');
            })

            ->orWhereHas('type', function ($query) use ($search) {
                $query->where('carpet_type', 'like', '%'.$search.'%');
            })
            ->orWhereHas('quality', function ($query) use ($search) {
                $query->where('quality', 'like', '%'.$search.'%');
            })
            ->get();



      
        /** qarz end */
        return view('dsh.dashboard', compact('general_cashbook', 'sales_cashbook', 'all_office_cashbook', 'central_carpet_area', 'sale_carpet_area', 'all_carpet_count', 'all_carpet_area', 'ready_to_sale_carpet'
            , 'central_cashbook', 'firstName',
            'firstTotal', 'secondName', 'secondTotal', 'thirdName', 'thirdTotal', 'loged_user_cashbook','all_carpets_for_centeral','rasidat','gerft_ha','search'

        ));
    }

    /** talab start */
    public function talab_mardom(Request $request)
    {
        $type = $request->query('type');
        $all = '';
        $talab_nomainda = 0;


        $talab_kachaee = 0;

        $talab_shost = 0;

        $talab_tayari = 0;

        $talab_tar_frosh = 0;

        $talab_motafareqa = DifferentAccountTotal::where('remaining', '>', 0)->sum('remaining');

        $talab_froshenda_qalin = 0;
        /** talab end */

        return view('talab-qarz.talab', compact('talab_nomainda', 'talab_kachaee',
            'talab_shost', 'talab_tayari', 'talab_tar_frosh', 'talab_motafareqa',
            'talab_froshenda_qalin', 'all', 'type'

        ));
    }

    /** talabe end */

    /** qarz start */
    public function qarz_mardom(Request $request)
    {
        $type = $request->query('type');
        $all = '';

        $qarz_kharidar_tar_af = 0;

        $qarz_kharidar_qalin =0;

        $qarz_motafareqa = DifferentAccountTotal::where('remaining', '<', 0)->sum('remaining') / -1;



        switch ($type) {

            case 'kharidar_tar' :


                $all = CustomerTotalAccount::whereHas('customer', function ($q) {
                    $q->where('type', 'مشتری تار');
                })->where('remaining', '>', 0)->get();;
                break;
            case 'kharidar_qalin' :

                $all =  CustomerTotalAccount::whereHas('customer', function ($q) {
                    $q->where('type', 'مشتری قالین');
                })->where('remaining', '>', 0)->get();
                break;
            case 'motafareqa' :

                $all = DifferentAccountTotal::where('remaining', '<', 0)->get();
                break;

            default:
                $all = '';


        }

        /** talab end */

        return view('talab-qarz.qarz', compact('qarz_motafareqa', 'qarz_kharidar_tar_af',
            'qarz_kharidar_qalin', 'all', 'type'

        ));
    }
    /** qarz end */


    public function editCurrency(Currency $id)
    {
        return view('dsh.currency-edit', compact('id'));
    }

    public function updateCurrency(Request $request, Currency $id)
    {
        $data = request()->validate([
            'amount' => 'required'
        ]);
        $id->update($data);
        return redirect('/dashboard');
    }
}
