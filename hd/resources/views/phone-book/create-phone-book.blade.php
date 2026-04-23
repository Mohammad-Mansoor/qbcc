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
                                <input type="text" placeholder="Search..." class="form-control">

                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <ul class="breadcome-menu">
                            <li><a href="/dashboard">داشبورد</a> <span class="bread-slash">/</span>
                            </li>
                            <li><a href="/dashboard/users">لیست تلفن</a> <span class="bread-slash">/</span>
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
            <h1>ایجاد ریکارد جدید</h1>
        </div>
    </div>
    <div class="sparkline12-graph">
        <div class="basic-login-form-ad">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

    <script>
        $('#phone-book').parsley();
    </script>
@endsection
