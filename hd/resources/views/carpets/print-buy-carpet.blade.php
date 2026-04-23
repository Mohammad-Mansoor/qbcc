@extends('dsh.master')
@section('title' , 'جزییات قالین')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  <div class="row" style="margin-left : 1px;">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-header">
          <a href="/dashboard/list-buy-carpet" style="float: left" class="btn btn-sm btn-primary hideOnPrint">برگشت</a>
          
          @if(session("status"))
            <div class="alert alert-success status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-danger status text-center" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
        </div>
        <div class="card-body">
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link has-ripple active" id="pills-carpet-details-tab"
                 data-toggle="pill"
                 href="#carpet-details"
                 role="tab" aria-controls="pills-carpet-details" aria-selected="true">مشخصات کلی قالین<span
                        class="ripple ripple-animate"
                        style="height: 71.6719px; width: 71.6719px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -30.8359px; left: 9.16405px;"></span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link has-ripple" id="pills-carpet-checkbook-tab" data-toggle="pill" href="#carpet-checkbook"
                 role="tab" aria-controls="pills-carpet-checkcbook" aria-selected="false">چک بٌک قالین<span
                        class="ripple ripple-animate"
                        style="height: 82.2188px; width: 82.2188px; animation-duration: 0.7s; animation-timing-function: linear; background: rgb(70, 128, 255); opacity: 0.4; top: -15.1094px; left: 18.9375px;"></span></a>
            </li>
          
          
          </ul>
          <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade active show" id="carpet-details" role="tabpanel"
                 aria-labelledby="pills-carpet-details-tab">
              <div class="btn btn-sm btn-primary hideOnPrint pull-right"
                   style="position: relative;top:20px;right:10px;float: left"
                   onclick="printPage('allDetails')"><i class="fa fa-print"></i> Print
              </div>
              
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 sol-xs-12">
                  <h4 style="margin:20px auto">مشخصات عمومی قالین</h4>
                  <div class="static-table-list">
                    <table class="table table-hover table-xs">
                      <thead>
                      <tr>
                        <th style="text-align:right !important">مشخصات</th>
                        <th style="text-align:right !important">مقادیر</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr>
                        <td>اسم فروشنده</td>
                        <td>{{ $carpet->agent->user->name }}</td>
                      </tr>
                      <tr>
                        <td>شماره قالین</td>
                        <td>{{$carpet->carpet_no}}</td>
                      </tr>
                      <tr>
                        <td>شماره فرمایش</td>
                        @if($carpet->carpet_order)
                          <td>{{$carpet->carpet_order->order_number}}</td>
                        @else
                          <td></td>
                        @endif
                      </tr>
                      <tr>
                        <td>طول قالین</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->height }} m</td>
                      </tr>
                      <tr>
                        <td>عرض قالین</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->width }} m</td>
                      </tr>
                      <tr>
                        <td>مساحت قالین</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->area }} m
                          <sup>2</sup></td>
                      </tr>
                      <tr>
                        <td>شماره نقشه قالین</td>
                        <td>{{ $carpet->map_number }}</td>
                      </tr>
                      <tr>
                        <td>زمینه قالین</td>
                        <td>{{ $carpet->field }} </td>
                      </tr>
                      <tr>
                        <td>حاشیه قالین</td>
                        <td>{{ $carpet->margin }}</td>
                      </tr>
                      <tr>
                        <td> قیمت فی متر</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->price }} AF</td>
                      </tr>
                      <tr>
                        <td> قیمت مجموعی افغانی</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->total_price_af }} AF
                        </td>
                      </tr>
                      <tr>
                        <td> قیمت مجموعی دالر</td>
                        <td style="direction: ltr;text-align:right">{{ $carpet->total_price }} $
                        </td>
                      </tr>
                      <tr>
                        <td>تاریخ</td>
                        <td>{{ $carpet->date }}</td>
                      </tr>
                      <tr>
                        <td> حالت</td>
                        @if($carpet->status == 5)
                          <td>تکمیل شده</td>
                        @else
                          <td>زیر کار است</td>
                        @endif
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="carpet-checkbook" role="tabpanel"
                 aria-labelledby="pills-carpet-checkbook-tab">
              <div class="btn btn-sm btn-primary hideOnPrint"
                   style="position: relative;top:20px;float: left;right: 10px;"
                   onclick="printPage('allDetails')"><i class="fa fa-print"></i> Print
              </div>
              <div class="row" id="checkDetails">
                @if (!$carpetCheckBook)
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h4> فورم چک بٌک</h4>
                    <div class="all-form-element-inner">
                      <form action="/dashboard/check-book" method="post">
                        @csrf
                        <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                        <input type="hidden" name="agent_id" value="{{$carpet->agent_id}}">
                        <div class="row" style=" display:flex;justify-content:center">
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">چک بوک نمبر</label>
                              <input type="text" name="check_number" value="{{$CheckNo}}"
                                     placeholder=" نمبر چک بوک قالین " class="form-control"
                                     id="check_number">
                              @error('check_number') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              
                              <label class="pull-right">پول برای کچایی به
                                افغانی</label>
                              <input type="text" name="kachaee_amount" value="{{old('kachaee_amount')}}"
                                     placeholder=" پول برای کچایی" class="form-control"
                                     id="kachaee_amount">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">پول برای کچایی به دالر</label>
                              <input type="text" name="kachaee_dollar_amount"
                                     value="{{old('kachaee_dollar_amount')}}"
                                     placeholder=" پول برای کچایی" class="form-control"
                                     id="kachaee_dollar_amount" readonly>
                              <input type="hidden" value="{{$currency}}"
                                     id="currency">
                            </div>
                          </div>
                          
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right" for="qheight">طول</label>
                              <input type="text"
                                     class="form-control" id="qheight" name="height" value="{{$carpet->height}}" readonly>
                              @error('height') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right" for="qwidth">عرض</label>
                              <input type="text"
                                     class="form-control" value="{{$carpet->width}}" name="width" id="qwidth" readonly>
                              @error('width') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                        
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right" for="area">مساحت</label>
                              <input type="text" name="area"
                                     class="form-control" value="{{$carpet->area}}" id="area" readonly>
                              @error('area') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date" value="{{$carpet->date}}"
                                     placeholder="تاریخ را وارد کنید" class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                         
                        </div>
                        <div class="row">
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <a href="/dashboard/contract-carpet/{{$carpet->carpet_id}}"
                                 class="btn btn-warning btn-sm" type="reset">انصراف</a>
                              <button class="btn btn-primary marginx btn-sm" type="submit">
                                <span class="fa fa-save"></span> ذخیره
                              </button>
                            </div>
                          </div>
                        </div>
                      
                      
                      </form>
                    
                    </div>
                  </div>
                @else
                  <div class="col-sm-12" id="editDetails">
                    <div class="sparkline8-graph text-muted">
                      <h4 style="margin:40px auto; text-align:center">مشخصات عمومی چک بٌک</h4>
                      <div class="static-table-list">
                        <table class="table table-hover table-xs">
                          <thead>
                          
                          
                          <tr>
                            <th style="text-align:right !important">مشخصات</th>
                            <th style="text-align:right !important">مقادیر</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr>
                            <td>شماره قالین</td>
                            <td>{{$carpetCheckBook->carpet->carpet_no}}</td>
                          </tr>
                          <tr>
                            <td>شماره چک بوک</td>
                            <td>{{$carpetCheckBook->check_number}}</td>
                          </tr>
                          <tr>
                            <td> طول قالین</td>
                            <td style="direction: ltr;text-align:right">
                              {{$carpetCheckBook->height}}
                              m
                            </td>
                          </tr>
                          <tr>
                            <td> عرض قالین</td>
                            <td style="direction: ltr;text-align:right">
                              {{$carpetCheckBook->width}}
                              m
                            </td>
                          </tr>
                          <tr>
                            <td> مساحت قالین</td>
                            <td style="direction: ltr;text-align:right">
                              {{$carpetCheckBook->area}}
                              m <sup>2</sup></td>
                          </tr>
                          @if($carpetCheckBook->heightwaste)
                            <tr>
                              <td> طول ضایع شده قالین</td>
                              <td style="direction: ltr;text-align:right">
                                {{$carpetCheckBook->heightwaste}}
                                m
                              </td>
                            </tr>
                          @endif
                          @if($carpetCheckBook->kachaee_amount)
                            <tr>
                              <td> پول اخذ شده برای کجایی</td>
                              <td style="direction: ltr;text-align:right">
                                {{$carpetCheckBook->kachaee_amount}}
                                AF
                              </td>
                            </tr>
                          @endif
                          @if($carpetCheckBook->widthwaste)
                            <tr>
                              <td> عرض ضایع شده قالین</td>
                              <td style="direction: ltr;text-align:right">
                                {{$carpetCheckBook->widthwaste}}
                                m
                              </td>
                            </tr>
                          @endif
                          <tr>
                            <td>تاریخ</td>
                            <td>{{$carpetCheckBook->date}}</td>
                          </tr>
                          <tr class="hideOnPrint">
                            <td>ویرایش</td>
                            <td>
                              <button class="btn btn-info btn-sm" id="editBtn"><i
                                        class="fa fa-pencil"></i> ویرایش
                              </button>
                            </td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12" id="editForm">
                    <h4> فورم ویرایش چک بٌک</h4>
                    <div class="all-form-element-inner">
                      <form action="/dashboard/check-book/{{$carpetCheckBook->id}}" method="post">
                        @csrf
                        @method("PUT")
                        <input type="hidden" name="carpet_id" value="{{$carpet->carpet_id}}">
                        <div class="row" style=" display:flex;justify-content:center">
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">چک بوک نمبر</label>
                              <input type="text" name="check_number"
                                     value="{{$carpetCheckBook->check_number}}"
                                     class="form-control" id="check_number">
                              @error('check_number') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">پول برای کچایی به
                                افغانی</label>
                              <input type="text" name="kachaee_amount"
                                     value="{{$carpetCheckBook->kachaee_amount}}"
                                     class="form-control" id="kachaee_amount">
                              <input type="hidden" name="old_kachaee_amount"
                                     value="{{$carpetCheckBook->kachaee_amount}}">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">پول برای کچایی به دالر</label>
                              <input type="text" name="kachaee_dollar_amount"
                                     value="{{$carpetCheckBook->kachaee_dollar_amount}}"
                                     class="form-control" id="kachaee_dollar_amount"
                                     readonly>
                              <input type="hidden" name="old_kachaee_dollar_amount"
                                     value="{{$carpetCheckBook->kachaee_dollar_amount}}">
                              <input type="hidden" value="{{$currency}}"
                                     id="currency">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">طول</label>
                              <input type="text" name="height"
                                     value="{{$carpetCheckBook->height}}"
                                     class="form-control" id="height">
                              @error('height') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">عرض</label>
                              <input type="text" name="width"
                                     value="{{$carpetCheckBook->width}}" class="form-control"
                                     id="width">
                              @error('width') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">مساحت</label>
                              <input type="text" name="area"
                                     value="{{$carpetCheckBook->area}}" class="form-control"
                                     id="area" readonly>
                              @error('area') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <label class="pull-right">تاریخ</label>
                              <input type="date" name="date"
                                     value="{{$carpetCheckBook->date}}" class="form-control">
                              @error('date') <p class="text-danger">
                                {{trans('message.'.$message)}}</p>
                              @enderror
                            </div>
                          </div>
                       
                         
                        
                        </div>
                        <div class="row">
                          <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
                            <div class="form-group fill">
                              <a href="/dashboard/print-buy-carpet/{{$carpet->carpet_id}}"
                                 class="btn btn-warning btn-sm" type="reset">انصراف</a>
                              <button class="btn btn-primary btn-sm" type="submit">بروز کردن
                              </button>
                            </div>
                          </div>
                        </div>
                      
                      </form>
                    </div>
                  </div>
                
                @endif
              </div>
            </div>
          
          </div>
        </div>
      </div>
      <br>
    
    
    </div>
  
  </div>
@endsection
@section('scripts')
  
  
  <script>

      $(document).ready(function () {

          $('#editForm').hide();

          // DESPLAYING EDIT FORM
          $('#editBtn').click(function () {
              $('#editForm').show();
              $('#editDetails').hide();
          });
      });
      // remove carpet type function
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);

      // chheight
      $("#CH").blur(function () {
          var height = $('#CH').val();
          var mainHeight = parseFloat(height).toFixed(2);
          if (isNaN(mainHeight)) {
              $("#CH").val();
          } else {
              $("#CH").val(mainHeight);
          }

          var qh = $('#qheight').val();
          var sub = qh - mainHeight;
          var hw = parseFloat(sub).toFixed(2);
          $('#heightWaste').val(hw);

      });
      // chwidth
      $("#CW").blur(function () {
          var width = $('#CW').val();
          var mainWidth = parseFloat(width).toFixed(2);
          if (isNaN(mainWidth)) {
              $("#CW").val();
          } else {
              $("#CW").val(mainWidth);
          }

          var qw = $('#qwidth').val();
          var sub = qw - mainWidth;
          var ww = parseFloat(sub).toFixed(2);
          $('#widthWaste').val(ww);

      });
      //KACAHEE RECEIVED IN CHECK-BOOK
      $("#kachaee_amount").blur(function () {
          var c = $('#currency').val();
          var kp = $('#kachaee_amount').val();
          var mainPrice = parseFloat(kp).toFixed(2);
          if (isNaN(mainPrice)) {
              $("#kachaee_amount").val();
          } else {
              $("#kachaee_amount").val(mainPrice);
              var mar = mainPrice / c;
              var usPrice = parseFloat(mar).toFixed(2);
              $('#kachaee_dollar_amount').val(usPrice);
          }
      });

      // Material Amount
      $("#material-amount").blur(function () {
          var ma = $('#material-amount').val();
          var mainma = parseFloat(ma).toFixed(2);
          if (isNaN(mainma)) {
              $("#material-amount").val();
          } else {
              $("#material-amount").val(mainma);
          }
      });
      // Material Price
      $("#material-price").blur(function () {
          var mp = $('#material-price').val();
          var mainmp = parseFloat(mp).toFixed(2);
          if (isNaN(mainmp)) {
              $("#material-price").val();
          } else {
              $("#material-price").val(mainmp);
          }
      });
      //total price
      $("#material-amount,#material-price").blur(function () {
          var mp = $('#material-price').val();
          var midmp = parseFloat(mp).toFixed(2);
          var c = $('#currency').val();
          var mainmp = midmp / c;
          var ma = $('#material-amount').val();
          var mainma = parseFloat(ma).toFixed(2);
          if (mainma != '' && mainmp != '') {
              var t = mainmp * mainma;
              var at = midmp * mainma;
              var total = parseFloat(t).toFixed(2);
              var atotal = parseFloat(at).toFixed(2);
              if (isNaN(total)) {
                  $("#material-total-price").val();

              } else {
                  $("#material-total-price").val(total);
              }
              if (isNaN(atotal)) {
                  $("#material-af-total-price").val();
              } else {
                  $("#material-af-total-price").val(atotal);
              }
          }
      });
  
  
  </script>
@endsection