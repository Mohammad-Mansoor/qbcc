@extends('dsh.master')
@section('title' , 'لیست قالین های فروخته شده')
@section('content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">

            <div class="card" id="salePrint">
                <div class="card-header">

                        <h4> گزارش فروشات

                        </h4>

                    <form  action="{{url('/dashboard/get_sales_report')}}"  method="POST" id="search_form">
                        <div class="row">
                            @csrf

                            <div class="col col-lg-3 col-md-3 col-sm-6 col-6">
                                <input type="date" class="form-control" name="from_date" required
                                       placeholder=" تاریخ شروع..." autocomplete="off" id="datePicker_from"/>
                            </div>
                            <div class="col col-lg-3 col-md-3 col-sm-6 col-6">
                                <input type="date" class="form-control" name="to_date" required
                                       placeholder=" تاریخ ختم..." autocomplete="off" id="datePicker_to"/>
                            </div>
                            <div class="col col-lg-2 col-md-3 col-sm-6 col-6">
                                <button class="btn btn-primary btn-sm btn-block"
                                        type="submit" style="    position: absolute;top: 22%; left: -3%;">جستجو
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
                @if($search)
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <h5> گزارش فروشات از تاریخ {{ $from_date }} الی
                                تاریخ {{ $to_date }}</h5>

                        </div>


                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-xs">
                            <thead>
                            <tr>
                                <th>نمبر انوایس</th>

                                <th>نام مشتری</th>
                                <th>نمبر قالین</th>
                                <th>نوعیت</th>
                                <th>کوالتی</th>
                                <th>طول</th>
                                <th>عرض</th>
                                <th>سایز</th>
                                <th>قیمت خرید</th>
                                <th>فی متر مصرف</th>
                                <th>فی متر فروش</th>
                                <th>مجموع فروش</th>

                                    <th>قیمت مجموع تمام شد</th>
                                    <th>مفاد</th>



                            </tr>
                            </thead>
                            <tbody>

                            @php($majmo_tamam_shod = 0)
                            @php($total_sale_price = 0)
                            @php($sizes = 0)
                            @php($profits = 0)



                            @if(isset($search))
                                @foreach($sales as $sale)
                                        <?php $invoice = DB::table('invoices')->where('id',$sale->invoice_id)->first(); ?>
                                        <?php $customer = DB::table('customers')->where('id',$sale->customer_id)->first(); ?>
                                        <?php  $kachaee_expense = DB::table('carpet_repairs')->where('carpetId',$sale->carpet_id)->sum('total_price'); ?>
                                        <?php  $wash_expense = DB::table('carpet_washes')->where('carpetId',$sale->carpet_id)->sum('total_price'); ?>
                                        <?php  $finishing_expense = DB::table('finishing_works')->where('carpetId',$sale->carpet_id)->sum('price'); ?>
                                        <?php  $carpt = \Illuminate\Support\Facades\DB::table('carpets')->where('carpet_id',$sale->carpet_id)->first();?>


                                        <tr>

                                        <td>{{$invoice->invoice_no}}</td>

                                        <td>{{$customer->name}}</td>

                                        <td>{{$carpt->carpet_no}}</td>
                                        <td>{{$sale->type}}</td>
                                        <td>{{$sale->quality}}</td>

                                        <td>{{$sale->carpet_height}} m</td>
                                        <td>{{$sale->carpet_width}} m</td>
                                        <td>{{$sale->carpet_area}} m <sup>2</sup></td>

                                        <td>{{$sale->price}} $</td>
                                        @if($sale->carpet_area > 0)
                                            <td>{{round(($kachaee_expense + $wash_expense +  $finishing_expense) / $sale->carpet_area , 2)}} $</td>
                                        @else
                                            <td>{{round(($kachaee_expense + $wash_expense +  $finishing_expense) / $sale->carpet->area , 2)}} $</td>
                                        @endif

                                        <td>{{$sale->sale_cost_per_meter}} $</td>
                                        <td>{{$sale->carpet_area * $sale->sale_cost_per_meter}} $</td>

                                        @if(auth()->user()->role == 'SP')
                                            <td>{{round((($sale->carpet_area * $sale->price) + $kachaee_expense + $wash_expense +  $finishing_expense) , 2)}} $</td>
                                            <td>{{round(($sale->carpet_area * $sale->sale_cost_per_meter) - ($sale->carpet_area * $sale->price) + $kachaee_expense + $wash_expense +  $finishing_expense,2) }} $</td>

                                        @endif




                                    </tr>

                                    <span style="display: none">{{$majmo_tamam_shod += ($sale->carpet_price_us + $kachaee_expense + $wash_expense +  $finishing_expense) }}</span>
                                    <span style="display: none">{{$total_sale_price += $sale->sale_cost_total}}</span>
                                    <span style="display: none">{{$profits += $sale->profit}}</span>
                                    <span style="display: none">{{$sizes += $sale->carpet_area}}</span>
                                @endforeach
                            @else
                                @foreach($sales as $sale)

                                        <?php  $kachaee_expense = DB::table('carpet_repairs')->where('carpetId',$sale->carpet_id)->sum('total_price'); ?>
                                        <?php  $wash_expense = DB::table('carpet_washes')->where('carpetId',$sale->carpet_id)->sum('total_price'); ?>
                                        <?php  $finishing_expense = DB::table('finishing_works')->where('carpetId',$sale->carpet_id)->sum('price'); ?>
                                    <tr>

                                        <td>{{$sale->invoice->invoice_no}}</td>

                                        <td class="hideOnPrint">{{$sale->customer->name}}</td>

                                        <td>{{$sale->carpet->carpet_no}}</td>
                                        <td>{{$sale->type}}</td>
                                        <td>{{$sale->quality}}</td>
                                        <td>{{$sale->carpet_height}} m</td>
                                        <td>{{$sale->carpet_width}} m</td>
                                        <td>{{$sale->carpet_area}} m <sup>2</sup></td>
                                        <td>{{$sale->carpet->price}} $</td>


                                        @if($sale->carpet_area > 0)
                                            <td>{{round(($kachaee_expense + $wash_expense +  $finishing_expense) / $sale->carpet_area , 2)}} $</td>
                                        @else
                                            <td>{{round(($kachaee_expense + $wash_expense +  $finishing_expense) / $sale->carpet->area , 2)}} $</td>
                                        @endif

                                        <td>{{$sale->sale_cost_per_meter}} $</td>
                                        <td>{{round($sale->carpet_area * $sale->sale_cost_per_meter,2)}} $</td>

                                        @if(auth()->user()->role == 'SP')
                                            <td>{{round((($sale->carpet_area * $sale->carpet->price) + $kachaee_expense + $wash_expense +  $finishing_expense) , 2)}} $</td>
                                            <td>{{round(($sale->carpet_area * $sale->sale_cost_per_meter) - (($sale->carpet_area * $sale->carpet->price) + $kachaee_expense + $wash_expense +  $finishing_expense),2)}} $</td>

                                        @endif
                                        <td class="hideOnPrint"><a href="/dashboard/sales/{{$sale->id}}/edit"
                                                                   class="btn btn-sm btn-info printBTN"><i
                                                    class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>



                                    </tr>

                                    <span style="display: none">{{$majmo_tamam_shod += ($sale->carpet->carpet_price_us + $kachaee_expense + $wash_expense +  $finishing_expense) }}</span>
                                    <span style="display: none">{{$total_sale_price += $sale->sale_cost_total}}</span>
                                    <span style="display: none">{{$profits += $sale->profit}}</span>
                                    <span style="display: none">{{$sizes += $sale->carpet_area}}</span>
                                @endforeach
                            @endif

                            </tbody>
                            <tr>


                                <th></th>
                                <th></th>
                                <th>Pcs {{$sales->count()}}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>{{round($sizes,2)}} m <sup>2</sup></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>{{round($total_sale_price,2)}} $</th>
                                <th>{{round($majmo_tamam_shod,2)}} $</th>
                                <th>{{round($profits,2)}} $</th>
                            </tr>
                        </table>

                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>

@endsection

@section('scripts')

    <script type="text/javascript">
        $(document).ready(function () {


            $('#carpet_height').keyup(function () {
                var carpet_height = $('#carpet_height').val();
                var carpet_width = $('#carpet_width').val();
                var carpet_area = $('#carpet_area').val(carpet_width * carpet_height);
                var total_price = $('#total_price_cost').val();

                $('#price_per_meter').val(total_price / $('#carpet_area').val());


                var sale_cost_per_meter = $("#sale_cost_per_meter").val();
                if (sale_cost_per_meter != null) {
                    $('#sale_cost_total').val(sale_cost_per_meter * $('#carpet_area').val());
                }


            });

            $('#carpet_width').keyup(function () {
                var carpet_height = $('#carpet_height').val();
                var carpet_width = $('#carpet_width').val();
                var carpet_area = $('#carpet_area').val(carpet_width * carpet_height);


                var total_price = $('#total_price_cost').val();

                $('#price_per_meter').val(total_price / $('#carpet_area').val());

                var sale_cost_per_meter = $("#sale_cost_per_meter").val();
                if (sale_cost_per_meter != null) {
                    $('#sale_cost_total').val(sale_cost_per_meter * $('#carpet_area').val());
                }

            });


            // $('#carpet_id').select2();

            $('#invoice_id').change(function () {
                var customer_name = $('#invoice_id option:selected').attr('customer_name');
                $('#customer_name').val(customer_name);

                var customer_company = $('#invoice_id option:selected').attr('customer_company');
                $('#company_name').val(customer_company);

                var company_address = $('#invoice_id option:selected').attr('customer_address');
                $('#company_address').val(company_address);

            });

            // $('#carpet_id').change(function () {
            //     var carpet_type = $('#carpet_id option:selected').attr('carpet_type');
            //     $('#carpet_type').val(carpet_type);
            //
            //     var carpet_quality = $('#carpet_id option:selected').attr('carpet_quality');
            //     $('#carpet_quality').val(carpet_quality);
            //
            //     var carpet_height = $('#carpet_id option:selected').attr('carpet_height');
            //     $('#carpet_height').val(carpet_height);
            //
            //     var carpet_width = $('#carpet_id option:selected').attr('carpet_width');
            //     $('#carpet_width').val(carpet_width);
            //
            //     var carpet_area = $('#carpet_id option:selected').attr('carpet_area');
            //     $('#carpet_area').val(carpet_area);
            //
            //
            //     var total_price = $('#carpet_id option:selected').attr('total_price');
            //     $('#total_price_cost').val(total_price);
            //
            //     // var price_per_meter = $('#carpet_id option:selected').attr('price_per_meter');
            //
            //     $('#price_per_meter').val(total_price / carpet_area);
            //
            //
            // })




            $("#sale_cost_per_meter").keyup(function () {
                var sale_cost_per_meter = $('#sale_cost_per_meter').val();
                var mainCostPM = parseFloat(sale_cost_per_meter).toFixed(2);

                var carpet_area = $('#carpet_area').val();
                var mainCarpetArea = parseFloat(carpet_area).toFixed(2);


                $("#sale_cost_per_meter").val();

                $('#sale_cost_total').val(mainCostPM * mainCarpetArea);


            });

            // $('#invoice_id').onload(function () {
            var customer_name = $('#invoice_id option:selected').attr('customer_name');
            $('#customer_name').val(customer_name);

            var customer_company = $('#invoice_id option:selected').attr('customer_company');
            $('#company_name').val(customer_company);

            var company_address = $('#invoice_id option:selected').attr('customer_address');
            $('#company_address').val(company_address);

            // })
            // $('#carpet_id').onload(function () {
            // var carpet_type = $('#carpet_id option:selected').attr('carpet_type');
            // $('#carpet_type').val(carpet_type);
            //
            // var carpet_quality = $('#carpet_id option:selected').attr('carpet_quality');
            // $('#carpet_quality').val(carpet_quality);
            //
            // var carpet_height = $('#carpet_id option:selected').attr('carpet_height');
            // $('#carpet_height').val(carpet_height);
            //
            // var carpet_width = $('#carpet_id option:selected').attr('carpet_width');
            // $('#carpet_width').val(carpet_width);
            //
            // var carpet_area = $('#carpet_id option:selected').attr('carpet_area');
            // $('#carpet_area').val(carpet_area);


            // var total_price = $('#carpet_id option:selected').attr('total_price');
            // $('#total_price_cost').val(total_price);
            //
            // if (total_price != null && carpet_area != null) {
            //     $('#price_per_meter').val(total_price / carpet_area);
            // }


        });
    </script>
    <script>

        $('.status').show();
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () {

                $(this).remove();
            });
        }, 2000);

    </script>
    <script type="text/javascript">
        $("#packing_id").change(function () {
            $.ajax({
                url: "{{ route('dashboard.package_list.get_by_packing') }}?packing_id=" + $(this).val(),
                method: 'GET',
                success: function (data) {
                    $('#package_id').html(data.html);
                }
            });
        });
    </script>


@endsection
