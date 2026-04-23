@extends('dsh.master')
@section('title' , 'ترانسپورت')
@section('content')
<!-- form -->
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="sparkline12-list">
            <div class="sparkline12-hd">
                <div class="main-sparkline12-hd">
                    @if(!$transportEdit)
                    <h1 class="text-right">ترانسپورت جدید</h1>
                    @else
                    <h1 class="text-right">ویرایش ترانسپورت</h1>
                    @endif
                </div>
            </div>
            <div class="sparkline12-graph">
                <div class="basic-login-form-ad">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="all-form-element-inner">
                                @if(!$transportEdit)
                                <form action="/dashboard/carpet-transport" method="post">
                                    @csrf
                                    <div class="form-group-inner">
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                <input type="text" style="direction: rtl" name="transport_price" id="province" class="form-control">
                                                @error('province') <p class="text-danger">{{$message}}</p> @enderror
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label class="login2 pull-right pull-right-pro">قیمت ترانسپورت</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="login-horizental cancel-wp pull-right">
                                                    <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                                                    <button class="btn btn-sm btn-primary submit-btn"  type="submit">ذخیره</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @else
                                <form action="/dashboard/provinces/{{$provinceEdit->province_id}}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group-inner">
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                <input type="text" style="direction:rtl" name="province" value="{{$provinceEdit->province}}" class="form-control">
                                                @error('province') <p class="text-danger">{{$message}}</p> @enderror
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label class="login2 pull-right pull-right-pro">نام</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="login-horizental cancel-wp pull-right">
                                                    <a href="/dashboard/provinces" class="btn btn-sm btn-default" type="reset">انصراف</a>
                                                    <button class="btn btn-sm btn-primary submit-btn"  type="submit">ذخیره</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-48 col-md-8 col-sm-8 col-xs-12">
        <div class="sparkline12-list">
            <div class="sparkline12-graph">
                <div class="basic-login-form-ad">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="sparkline8-list">
                                <div class="sparkline8-hd">
                                    <div class="main-sparkline8-hd">
                                        <h1 class="text-center">لیست ولایات</h1>
                                            <div class="alert alert-success" style="display:none;" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                        aria-hidden="true">&times;</span></button>
                                            <p class="text-center">ولایت حذف شد</p>
                                        </div>
                                        @if(session("status"))
                                            <div class="alert alert-success status"  style="display:none;" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                <p class="text-center">{{session('status')}}</p>
                                            </div>
                                        @endif
                                        @if(session("error"))
                                            <div class="alert alert-success status" style="display:none;" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                <p class="text-center">{{session('error')}}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="sparkline8-graph">
                                    <div class="static-table-list">
                                        <table class="table">
                                            <thead>
                                                <tr class="text-center">
                                                    {{-- <th class="text-center">آی دی</th> --}}
                                                    <th class="text-center">ولایت</th>
                                                    <th class="text-center">ویرایش</th>
                                                    <th class="text-center">حذف</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($provinces as $province)
                                                    <tr class="ur{{ $province->province_id }} text-center">
                                                        {{-- <td>{{$province->province_id}}</td> --}}
                                                        <td>{{$province->province}}</td>
                                                        <td><a href="/dashboard/provinces/{{$province->province_id}}/edit"
                                                            class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                                                        <td>
                                                            <button onclick="RemoveProvince({{ $province->province_id }})"
                                                                    class="btn btn-danger btn-sm"><i class="fa fa-remove"></i> &nbsp; حذف
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <span class="text-center">{{$provinces->onEachSide(1)->links()}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $('.status').show();
        window.setTimeout(function () {
            $(".status").fadeTo(500, 0).slideUp(500, function () {

                $(this).remove();
            });
        }, 2000);
        function RemoveProvince(id) {
            swal({
                style: "text-center",
                text: "ولایت حذف شود؟",
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
                            method: 'DELETE',
                            data: {'_token': '{{ csrf_token() }}'},
                            url: '/dashboard/provinces/' + id,
                            success: function (data) {
                                $('.ur' + id).hide();
                                $('.alert').show();
                                window.setTimeout(function () {
                                    $(".alert").fadeTo(500, 0).slideUp(500, function () {

                                        $(this).remove();
                                    });
                                }, 2000);
                            }
                        })
                    }
                });
        }
    </script>
@endsection
