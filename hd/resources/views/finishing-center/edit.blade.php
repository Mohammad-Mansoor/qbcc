@extends('dsh.master')
@section('title' , 'Editing Works')
@section('content')
<!-- form -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h4 class="pull-right">ویرایش تیاری قالین</h4>
            </div>
            <div class="card-body">
                <div class="all-form-element-inner">
                    <form action="/dashboard/finishing-center/{{$finish->id}}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row" >
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">نمبر قالین</label>
                                    <input type="text"  value="{{$finish->carpet->carpet_no}}" readonly
                                           class="form-control">

                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group fill">
                                    <label class="pull-right">تیاری نمبر</label>
                                    <input type="text" name="finish_number" value="{{$finish->finish_number}}"
                                           class="form-control">
            
                                    <small class="text-danger">@error('finish_number') {{ __('message.'.$message) }} @enderror</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">تیم تیاری</label>
                                    <input type="hidden" value="{{$team->id}}" name="oldTeam">
                                    <select name="team_id" id="team_id" class="form-control">
                                        <option value="{{$team->id}}" selected>{{$team->name}}</option>
                                        @foreach($teams as $team)
                                            <option value="{{$team->id}}">{{$team->name}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">@error('team_id') {{ __('message.'.$message) }} @enderror</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">دسته بندی تیم تیاری</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="{{$category->id}}" selected>{{$category->category}}</option>
                                        @foreach($team_categories as $category)
                                            <option value="{{$category->id}}">{{$category->category}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">@error('category_id') {{ __('message.'.$message) }} @enderror</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">مصرف فی متر یا متر مربع</label>
                                    <input type="text"
                                           class="form-control" name="price_af" id="fp" value="{{$mainPrice_af}}">
                                    <input type="hidden" value="{{$finish->carpetId}}"
                                           name="carpetId">
                                    <input type="hidden" id="mainP" value="{{$mainPrice}}" name="price">
                                    <input type="hidden" id="currency" value="{{$currency}}">
                                    <input type="hidden" value="{{$finish->price}}" name="old_price">
                                    <input type="hidden" value="{{$finish->price_af}}" name="af_old_price">
                                    @error('price') <p class="text-danger">
                                        {{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">تاریخ تیاری قالین</label>
                                    <input type="text" id="repair-date" name="date"
                                           class="form-control"
                                           value="{{$finish->date}}">
                                    @error('date') <p class="text-danger">
                                        {{trans('message.'.$message)}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">طول قالین</label>
                                    <input type="text" name="height" readonly value="{{$newCarpet->height}}" placeholder="مصرف تیاری"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="pull-right">مساحت قالین</label>
                                    <input type="text" name="height" readonly value="{{$newCarpet->area}}" placeholder="مصرف تیاری"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <label class="">شرح</label>
                                    <textarea name="description" id="description" rows="1"
                                              class="form-control">{{$finish->description}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group fill">
                                    <button class="btn btn-warning btn-sm"><a href="/dashboard/finishing-center">
                                            انصراف </a></button>
                                    <button class="btn btn-primary btn-sm marginx" type="submit"> <span
                                                class="fa fa-save"></span> ذخیره</button>
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