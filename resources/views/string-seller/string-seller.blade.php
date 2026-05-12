@extends('dsh.master')
@section('content')
  <!-- navbar -->
  
  <!-- form -->
  <br>
  <div class="row" id="sellers">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card hideOnPrint">
        <div class="card-header">
          @if(!$sellerEdit)
            <h5>ایجاد فروشنده تار</h5>
          @else
            <h5>ویرایش فروشنده تار</h5>
          @endif
        </div>
        <div class="card-body">
          <div class="all-form-element-inner">
            @if(!$sellerEdit)
              <form method="post" id="" action="/dashboard/string-seller">
                @csrf
                <div class="row">
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>نام</label>
                      <input type="text" class="form-control" value="{{old('name')}}" required name="name">
                      @error('name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>شماره تماش</label>
                      <input type="text" class="form-control" value="{{old('phone')}}" required name="phone">
                      @error('name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <div class="form-group fill">
                      <label for="">آدرس</label>
                        <textarea type="text" value="{{old('address')}}" class="form-control" required
                                  rows="1"
                                  name="address"></textarea>
                      @error('company_address') <p class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
                    </div>
                  </div>
                </div>
              </form>
            @else
              <form method="post" id="" action="/dashboard/string-seller/{{$sellerEdit->id}}">
                {{method_field('patch')}}
                @csrf
                <div class="row">
                  
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>نام</label>
                      <input type="text" class="form-control" value="{{$sellerEdit->name}}" required name="name">
                      @error('name') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <label>شماره تماش</label>
                      <input type="text" class="form-control" value="{{$sellerEdit->phone}}" required name="phone">
                      @error('phone') <p class="text-danger">{{trans('message.'.$message)}}</p> @enderror
                    </div>
                  </div>
                  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <div class="form-group fill">
                      <label for="">آدرس</label>
                        <textarea type="text" value="{{old('address')}}" class="form-control" required
                                  rows="1"
                                  name="address">{{$sellerEdit->address}}</textarea>
                      @error('company_address') <p class="text-danger">{{trans('message.'.$message)}}</p>
                      @enderror
                    </div>
                  </div>
                
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <div class="form-group fill">
                      <button class="btn btn-sm btn-default" type="reset">انصراف</button>
                      <button class="btn btn-sm btn-primary submit-btn" type="submit">ثبت</button>
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
          <h5>فروشنده ها</h5>
          <div class="alert alert-success" style="display:none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
            مشتری حذف شد
          </div>
          
          @if(session("status"))
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('status')}}
            </div>
          
          @endif
          @if(session("error"))
            
            <div class="alert alert-success status" style="display:none;" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
              {{session('error')}}
            </div>
          
          @endif
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
                <form action="/dashboard/string-seller/search" method="post">
                  @csrf
                  <input type="text" name="search" required
                         placeholder="جستجو" class="form-control">
                </form>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 hideOnPrint">
                <div class="btn btn-primary btn-sm hideOnPrint" onclick="printPage('sellers')"
                     style="position: relative;float: left"><i class="fa fa-print"></i> Print
  
                </div>
                <a href="/dashboard/sttring-seller-accounts" style="float: left" class="btn btn-sm btn-info hideOnPrint">فروشنده های
                  حسابدار</a>
              </div>
            </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-xs table-hover" style="font-size: 10px; ">
                <thead>
              <tr>
                <th>نام فروشنده</th>
                <th>شماره تماس</th>
                <th>مجموع خرید (kg)</th>
                <th>باقیات حسابداری (AFN)</th>
                <th>باقیات (USD)</th>
                <th>باقیات (AFN)</th>
                <th>آخرین فعالیت</th>
                <th class="hideOnPrint">عملیات</th>
              </tr>
              </thead>
              <tbody>
              @if(!isset($accounts))
                @foreach($sellers as $seller)
                  <tr>
                    <td><strong>{{$seller->name}}</strong><br><small class="text-muted">{{$seller->address}}</small></td>
                    <td>{{$seller->phone}}</td>
                    <td><span class="badge badge-light border">{{number_format($seller->total_supplied, 2)}} kg</span></td>
                    
                    {{-- Accounting Balance --}}
                    @if($seller->accounting_balance > 0)
                      <td style="direction: ltr;color: green;" class="font-weight-bold">{{number_format($seller->accounting_balance, 2)}}</td>
                    @elseif($seller->accounting_balance < 0)
                      <td style="direction: ltr;color: red;" class="font-weight-bold">{{number_format($seller->accounting_balance, 2)}}</td>
                    @else
                      <td style="direction: ltr;">0.00</td>
                    @endif

                    {{-- Legacy USD Balance --}}
                    @if($seller->legacy_usd > 0)
                      <td style="direction: ltr;color: green;">{{number_format($seller->legacy_usd, 2)}}</td>
                    @elseif($seller->legacy_usd < 0)
                      <td style="direction: ltr;color: red;">{{number_format($seller->legacy_usd, 2)}}</td>
                    @else
                      <td style="direction: ltr;">0.00</td>
                    @endif

                    {{-- Legacy AFN Balance --}}
                    @if($seller->legacy_af > 0)
                      <td style="direction: ltr;color: green;">{{number_format($seller->legacy_af, 2)}}</td>
                    @elseif($seller->legacy_af < 0)
                      <td style="direction: ltr;color: red;">{{number_format($seller->legacy_af, 2)}}</td>
                    @else
                      <td style="direction: ltr;">0.00</td>
                    @endif

                    <td>
                        <small>{{ $seller->last_activity ? \Carbon\Carbon::parse($seller->last_activity)->format('Y-m-d') : 'بدون فعالیت' }}</small>
                    </td>
                    
                    <td class="hideOnPrint">
                      <div class="btn-group">
                        <a href="/dashboard/string-seller/{{$seller->id}}/edit"
                           class="btn btn-xs btn-primary" title="ویرایش"><i class="fa fa-edit"></i></a>
                        <a href="/dashboard/string-seller-payments/{{$seller->id}}"
                           class="btn btn-xs btn-info" title="حساب میراثی"><i class="fa fa-list"></i></a>
                        <a href="{{ route('accounting.reports.account_ledger', ['account_id' => 1]) }}?party_type=App\StringSeller&party_id={{$seller->id}}" 
                           class="btn btn-xs btn-success" title="صورت حساب مالی"><i class="fa fa-calculator"></i></a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else
                @foreach($sellers as $seller)
                  
                  
                  @php($total_af = 0)
                  @php($total_usd = 0)

                  <?php

                  $total_af = \Illuminate\Support\Facades\DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'رسید')->sum('amount_af') - \Illuminate\Support\Facades\DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'گرفت')->sum('amount_af');
                  $total_usd = \Illuminate\Support\Facades\DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'رسید')->sum('amount') - \Illuminate\Support\Facades\DB::table('seller_payments')->where('seller_id', $seller->id)->where('type', 'گرفت')->sum('amount');

                  ?>



                  @if($seller->payment->count() > 0 && $total_af !=  0 || $total_usd != 0)
                    <tr>
                      <td>{{ $seller->name}}</td>
                      <td>{{ $seller->phone}}</td>
                      <td>{{ $seller->address}}</td>
  
  
                      {{--for dollars balance--}}
                      @if($total_usd > 0)
                        <td style="direction: ltr;color: green;">{{ $total_usd}}</td>
                      @elseif($total_usd < 0)
                        <td style="direction: ltr;color: red;">{{$total_usd}}</td>
                      @else
                        <td>{{ $total_usd }}</td>
                      @endif
                      {{--end dollars balance--}}
  
                      {{--afghani balance--}}
                      @if($total_af > 0)
                        <td style="direction: ltr;color: green;">{{$total_af }}</td>
                      @elseif($total_af < 0)
                        <td style="direction: ltr;color: red;">{{$total_af }}</td>
                      @else
                        <td>{{ $total_af }}</td>
                      @endif
  
  
                      <td>
                        <a href="/dashboard/string-seller/{{$seller->id}}/edit"
                           class="btn btn-sm btn-primary hideOnPrint">ویرایش</a>
                      </td>
  
                      <td>
                        <a href="/dashboard/string-seller-payments/{{$seller->id}}"
                           class="btn btn-sm btn-primary hideOnPrint">حساب</a>
                      </td>
                    
                    </tr>
                  @endif
                @endforeach
              @endif
              
              @if(!isset($search))
                <tr style="background: gainsboro">
                  
                  <td></td>
                  <td></td>
                  <td></td>
                  @if($credit_us  -  $debit_us  > 0)
                    <td style="font-size: 10px;direction: ltr;color: green">
                      {{$credit_us  -  $debit_us }}</td>
                  @elseif($credit_us - $debit_us < 0)
                    <td style="font-size: 10px;direction: ltr;color: red">
                      {{$credit_us  - $debit_us}}</td>
                  @else
                    <td style="font-size: 10px;direction: ltr;">
                      {{$credit_us - $debit_us }}</td>
                  @endif
  
                  @if($credit_af  -  $debit_af  > 0)
                    <td style="font-size: 10px;direction: ltr;color: green;">
                      {{$credit_af  -  $debit_af}}</td>
                  @elseif($credit_af - $debit_af < 0)
                    <td style="font-size: 10px;direction: ltr;color: red;">
                      {{$credit_af  - $debit_af }}</td>
                  @else
                    <td style="font-size: 10px;direction: ltr;">
                      {{$credit_af  -  $debit_af }}</td>
                  @endif
                  
                  <td>مجموعه</td>
                  
                  <td class="hideOnPrint"></td>
                </tr>
              @endif
              </tbody>
            </table>
            {{-- <span class="text-center">{{$customers->links()}}</span> --}}
          </div>
        </div>
      </div>
    
    
    </div>
  </div>
@endsection

@section('scripts')
  <script>
      $('#form2').hide();


      // remove carpet type function
      $('.status').show();
      window.setTimeout(function () {
          $(".status").fadeTo(500, 0).slideUp(500, function () {

              $(this).remove();
          });
      }, 2000);
  
  </script>
@endsection

