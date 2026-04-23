@extends('dsh.master')
@section('title' , 'Create New Province')
@section('content')
    <!-- form -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    @if(!$fteamEdit)
                        <h5>ایجاد دسته بندی جدید</h5>
                    @else
                        <h5>ویرایش نام دسته بندی</h5>
                    @endif
                </div>
                <div class="card-body">
                    <div class="all-form-element-inner">
                        @if(!$fteamEdit)
                            <form action="/dashboard/finish-team-category" method="post">
                                @csrf
                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <label class="login2 pull-right pull-right-pro">نام</label>
                                            <input type="text" style="direction: rtl" placeholder="نام" name="category" id="category" class="form-control">
                                            @error('category') <p class="text-danger">{{$message}}</p> @enderror
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <div class="login-horizental cancel-wp pull-right">
                                                <button class="btn btn-sm btn-warning" type="reset">انصراف</button>
                                                <button class="btn btn-sm btn-primary submit-btn"  type="submit">ذخیره</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form action="/dashboard/finish-team-category/{{$fteamEdit->id}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <label class="login2 pull-right pull-right-pro">نام</label>
                                            <input type="text" style="direction: rtl" placeholder="نام" name="category" value="{{$fteamEdit->category}}"  id="category" class="form-control">
                                            @error('category') <p class="text-danger">{{$message}}</p> @enderror
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                            <div class="login-horizental cancel-wp pull-right">
                                                <a href="/dashboard/finish-team-category" class="btn btn-sm btn-warning" type="reset">انصراف</a>
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
            <div class="card">
                <div class="card-header">
                    <h5>لیست دسته بندی های تیم نهایی</h5>
                    <div class="alert alert-success" style="display:none;" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <p class="text-center">دسته بندی حذف شد</p>
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
                <div class="card-body">
                    <div class="static-table-list table-responsive">
                        <table class="table table-hover table-xs">
                            <thead>
                            <tr >
                                <th >آی دی</th>
                                <th >دسته بندی</th>
                                <th >ویرایش</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($categories as $category)
                                <tr class="ur{{ $category->id }}">
                                    <td>{{$category->id}}</td>
                                    <td>{{$category->category}}</td>
                                    <td><a href="/dashboard/finish-team-category/{{$category->id}}/edit"
                                           class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a>
                                    </td>
                                </tr>
                            @empty
                                <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                            @endforelse
                            </tbody>
                        </table>
                        <span class="text-center">{{$categories->onEachSide(1)->links()}}</span>
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
    </script>
@endsection
