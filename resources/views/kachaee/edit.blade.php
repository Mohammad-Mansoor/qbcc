@extends('dsh.master')
@section('title' , 'تیم کچایی')
@section('content')
<!-- navbar -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="breadcome-list single-page-breadcome">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="breadcome-heading">
                        <form role="search" class="">
                            <input type="text" placeholder="Search..." class="form-control">
                            <a href=""><i class="fa fa-search"></i></a>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <ul class="breadcome-menu">
                        <li><a href="/dashboard">داشبورد</a> <span class="bread-slash">/</span>
                        </li>
                        <li><a href="/dashboard/kachaee-team">تیم کچایی</a><span class="bread-blod"> </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="sparkline12-list">
            <div class="sparkline12-hd">
                <div class="row">
                    <div class="col-md-12">
                        <div class="main-sparkline12-hd">
                            <h1>ایجاد تیم جدید</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sparkline12-graph">
                <div class="basic-login-form-ad">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="all-form-element-inner">
                                <form action="/dashboard/kachaee-team/{{$team->id}}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="row" style="display: flex;flex-direction:column">
                                        
                                        <div class="col-md-7">
                                            <div class="form-group-inner" >
                                                <div class="row">
                                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                        <input type="text" value="{{ $team->name }}" id="name" name="name" class="form-control">
                                                        <small class="text-danger">@error('name') {{ __('message.'.$message) }} @enderror</small>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label class="">نام</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group-inner" >
                                                <div class="row">
                                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                        <input type="text" id="father_name" value="{{$team->father_name}}" name="father_name" class="form-control">
                                                        <small class="text-danger">@error('father_name') {{ __('message.'.$message) }} @enderror</small>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label class="">نام پدر</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group-inner" >
                                                <div class="row">
                                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                        <input type="text" id="g_name" name="g_name" value="{{$team->g_name}}" class="form-control">
                                                        <small class="text-danger">@error('g_name') {{ __('message.'.$message) }} @enderror</small>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label class="">نام ضمانت کننده</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group-inner" >
                                                <div class="row">
                                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                        <input type="text" id="grand_father_name" name="grand_father_name" value="{{$team->grand_father_name}}" class="form-control">
                                                        <small class="text-danger">@error('grand_father_name') {{ __('message.'.$message) }} @enderror</small>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label class="">نام پدر کلان</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group-inner" >
                                                <div class="row">
                                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 ">
                                                        <input type="text" id="contact_no" name="contact_no" value="{{$team->contact_no}}" dir="ltr" class="form-control">
                                                        <small class="text-danger">@error('contact_no') {{ __('message.'.$message) }} @enderror</small>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label class="">شماره تماس</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group-inner" >
                                                <div class="row">
                                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 ">
                                                        <input type="text" id="address" name="address" value="{{$team->address}}" dir="rtl" class="form-control">
                                                        <small class="text-danger">@error('address') {{ __('message.'.$message) }} @enderror</small>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label class="">آدرس</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7 ">
                                            <div class="form-group-inner" >
                                                <div class="row">
                                                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                        <input type="text" id="national_id" name="national_id" value="{{$team->national_id}}" class="form-control">
                                                        <small class="text-danger">@error('national_id') {{ __('message.'.$message) }} @enderror</small>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                        <label class="">نمبر تذکره</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="login-horizental cancel-wp pull-left">
                                                <button class="btn btn-white" type="button">انصراف</button>
                                                <button class="btn btn-primary " type="submit"> <span class="fa fa-save"></span> ذخیره</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-plugins')
<script type="text/javascript" src="/dsh/inmask/dist/jquery.inputmask.js"></script>
<script>
    $(document).ready(function(){
        $('#contact_no').inputmask({mask:['(99) 99-999-999']});  //static mask
        // $('#contract_date').persianDatepicker();
    });

</script>
@endsection