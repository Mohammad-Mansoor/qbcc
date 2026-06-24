@extends('dsh.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-header">
                 
                    <h5>ایجاد کاربر جدید</h5>
                </div>
                <div class="card-body">
                    <div class="all-form-element-inner">
                        @if(!$editUser)
                            @can('create_user')
                            <form action="/dashboard/users" method="post" id="user-form">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="pull-right">نام</label>
                                            <input type="text" name="name" placeholder="نام تان را وارد کنید"
                                                   data-parsley-maxlength="64"
                                                   required
                                                   data-parsley-required-message="نام الزامی هست"
                                                   data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست."
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">تخلص</label>
                                            <input type="text" name="last_name" placeholder="تخلص تان را وارد کنید "
                                                   data-parsley-maxlength="64"
                                                   required
                                                   data-parsley-required-message="تخلص الزامی هست"
                                                   data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست."
                                                   class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label>نوعیت کاربر</label>
                                            <select name="role" id="" class="form-control"
                                                    required
                                                    data-parsley-required-message="نوعیت کاربر الزامی هست">
                                                @foreach($spatieRoles as $r)
                                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">ایمیل</label>
                                            <input type="text" name="email" placeholder="ایمیل تان را وارد کنید"
                                                   data-parsley-maxlength="64"
                                                   required
                                                   value="{{ old('email') }}"
                                                   data-parsley-required-message="ایمیل الزامی هست"
                                                   data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست."
                                                   data-parsley-type="email"
                                                   data-parsley-type-message="ایمیل غیر درست، لطفا ایمیل آدرس درست را وارد کنید."
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">رمز</label>
                                            <input type="password" id="password" name="password"
                                                   data-parsley-length="[6,32]"
                                                   required
                                                   data-parsley-required-message="رمز خود را وارد نمائید."
                                                   data-parsley-length-message="رمز از ۶ حرف کمتر نباشد"
                                                   placeholder="رمز تان را وارد کنید"
                                                   class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">تایید رمز</label>
                                            <input type="password" id="confirm" name="confirm"
                                                   placeholder="رمز تان را تایید کنید"
                                                   required
                                                   data-parsley-equalto="#password"
                                                   data-parsley-equalto-message="رمز مطابقت نمیکند."
                                                   data-parsley-required-message="لطفا رمز خود را تائید نمائید."
                                                   class="form-control">
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <button class="btn btn-white" type="reset">لغو</button>
                                            <button class="btn btn-sm btn-info" type="submit"><i class="fa fa-save"></i>&nbsp;
                                                ثبت
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @else
                                <div class="alert alert-warning">شما دسترسی ایجاد کاربر جدید را ندارید.</div>
                            @endcan
                        @else
                            @can('edit_user')
                            <form action="/dashboard/users/{{$editUser->id}}" method="post" id="user-form">
                                @method('PATCH')
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">نام</label>
                                            <input type="text" name="name" value="{{$editUser->name}}"
                                                   placeholder="نام تان را وارد کنید"
                                                   data-parsley-maxlength="64"
                                                   required
                                                   data-parsley-required-message="نام الزامی هست"
                                                   data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست."
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">تخلص</label>
                                            <input type="text" name="last_name" value="{{$editUser->last_name}}"
                                                   placeholder="تخلص تان را وارد کنید "
                                                   data-parsley-maxlength="64"
                                                   required
                                                   data-parsley-required-message="تخلص الزامی هست"
                                                   data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست."
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">نوعیت کاربر</label>
                                            <select name="role" id="" class="form-control"
                                                    required
                                                    data-parsley-required-message="نوعیت کاربر الزامی هست">
                                                @foreach($spatieRoles as $r)
                                                    <option value="{{ $r->name }}" {{ ($editUser->role == $r->name ? 'selected' : '') }}>{{ $r->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">ایمیل</label>
                                            <input type="text" value="{{$editUser->email}}" name="email"
                                                   placeholder="ایمیل تان را وارد کنید"
                                                   data-parsley-maxlength="64"
                                                   required
                                                   value="{{ old('email') }}"
                                                   data-parsley-required-message="ایمیل الزامی هست"
                                                   data-parsley-maxlength-message="تعداد حروف بیشتر از ۶۴ حرف نیست."
                                                   data-parsley-type="email"

                                                   data-parsley-type-message="ایمیل غیر درست، لطفا ایمیل آدرس درست را وارد کنید."
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">رمز</label>
                                            <input type="password" value="{{$editUser->password}}" id="password"
                                                   name="password"
                                                   required
                                                   data-parsley-required-message="رمز خود را وارد نمائید."
                                                   data-parsley-length-message="رمز از ۶ حرف کمتر نباشد"
                                                   placeholder="رمز تان را وارد کنید"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <label class="login2 pull-right pull-right-pro">تایید رمز</label>
                                            <input type="password" id="confirm" value="{{$editUser->password}}"
                                                   name="confirm"
                                                   placeholder="رمز تان را تایید کنید"
                                                   required
                                                   data-parsley-equalto="#password"
                                                   data-parsley-equalto-message="رمز مطابقت نمیکند."
                                                   data-parsley-required-message="لطفا رمز خود را تائید نمائید."
                                                   class="form-control">
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="form-group fill">
                                            <button class="btn btn-white" type="reset">لغو</button>
                                            <button class="btn btn-sm btn-success" type="submit">ثبت</button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                            @else
                                <div class="alert alert-warning">شما دسترسی ویرایش کاربر را ندارید.</div>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>لیست کاربران سیستم</h5>
                  

                    
                    <div class="row hideOnPrint">
                        <div class="col-xs-3 col-lg-3 col-md-3 col-sm-3 hideOnPrint">
                            <form action="/dashboard/user-search" method="post">
                                @csrf
                                <input type="text" name="search" required
                                       placeholder="جستجو" class="form-control">
                            </form>
                        </div>
                    </div>
    
                    <div class="alert alert-success" style="display:none;" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        کاربر موفقانه حذف شد
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-xs">
                        <thead>
                        <tr>
                            <th>ای دی</th>
                            <th>نام</th>
                            <th>تخلص</th>
                            <th>مقام</th>
                            <th>ایمیل</th>
                            <th  class="hideOnPrint text-center">عملیات</th>


                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                       
                            <tr class="ur{{ $user->id }}">
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->last_name}}</td>
                                @if($user->role == 'SP')
                                    <td>سوپر ادمین</td>
                                @elseif($user->role == 'SO')
                                    <td>ادمین دفتر فروشات</td>
                                @elseif($user->role == 'CO')
                                    <td>ادمین دفتر مرکزی</td>
                                @elseif($user->role == 'AO')
                                    <td>حساب نماینده</td>
                                @elseif($user->role == 'CCO')
                                    <td>ادمین دفتر مرکزی و مشتریان</td>
                                @elseif($user->role == 'SCO')
                                    <td>ادمین دفتر فروشات و مشتریان</td>
                                @elseif($user->role == 'MO')
                                    <td>ادمین متفرقه</td>
                                @elseif($user->role == 'PH')
                                    <td>حساب عکاس</td>
                                @elseif($user->role == 'OM')
                                    <td>امید</td>
                               @elseif($user->role == 'DE')
                                    <td>یتا انتری</td>
                                 @elseif($user->role == 'FI')
                                    <td>کارمند مالی</td>
                                @endif
                                <td>{{$user->email}}</td>
                                <td class="hideOnPrint text-center">
                                    @can('edit_user')
                                    <a href="/dashboard/users/{{$user->id}}/edit" class="btn btn-sm btn-info"><i
                                                class="fa fa-pencil"></i>&nbsp; ویرایش</a>
                                    @endcan
                                    @can('delete_user')
                                    <button onclick="deleteUser({{$user->id}})"
                                            class="btn btn-danger btn-sm ">حذف
                                    </button>
                                    @endcan
                                </td>
                              
    
                                


                            </tr>
                  
                        @endforeach
                        </tbody>
                    </table>
                   <div class="row">
                       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                           <p>{{$users->links()}}</p>
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
        
        
        function deleteUser(id) {
    
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
                                url: '/dashboard/users/' + id,
                                success: function (res) {
                            
                                    if (res.status == 'success') {
                                        $('.ur' + id).hide();
                                        $('.alert-success').show();
                                       
                                        window.location = '/dashboard/users/'
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