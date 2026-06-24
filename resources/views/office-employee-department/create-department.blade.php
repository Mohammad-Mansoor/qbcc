@extends('dsh.master')
@section('title' , 'اضافه کردن دیپارتمنت جدید')
@section('content')
 <!-- navbar -->
<!-- form -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @if(!$departmentEdit)
                    <h5 class="text-right">ایجاد دیپارتمنت جدید</h5>
                @else
                    <h5 class="text-right">ویرایش نام دیپارتمنت</h5>
                @endif
            </div>
            <div class="card-body">
                <div class="all-form-element-inner">
                    @if(!$departmentEdit)
                        @can('create_employee_department')
                        <form action="/dashboard/employee-department" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group fill">
                                        <label class="login2 pull-right pull-right-pro">  نام دیپارتمنت</label>
                                        <input type="text" style="direction: rtl" name="department" id="department" class="form-control">
                                        @error('department') <p class="text-danger">{{$message}}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group fill">
                                        <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                                        <button class="btn btn-sm btn-primary submit-btn"  type="submit">ذخیره</button>
                                    </div>
                                </div>
                            </div>
                          
                        </form>
                        @endcan
                    @else
                        @can('edit_employee_department')
                        <form action="/dashboard/employee-department/{{$departmentEdit->id}}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group fill">
                                        <label class="login2 pull-right pull-right-pro">  نام دیپارتمنت</label>
                                        <input type="text" style="direction: rtl" name="department" value="{{$departmentEdit->department}}" id="department" class="form-control">
                                        @error('department') <p class="text-danger">{{$message}}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group fill">
                                        <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                                        <button class="btn btn-sm btn-primary submit-btn"  type="submit">ذخیره</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endcan
                    @endif
                </div>
                
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="text-center">لیست دیپارتمنت ها</h5>
                <div class="alert alert-success" style="display:none;" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <p class="text-center"> دیپارتمنت حذف شد</p>
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
                        <tr>
                            <th>آی دی</th>
                            <th>دیپارتمنت</th>
                            @can('edit_employee_department')
                            <th>ویرایش</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($departments as $department)
                            <tr class="ur{{ $department->id }}">
                                <td>{{$department->id}}</td>
                                <td>{{$department->department}}</td>
                                @can('edit_employee_department')
                                <td><a href="/dashboard/employee-department/{{$department->id}}/edit"
                                       class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>&nbsp; ویرایش</a></td>
                                @endcan
                            </tr>
                        @empty
                            <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                        @endforelse
                        </tbody>
                    </table>
                    <span class="text-center">{{$departments->onEachSide(1)->links()}}</span>
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


        function RemoveDepartment(id) {
            swal({
                style: "text-center",
                text: "  دیپارتمنت حذف شود؟",
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
                            url: '/dashboard/employee-department/' + id,
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