@extends('dsh.master')
@section('title' , 'ویرایش پارچه')
@section('content')
  <!-- navbar -->
 
  <!-- @if($errors)
    @foreach ($errors->all() as $error)
      <div>{{ $error }}</div>
  @endforeach
  @endif -->
  <!-- form -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <h5>ویرایش پارچه</h5>
        </div>
        <div class="card-body">
          <div class="card-body">
            <form action="/dashboard/contract-carpet/{{$carpet->carpet_id}}" method="post">
              @method('PATCH')
              @csrf
      
              <input type="hidden" value="1" name="status" id="account_no" class="form-control">
              <input type="hidden" value="edit all carpet" name="edit_all_carpet" class="form-control">
              <input type="hidden" value="{{$currency}}" id="currency_fo_af_convert">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label>نمبر
                      قالین</label>
                    <input type="text" value="{{ $carpet->carpet_no }}" name=""
                           id="" class="form-control" disabled>
                    <small class="text-danger">@error('account_no')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">اسم نماینده</label>
                    <select name="agent_id" id="agent_id" class="form-control">
                      @foreach($agents as $ag)
                        <option {{($ag->agent_id == $carpet->agent_id ? 'selected': '')}} value="{{$ag->agent_id}}">{{$ag->user->name}}
                          &nbsp; {{$ag->account_no}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('agent_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت فی متر به دالر</label>
                    <input type="text" name="price" value="{{$carpet->price}}"
                           class="form-control" id="ppm">
                    @error('price') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">طول</label>
                    <input type="text" name="height" value="{{$carpet->height}}"
                           class="form-control" id="height">
                    @error('height') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">عرض</label>
                    <input type="text" value="{{$carpet->width}}" name="width"
                           class="form-control" id="width">
                    @error('width') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">مساحت</label>
                    <input type="text" name="area" value="{{$carpet->area}}"
                           class="form-control" readonly id="area">
                    @error('area') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به افغانی</label>
                    <input type="hidden" name="oldPrice"
                           value="{{$carpet->carpet_price}}">
                    <input type="hidden" name="carpet_price" id="carpet_price"
                           value="{{$carpet->carpet_price}}">
                    <input type="text" name="total_price_af"
                           value="{{$carpet->total_price_af}}"
                           placeholder="قیمت مجموع به افغانی  " readonly
                           class="form-control" id="total_price_af">
                    @error('total_price_af') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">قیمت به دالر</label>
                    <input type="hidden" name="oldPrice_us"
                           value="{{$carpet->carpet_price_us}}">
                    <input type="hidden" name="carpet_price_us" id="carpet_price_us"
                           value="{{$carpet->carpet_price_us}}">
                    <input type="text" name="total_price"
                           value="{{$carpet->total_price}}" readonly
                           class="form-control" id="total_price">
                    @error('total_price') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
  
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">تاریخ</label>
                    <input type="date" name="date" value="{{$carpet->date}}"
                           class="form-control">
                    @error('date') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
               
             
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">شماره فرمایش</label>
                    <select name="order_id"  class="form-control">
                      <option value="">~~~</option>
                      @foreach($orders as $ord)
                        <option
                                {{ ($ord->id == $carpet->order_id ? 'selected' : '') }}
                                value="{{$ord->id}}">{{$ord->order_number}}</option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('order_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="">نوعیت</label>
                    <select name="type_id" id="type_id" class="form-control">
                      <option value="">~~~</option>
                      @foreach($types as $type)
                        <option
                                {{ ( $type->carpet_type_id == $carpet->type_id ? 'selected' : '') }}
                                value="{{$type->carpet_type_id}}">{{$type->carpet_type}}
                        </option>
                      @endforeach
                    </select>
                    <small class="text-danger">@error('type_id')
                      {{ __('message.'.$message) }} @enderror
                    </small>
                  </div>
                </div>
  
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class=""> کوالتی</label>
                    <select name="quality_id" id="quality_id" required class="form-control">
                      @if($carpet->quality)
                        <option value="{{$carpet->quality->id}}">{{$carpet->quality->quality}}</option>
                      @else
                        <option value=""></option>
                      @endif
                    </select>
                    <small class="text-danger">@error('quality_id') {{ __('message.'.$message) }}@enderror
                    </small>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">نمبر نقشه</label>
                    <input type="text" name="map_number"
                           value="{{$carpet->map_number}}" class="form-control">
                    @error('map_number') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">حاشیه</label>
                    <input type="text" name="margin" value="{{$carpet->margin}}"
                           class="form-control" id="margin">
                    @error('margin') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <label class="pull-right">زمینه</label>
                    <input type="text" name="field" value="{{$carpet->field}}"
                           class="form-control">
                    @error('field') <p class="text-danger">
                      {{trans('message.'.$message)}}</p> @enderror
                  </div>
                </div>
              
      
              </div>
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                  <div class="form-group fill">
                    <button class="btn btn-info btn-sm" type="submit"><i
                              class="fa fa-save"></i> &nbsp; بروز
                    </button>
                    <a class="btn btn-default btn-sm" href="/dashboard">
                      منصرف</a>

                  </div>
                </div>
              </div>
    
            </form>
  
          </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script type="text/javascript">
      $("#type_id").change(function () {
          $.ajax({
              url: "{{ route('dashboard.qualities.get_by_type') }}?type_id=" + $(this).val(),
              method: 'GET',
              success: function (data) {
                  $('#quality_id').html(data.html);
              }
          });
      });
  </script>
@endsection