@extends('dsh.master')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>ثبت نام کارگر</h4>
                </div>
                <div class="card-body">
                    <div class="all-form-element-inner">
                        <form action="/dashboard/agent-employees" method="post">
                            @csrf
                            <div class="form-group-inner">
                                <div class="row">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                        <select name="agent_id" class="form-control" id="" required>
                                            @foreach($agents as $agent)
                                                <option value="{{$agent->agent_id}}">{{$agent->user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                        <label class="login2">نام نماینده </label>
                                    </div>


                                </div>
                            </div>
                            <div class="form-group-inner">
                                <div class="row">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">

                                        <input type="text" name="first_name" placeholder="نام کارگر را وارد کنید"
                                               required
                                               class="form-control">
                                    </div>

                                    <div class="col-lg-1 col-md-1 col-sm-3 col-xs-12">
                                        <label class="login2">نام </label>
                                    </div>


                                </div>
                            </div>
                            <div class="form-group-inner">
                                <div class="row">

                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="last_name" placeholder="تخلص کارگر را وارد کنید "
                                               required
                                               class="form-control">
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                                        <label class="login2">تخلص </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group-inner">
                                <div class="row">

                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"></div>
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="father_name"
                                               placeholder="نام پدر کارگر را وارد کنید "
                                               required
                                               class="form-control">
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                                        <label class="login2">نام پدر </label>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="agent_id" value="1">
                            <div class="form-group-inner">
                                <div class="login-btn-inner">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <div class="cancel-wp pull-right">
                                                <button class="btn btn-sm btn-info" type="submit"><i
                                                            class="fa fa-save"></i>&nbsp; ثبت
                                                </button>
                                                <a class="btn btn-default"
                                                   href="/dashboard/agent-employees">انصراف</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
