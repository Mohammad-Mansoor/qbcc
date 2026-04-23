@extends('dsh.master')
@section('content')
    <!-- navbar -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="breadcome-list single-page-breadcome">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="breadcome-heading">
                            <form role="search" class="">
                                <input type="text" placeholder="جستجو ..." class="form-control">
                                <a href=""><i class="fa fa-search"></i></a>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <ul class="breadcome-menu">
                            <li><a href="/dashboard">داشبورد</a> <span class="bread-slash">/</span>
                            </li>
                            <li><a href="/dashboard/users"> فرمایشات</a> <span class="bread-slash">/</span>
                            </li>
                            <li><span class="bread-blod">ایجاد فرمایش جدید</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sparkline12-list">
        <div class="sparkline12-hd">
            <div class="main-sparkline12-hd tx-xs-center">
                <h1>فرمایش</h1>
            </div>
        </div>
        <div class="sparkline12-graph">
            <div class="basic-login-form-ad">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="all-form-element-inner">
                            <form action="/dashboard/carpet-orders" method="post" id="user-form">
                                @csrf
    
                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">

                                            <input type="text" name="order_number" placeholder="نمبر فرمایش  را وارد کنید"
                                         class="form-control">
                                         <small class="text-danger">@error('order_number') {{ __('message.'.$message) }} @enderror</small>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                            <label class="pull-right">نمبر فرمایش</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">

                                            <input type="text" name="height" id="height" placeholder="طول را وارد کنید"
                                           class="form-control">
                                           <small class="text-danger">@error('height') {{ __('message.'.$message) }} @enderror</small>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                            <label class="pull-right"> طول</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">

                                            <input type="text" name="width" id="width" placeholder="عرض  را وارد کنید"
                                                class="form-control">
                                              <small class="text-danger">@error('width') {{ __('message.'.$message) }} @enderror</small>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                            <label class="pull-right">عرض</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">

                                            <input type="text" name="quality" placeholder="کوالتی  را وارد کنید"
                                                class="form-control">
                                                <small class="text-danger">@error('quality') {{ __('message.'.$message) }} @enderror</small>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                            <label class="pull-right">کوالتی</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">

                                            <input type="text" name="background" placeholder="زمینه  را وارد کنید"
                                                  class="form-control">
                                                  <small class="text-danger">@error('background') {{ __('message.'.$message) }} @enderror</small>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                            <label class="pull-right">زمینه</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">

                                            <input type="text" name="border" placeholder="حاشیه  را وارد کنید"
                                                 class="form-control">
                                                 <small class="text-danger">@error('border') {{ __('message.'.$message) }} @enderror</small>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                            <label class="pull-right">حاشیه</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-inner">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">

                                            <input type="number" name="design_number" placeholder="دیزاین نمبر  را وارد کنید"
                                                  class="form-control">
                                                  <small class="text-danger">@error('design_number') {{ __('message.'.$message) }} @enderror</small>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                            <label class="pull-right">دیزاین نمبر</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group-inner">
                                    <div class="login-btn-inner">
                                        <div class="row">
                                         
                                            <div class="col-lg-10">
                                                <div class="cancel-wp pull-right">
                                                    <button class="btn btn-white" type="reset">لغو</button>
                                                    <button class="btn btn-sm btn-info" type="submit"><i class="fa fa-save"></i>&nbsp; ثبت</button>
                                                </div>
                                            </div>
                                            <div class="col-lg-2"></div>
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
@endsection
