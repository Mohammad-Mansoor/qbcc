@extends('dsh.master')
@section('title' , 'Carpets')
@section('content')
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h4>ثبت ترمیم قالین</h4>
        </div>
        <div class="card-body">
          <form action="/dashboard/carpet-repair" method="post">
            @csrf
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">نمبر قالین</label>
                  <input type="text" value="{{$id->carpet_no}}" readonly class="form-control">
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">تیم ترمیم کننده</label>
                  <input type="text" value="{{$id->kachaee->name}}" readonly class="form-control">
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">کچایی نمبر</label>
                  <input type="text" name="kachaee_number" placeholder="نمبر کچایی را وارد کنید" class="form-control"
                         value="{{$KachaeeNo}}">
                  @error('kachaee_number') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">قیمت فی متر</label>
                  <input type="text" name="price" placeholder="مصرف ترمیم به افغانی" class="form-control" id="price"
                         value="{{old('price')}}">
                  <input type="hidden" value="{{$currency}}" id="currency">
                  <input type="hidden" value="{{$id->carpet_id}}" name="carpetId">
                  <input type="hidden" value="{{$id->kachaee_id}}" name="team_id">
                  @error('price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">مساحت قالین</label>
                  <input type="text" id="area" name="area" value="{{$id->area}}" readonly class="form-control">
                  @error('area') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label>تاریخ</label>
                  <input type="date" id="repair-date" name="date" placeholder="تاریخ را وارد کنید" class="form-control"
                         value="{{old('date')}}">
                  @error('date') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
             
            </div>
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">قیمت مجموع به افغانی</label>
                  <input type="text" id="af_total_price" name="af_total_price" readonly class="form-control"
                         value="{{old('af_total_price')}}" placeholder="قیمت مجموعی به افغانی">
                  @error('af_total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
  
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <label class="pull-right">قیمت مجموع به دالر</label>
                  <input type="text" id="total_price" name="total_price" readonly class="form-control"
                         value="{{old('total_price')}}" placeholder="قیمت مجموعی به دالر">
                  @error('total_price') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                <div class="form-group fill">
                  <label>توضیحات</label>
                  <textarea name="description" id="description" rows="2" class="form-control"
                            placeholder="توضیحات ترمیم قالین">{{old('description')}}</textarea>
                  @error('description') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                <div class="form-group fill">
                  <a href="/dashboard/carpet-repair" class="btn btn-warning btn-sm" type="reset">انصراف</a>
                  <button class="btn btn-primary marginx btn-sm" type="submit"><span class="fa fa-save"></span> ذخیره</button>
                </div>
              </div>
            </div>
          
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection