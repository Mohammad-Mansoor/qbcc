@extends('dsh.master')
@section('content')
    <!-- navbar -->

    <!-- form -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if(!$accountEdit)
                        <h4>ثبت اکونت اجناس جدید</h4>
                    @else
                        <h4>ویرایش اکونت اجناس </h4>
                    @endif
                </div>
                <div class="card-body">
                    @if(!$accountEdit)
                        <form method="post" id="" action="/dashboard/assets-accounts">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">اسم اکونت جنس</span>
                                    <input type="text" name="aa_name" class="form-control">
                                    @error('aa_name') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">نوعیت اکونت جنس</span>
                                    <input type="text" name="aa_type" class="form-control">
                                    @error('aa_type') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">تاریخ ثبت اکونت جنس</span>
                                    <input type="date" name="aa_date" class="form-control">
                                    @error('aa_date') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>


                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 20px">
                                    <button class="btn btn-block btn-primary submit-btn" type="submit">ثبت</button>
                                </div>
                            </div>


                        </form>
                    @else
                        <form method="post" id=""
                              action="/dashboard/assets-accounts/{{$accountEdit->aa_id}}">
                            {{method_field('patch')}}
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">اسم جنس</span>
                                    <input type="text" name="aa_name" value="{{$accountEdit->aa_name}}"
                                           class="form-control">
                                    @error('aa_name') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">نوعیت جنس</span>
                                    <input type="text" name="aa_type" value="{{$accountEdit->aa_type}}"
                                           class="form-control">
                                    @error('aa_type') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label">تاریخ ثبت جنس</span>
                                    <input type="date" name="aa_date" value="{{$accountEdit->aa_date}}"
                                           class="form-control">
                                    @error('aa_date') <p
                                    {{--                                        class="text-danger">{{trans('message.'.$message)}}</p>--}}
                                    @enderror
                                </div>


                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top: 20px">
                                    <button class="btn btn-block btn-primary submit-btn" type="submit">ثبت</button>
                                </div>
                            </div>

                        </form>
                        {{--                    @endif--}}

                </div>
            </div>
        </div>

    </div>

    <div class="row" id="accounts">
        <!-- Extra small table start-->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست اجناس</h5>
                    {{--                    @if(session("status"))--}}
                    <div class="alert alert-success status" style="display:none;" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        {{--                            {{session('status')}}--}}
                    </div>

                    {{--                    @endif--}}
                    {{--                    @if(session("error"))--}}

                    <div class="alert alert-danger error" style="display:none;" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        {{session('error')}}
                    </div>

                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-xs table-hover">
                            <thead>
                            <tr>
                                <th>شماره</th>
                                <th>اسم جنس</th>
                                <th>نوعیت جنس</th>
                                <th>تاریخ ثبت جنس</th>
                                <th>جزییات</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($asset_accounts  as $as)

                                <tr>
                                    <td>{{$as->aa_id}}</td>
                                    <td>{{$as->aa_name}}</td>
                                    <td>{{$as->aa_type}}</td>
                                    <td>{{$as->aa_date}}</td>

                                    <td class="hideOnPrint">
                                        <a
                                            href="/dashboard/assets-accounts/{{$as->aa_id}}"
                                            class="btn btn-sm btn-warning">&nbsp;
                                            جزییات</a>
                                    </td>
                                    <td class="hideOnPrint">
                                        @if(auth()->user()->role == 'SP')
                                            <a href="/dashboard/assets-accounts/{{$as->aa_id}}/edit"
                                               class="btn btn-sm btn-info">&nbsp;ویرایش</a>

                                            <button onclick="deleteAssetAccount({{$as->aa_id}})"
                                                    class="btn btn-danger btn-sm ">حذف
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Extra small table start-->
    </div>
@endsection

@section('scripts')
    <script>


        function deleteAssetAccount(id) {

            swal({
                text: "مطمعین هستید ؟",
                buttons: true,
                dangerMode: true,
                buttons: {
                    confirm: {text: 'بلی', className: 'btn-danger'},
                    cancel: 'نخیر'
                },
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'DELETE',
                            data: {
                                '_token': '{{csrf_token()}}',
                            },
                            url: '/dashboard/assets-accounts/' + id,
                            success: function (res) {

                                if (res.status == 'success') {
                                    $('.alert-success').show();
                                    $('.ur' + id).hide();
                                    window.location = '/dashboard/assets-accounts'

                                } else if (res.status == 'error') {
                                    swal("خطا!", res.message, "error");
                                } else {
                                    $('.alert-danger').show();
                                }
                                window.setTimeout(function () {
                                    $(".alert-success").fadeTo(500, 0).slideUp(500, function () {

                                        $(this).remove();
                                    });
                                }, 5000);


                            },

                        })
                    }
                });
        }


    </script>
@endsection

