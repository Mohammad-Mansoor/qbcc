@extends('dsh.master')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h5>دفترچه مبایل</h5>
                    <div class="alert alert-success" style="display:none;" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        مخاطب حذف شد
                    </div>
                    @if(session("status"))
                        <div class="alert alert-success status"  style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            {{session('status')}}
                        </div>
                    @endif
                    @if(session("error"))
                        <div class="alert alert-success status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            {{session('error')}}
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="all-form-element-inner">
                        <form id="phone-book" action="/dashboard/phone-book" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="form-group fill">
                                        <label class="login2">نام</label>
                                        <input type="text" name="name" placeholder="لطفا نام تان نوشته کنید" class="form-control"
                                               data-parsley-maxlength="64"
                                               required
                                               data-parsley-required-message="نام الزامی هست"
                                               data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="form-group fill">
                                        <label class="login2"> وظیفه</label>
                                        <input type="text" name="job_title" placeholder="لطفا وظیفه تان نوشته کنید" class="form-control"
                                               data-parsley-maxlength="64"
                                               required
                                               data-parsley-required-message="وظیفه الزامی هست"
                                               data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="form-group fill">
                                        <label class=" pull-right">تلفن</label>
                                        <input type="text" name="phone" placeholder="لطفا شماره تلفن خود را نوشته کنید" class="form-control"
                                               data-parsley-maxlength="12"
                                               required
                                               data-parsley-required-message="نام الزامی هست"
                                               data-parsley-maxlength-message="تعداد حروف بیشتر از 12 حرف نیست.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="form-group fill">
                                        <label class="login2">ایمیل</label>
                                        <input type="text" name="email" placeholder="لطفا ایمیل خود را نوشته کنید" class="form-control"
                                               data-parsley-maxlength="64"
                                               required
                                               value="{{ old('email') }}"
                                               data-parsley-required-message="ایمیل الزامی هست"
                                               data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست."
                                               data-parsley-type="email"
                                               data-parsley-type-message="ایمیل غیر درست، لطفا ایمیل آدرس درست را وارد کنید.">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="form-group fill">
                                        <button class="btn btn-sm btn-primary" type="submit">ثبت</button>
                                        <a class="btn btn-warning btn-sm" href="/dashboard/phone-book" >انصراف</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
                            <form action="/dashboard/phone-books/search" method="post">
                                @csrf
                                <input type="text" name="search" required
                                       placeholder="جستجو" class="form-control">
                            </form>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 hideOnPrint">
                            <div class="btn btn-primary btn-sm hideOnPrint" onclick="printPage('phone_books')"
                                 style="position: relative;float: left"><i class="fa fa-print"></i> Print
            
                            </div>
                         
                        </div>
                    </div>
                    
                </div>
                <div class="card-body" id="phone_books">
                    <div class="static-table-list table-responsive">
                        <table class="table table-hover table-xs">
                            <thead>
                            <tr>
                                <th>آیدی</th>
                                <th>نام</th>
                                <th>وظیفه</th>
                                <th>تلفن</th>
                                <th>ایمیل</th>
                                <th>ویرایش</th>
                                <th>حذف</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($phone_book as $phone)
                                <tr class="ur{{ $phone->phone_book_id }}">
                                    <td>{{$phone->phone_book_id}}</td>
                                    <td>{{$phone->name}}</td>
                                    <td>{{$phone->job_title}}</td>
                                    <td>{{$phone->phone}}</td>
                                    <td>{{$phone->email}}</td>
                                    <td class="hideOnPrint"><a href="/dashboard/phone-book/{{$phone->phone_book_id}}/edit"
                                           class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                                    <td class="hideOnPrint">
                                        <button onclick="RemovePhone({{ $phone->phone_book_id }})"
                                                class="btn btn-danger btn-sm"><i class="fa fa-remove"></i> &nbsp; حذف
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                                <p>{{$phone_book->links()}}</p>
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
        function RemovePhone(id) {
            swal({
                text: "آیا واقعا میخواهی که همی کاربر را حذف کنی؟",
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
                            url: '/dashboard/phone-book/' + id,
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
