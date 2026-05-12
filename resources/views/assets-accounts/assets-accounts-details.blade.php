@extends('dsh.master')

@section('content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hideOnPrint">

            <div class="card">
                <div class="card-header">
                    <h4>ثبت جنس</h4>
                </div>
                <div class="card-body">

                    @if(!$detailEdit)
                        <form action="/dashboard/assets-accounts-details" method="post" enctype="multipart/form-data">
                            @csrf
                            <br>
                            <input type="hidden" name="assets_account_id" value="{{$account->aa_id}}">

                            <div class="row">

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>اسم جنس</label>
                                    <input type="text" name="asset_name" placeholder="اسم جنس"
                                           class="form-control">
                                    @error('asset_name') <p class="text-danger">
                                        {{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>کلاس جنس</label>
                                    <input type="text" name="asset_class" class="form-control">
                                    @error('asset_class') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>تفصیلات جنس</label>
                                    <input type="text" name="asset_description" class="form-control">
                                    @error('asset_description') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>موقعیت فزیکی جنس</label>
                                    <input type="text" name="physical_location" class="form-control">
                                    @error('asset_class') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>نمبر جنس</label>
                                    <input type="text" name="asset_number" class="form-control">
                                    @error('asset_number') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>سریال جنس</label>
                                    <input type="text" name="asset_serial_number" class="form-control">
                                    @error('asset_serial_number') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>تاریخ خرید جنس</label>
                                    <input type="date" name="acquisition_date" class="form-control">
                                    @error('acquisition_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>قیمت خرید جنس</label>
                                    <input type="text" name="acquisition_cost" id="purchase_cost" class="form-control">
                                    @error('acquisition_cost') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>تعداد سال قابل استفاده</label>
                                    <input type="text" name="estimated_useful_life" id="estimated_useful_life"
                                           class="form-control">
                                    @error('estimated_useful_life') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>تخمین ارزش اسقاط (Salvage Value)</label>
                                    <input type="text" name="estimated_salvage_value" id="estimated_salvage_value"
                                           class="form-control">
                                    <small class="text-muted small">ارزش دستگاه بعد از خرابی.</small>
                                    @error('estimated_salvage_value') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label class="text-primary">استهلاک سالانه (Annual Dep.)</label>
                                    <input type="text" id="annual_depreciation" readonly
                                           class="form-control bg-light text-primary font-weight-bold">
                                    <small class="text-muted small">کاهش ارزش در یک سال.</small>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>عکس جنس (اختیاری)</label>
                                    <input type="file" name="asset_image" class="form-control" accept="image/*">
                                </div>

                                <!-- ACCOUNT OVERRIDES -->
                                <div class="col-lg-12 mt-4">
                                    <div class="row p-3" style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">
                                        <div class="col-lg-12">
                                            <h6 class="mb-3 text-muted"><i class="fa fa-university"></i> تنظیمات حسابی (Fixed Asset Accounting)</h6>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="text-info">حساب دارایی ثابت (Debit)</label>
                                                <select name="override_debit_account_id" id="override_debit_account_id" class="form-control">
                                                    @foreach($allowedDebitAccounts as $acc)
                                                        <option value="{{ $acc->id }}" {{ ($mapping && $mapping->debit_account_id == $acc->id) ? 'selected' : '' }}>
                                                            {{ $acc->account_code }} - {{ $acc->account_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="text-info">حساب پرداخت (Credit)</label>
                                                <select name="override_credit_account_id" id="override_credit_account_id" class="form-control">
                                                    @foreach($allowedCreditAccounts as $acc)
                                                        <option value="{{ $acc->id }}" {{ ($mapping && $mapping->credit_account_id == $acc->id) ? 'selected' : '' }}>
                                                            {{ $acc->account_code }} - {{ $acc->account_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2" style="margin-top: 28px;">
                                            <button class="btn btn-block btn-primary submit-btn" type="submit">
                                                <span class="fa fa-save"></span> ثبت نهایی
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>

                    @else
                        <form action="/dashboard/assets-accounts-details/{{$detailEdit->aad_id}}" method="post"
                              enctype="multipart/form-data">
                            {{method_field('patch')}}
                            @csrf
                            <br>
                            <input type="hidden" name="assets_account_id" value="{{$detailEdit->ajnas_account_id}}">
                            <div class="row">

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>اسم جنس</label>
                                    <input type="text" name="asset_name" placeholder="اسم جنس"
                                           value="{{$detailEdit->asset_name}}"
                                           class="form-control">
                                    @error('asset_name') <p class="text-danger">
                                        {{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>کلاس جنس</label>
                                    <input type="text" name="asset_class" value="{{$detailEdit->asset_class}}"
                                           class="form-control">
                                    @error('asset_class') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>تفصیلات جنس</label>
                                    <input type="text" name="asset_description"
                                           value="{{$detailEdit->asset_description}}" class="form-control">
                                    @error('asset_description') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>موقعیت فزیکی جنس</label>
                                    <input type="text" name="physical_location"
                                           value="{{$detailEdit->physical_location}}" class="form-control">
                                    @error('asset_class') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>نمبر جنس</label>
                                    <input type="text" name="asset_number" value="{{$detailEdit->asset_number}}"
                                           class="form-control">
                                    @error('asset_number') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>سریال جنس</label>
                                    <input type="text" name="asset_serial_number"
                                           value="{{$detailEdit->asset_serial_number}}" class="form-control">
                                    @error('asset_serial_number') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>تاریخ خرید جنس</label>
                                    <input type="date" name="acquisition_date" value="{{$detailEdit->acquisition_date}}"
                                           class="form-control">
                                    @error('acquisition_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>قیمت خرید جنس</label>
                                    <input type="text" name="acquisition_cost" id="purchase_cost"
                                           value="{{$detailEdit->acquisition_cost}}" class="form-control">
                                    @error('acquisition_cost') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>تعداد سال قابل استفاده</label>
                                    <input type="text" name="estimated_useful_life" id="estimated_useful_life"
                                           value="{{$detailEdit->estimated_useful_life}}" class="form-control">
                                    @error('estimated_useful_life') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>تخمین ارزش اسقاط (Salvage Value)</label>
                                    <input type="text" name="estimated_salvage_value" id="estimated_salvage_value_edit"
                                           value="{{$detailEdit->estimated_salvage_value}}"
                                           class="form-control">
                                    @error('estimated_salvage_value') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>@enderror
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label class="text-primary">استهلاک سالانه (Annual Dep.)</label>
                                    <input type="text" id="annual_depreciation_edit" readonly
                                           class="form-control bg-light text-primary font-weight-bold">
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <label>عکس جنس (اختیاری)</label>
                                    <input type="file" name="asset_image" class="form-control" accept="image/*">
                                    @if($detailEdit->asset_image)
                                        <a href="{{ asset('uploads/assets/' . $detailEdit->asset_image) }}" target="_blank" class="small mt-1 d-block text-info"><i class="fa fa-image"></i> مشاهده عکس فعلی</a>
                                    @endif
                                </div>


                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 35px;">

                                    <button class="btn btn-block btn-primary submit-btn"
                                            type="submit"><span
                                            class="fa fa-save"></span> ثبت
                                    </button>
                                </div>

                            </div>

                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <div class="row" id="expensePrint">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                            <h4> جزییات {{$account->aa_name}}</h4>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 hideOnPrint">

                            <div class="btn-group hideOnPrint" style="float:left;">
                                <div class="btn btn-sm btn-primary"
                                     onclick="printPage('expensePrint')"><i
                                        class="fa fa-print"></i> چاپ
                                </div>

                            </div>
                        </div>


                    </div>


                    @if(session("status"))
                        <div class="alert alert-success status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('status')}}</p>
                        </div>

                    @endif
                    @if(session("error"))

                        <div class="alert alert-danger error" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <p class="text-center">{{session('error')}}</p>
                        </div>

                    @endif
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover table-xs" id="expense_list">
                            <thead>
                            <tr>
                                <th>شماره</th>
                                <th>اسم جنس</th>
                                <th>کلاس جنس</th>
                                <th>تفصیلات</th>
                                <th>موقعیت فزیکی</th>
                                <th>نمبر جنس</th>
                                <th>سریال نمبر</th>
                                <th>تاریخ خرید</th>
                                <th>قیمت خرید</th>
                                <th>تعداد سال قابل استفاده</th>
                                <th>تخمین ارزش اسقاط</th>
                                <th>عکس</th>
                                <th>قیمت فعلی</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>


                                <?php
                                $today = \Carbon\Carbon::today()->format('Y');
                                $year = 0;
                                $total_current_value = 0;
                                ?>


                            @foreach($asset_account_details as $co)

                                <tr>
                                    <td>{{$co->aad_id}}</td>
                                    <td>{{$co->asset_name}}</td>
                                    <td>{{$co->asset_class}}</td>
                                    <td>{{$co->asset_description}}</td>
                                    <td>{{$co->physical_location}}</td>
                                    <td>{{$co->asset_number}}</td>
                                    <td>{{$co->asset_serial_number}}</td>
                                    <td>{{$co->acquisition_date}} <span
                                            style="display: none">{{$year = $today - \Carbon\Carbon::parse($co->acquisition_date)->format('Y')}}</span>
                                    </td>
                                    <td>{{$co->acquisition_cost}}</td>
                                    <td>{{$co->estimated_useful_life}}</td>
                                    <td>{{$co->estimated_salvage_value}}</td>
                                    <td>
                                        @if($co->asset_image)
                                            <a href="{{ asset('uploads/assets/' . $co->asset_image) }}" target="_blank"><img src="{{ asset('uploads/assets/' . $co->asset_image) }}" style="width: 40px; height: 40px; border-radius: 5px;" alt="Image"></a>
                                        @else
                                            <span class="text-muted small">ندارد</span>
                                        @endif
                                    </td>
                                    <td>{{$co->acquisition_cost - $year * (($co->acquisition_cost - $co->estimated_salvage_value) / ($co->estimated_useful_life ?: 1)) }}

                                    <span style="display: none;">{{$total_current_value += $co->acquisition_cost - $year * (($co->acquisition_cost - $co->estimated_salvage_value) / ($co->estimated_useful_life ?: 1)) }}</span>
                                    </td>

                                    <td>
                                        @if(auth()->user()->role == 'SP')
                                            <a href="/dashboard/assets-accounts-details/{{$co->aad_id}}/edit"
                                               class="btn btn-sm btn-info">&nbsp;ویرایش</a>

                                            <button onclick="deleteAssetAccountDetails({{$co->aad_id}})"
                                                    class="btn btn-danger btn-sm ">حذف
                                            </button>
                                        @endif
                                    </td>

                                </tr>

                            @endforeach
                                <tr>
                                    <td><b>جمله تعداد جنس</b></td>
                                    <td><b>{{$asset_account_details->count()}}</b></td>
                                    <td colspan="9"></td>
                                    <td>{{$total_current_value}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#override_debit_account_id').select2();
            $('#override_credit_account_id').select2();
        });

        function deleteAssetAccountDetails(id) {

            swal({
                text: "Are you Sure ?",
                buttons: true,
                dangerMode: true,
                buttons: {
                    confirm: {text: 'Yes', className: 'btn-danger'},
                    cancel: 'No'
                },
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'DELETE',
                            data: {
                                '_token': '{{csrf_token()}}',
                            },
                            url: '/dashboard/assets-accounts-details/' + id,
                            success: function (res) {

                                if (res.status == 'success') {
                                    $('.ur' + id).hide();
                                    $('.alert-success').show();
                                    location.reload();
                                } else {
                                    $('.alert-danger').show();
                                }


                                window.setTimeout(function () {
                                    $(".alert-success").fadeTo(500, 0).slideUp(500, function () {

                                        $(this).remove();
                                    });
                                }, 2000);
                            },

                        })
                    }
                });
        }

        function calculateDepreciation() {
            var p = parseFloat($('#purchase_cost').val()) || 0;
            var w = parseFloat($('#estimated_useful_life').val()) || 1;
            var s = parseFloat($('#estimated_salvage_value').val()) || 0;
            if (w > 0) {
                $('#annual_depreciation').val(((p - s) / w).toFixed(2));
            }
        }

        $("#purchase_cost, #estimated_useful_life, #estimated_salvage_value").on("change paste keyup", calculateDepreciation);
        
        function calculateDepreciationEdit() {
            var p = parseFloat($('#purchase_cost').val()) || 0;
            var w = parseFloat($('#estimated_useful_life').val()) || 1;
            var s = parseFloat($('#estimated_salvage_value_edit').val()) || 0;
            if (w > 0) {
                $('#annual_depreciation_edit').val(((p - s) / w).toFixed(2));
            }
        }

        $("#purchase_cost, #estimated_useful_life, #estimated_salvage_value_edit").on("change paste keyup", calculateDepreciationEdit);
        
        // Initial calculation
        calculateDepreciation();
        calculateDepreciationEdit();


    </script>
@endsection

