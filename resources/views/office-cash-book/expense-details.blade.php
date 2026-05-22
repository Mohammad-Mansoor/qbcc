@extends('dsh.master')
@section('title' , 'جزییات مصرف')
@section('content')
    <div id="expensePrint">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            
                            <h4 class="text-c-yellow"> {{ number_format(($debits->original_amount ?: ($debits->amount_af ?: $debits->amount)) - $details->sum('amount'), 2) }}
                                {{ $debits->currency_code ?: ($debits->amount_af ? 'AFN' : 'USD') }}</h4>
                        
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-c-yellow">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="text-white m-b-0">مجموعه مبلغ باقیمانده</h5>
                        </div>
                        <div class="col-3 text-right">
                            <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            
                            <h4 class="text-c-yellow">{{ number_format($details->sum('amount'), 2) }} {{ $debits->currency_code ?: ($debits->amount_af ? 'AFN' : 'USD') }}</h4>
                        
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-c-yellow">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="text-white m-b-0"> مجموعه مبلغ مصرف شده</h5>
                        </div>
                        <div class="col-3 text-right">
                            <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            
                            <h4 class="text-c-yellow">{{ number_format($debits->original_amount ?: ($debits->amount_af ?: $debits->amount), 2) }} {{ $debits->currency_code ?: ($debits->amount_af ? 'AFN' : 'USD') }}</h4>
                        
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather icon-bar-chart-2 f-28"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-c-yellow">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h5 class="text-white m-b-0">  مجموع پول {{$debits->name}}</h5>
                        </div>
                        <div class="col-3 text-right">
                            <i class="feather icon-trending-up text-white f-16"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
            
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="alert alert-success" style="display:none;" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        جزئیات حذف شد
                    </div>
    
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
                    @if(!$paymentEdit)
                        <h5 class="text-right">پرداخت</h5>
                    @else
                        <h5 class="text-right">ویرایش پرداخت</h5>
                    @endif
                    
                </div>
                <div class="card-body">
                    <div class="all-form-element-inner hideOnPrint">
                        @if(!$paymentEdit)
                            <form action="/dashboard/expense-details" method="post">
                                @csrf
                                <input type="hidden" name="expense_id" value="{{$debits->id}}">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="form-group fill">
                                            <div class="row">
                                                
                                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                    <label for="">مقدار</label>
                                                    <input type="text"  style="direction: rtl"
                                                           name="amount"
                                                           id="fp"
                                                           class="form-control">
                                                    @error('amount') <p class="text-danger">
                                                        {{trans('message.'.$message)}}</p> @enderror
        
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-10 col-xs-12">
                                                    <label for="">واحد</label>
                                                    <input type="text" value="{{ $debits->currency_code ?: ($debits->amount_af ? 'AFN' : 'USD') }}" class="form-control" disabled>
                                                </div>
    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="form-group fill">
                                            <label for="">توضیحات</label>
                                             <textarea name="description" id="" cols="5" rows="1"
                                                       class="form-control"></textarea>
                                            <br>
                                            @error('description') <p class="text-danger">
                                                {{trans('message.'.$message)}}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                        <div class="form-group fill">
                                            <div class="login-horizental cancel-wp pull-right">
                                                <button class="btn btn-sm btn-warning" type="reset">انصراف
                                                </button>
                                                <button class="btn btn-sm btn-primary submit-btn"
                                                        type="submit">ذخیره
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                            </form>
                        @else
                            <form action="/dashboard/expense-details/{{$paymentEdit->id}}" method="post">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="expense_id" value="{{$debits->id}}">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="form-group fill">
                                            <div class="row">
                    
                                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                                    <label for="">مقدار</label>
                                                    <input type="text"  style="direction: rtl"
                                                           name="amount" value="{{$paymentEdit->amount}}"
                                                           id="fp"
                                                           class="form-control">
                                                    @error('amount') <p class="text-danger">
                                                        {{trans('message.'.$message)}}</p> @enderror
                    
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-10 col-xs-12">
                                                    <label for="">واحد</label>
                                                    <input type="text" value="{{ $debits->currency_code ?: ($debits->amount_af ? 'AFN' : 'USD') }}" class="form-control" disabled>
                                                </div>
                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <div class="form-group fill">
                                            <label for="">توضیحات</label>
                                            <textarea name="description" id="" cols="5" rows="1"
                                                      class="form-control">{{$paymentEdit->description}}</textarea>
                                            <br>
                                            @error('description') <p class="text-danger">
                                                {{trans('message.'.$message)}}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                        <div class="form-group fill">
                                            <div class="login-horizental cancel-wp pull-right">
                                                <button class="btn btn-sm btn-warning" type="reset">انصراف
                                                </button>
                                                <button class="btn btn-sm btn-primary submit-btn"
                                                        type="submit">ذخیره
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pull-right hideOnPrint"></div>
                     <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pull-right hideOnPrint">
                      <div class="btn btn-sm btn-primary hideOnPrint" style="float: left;"
                           onclick="printPage('expensePrint')"><i class="fa fa-print"></i> Print
                      </div>
            
                     </div>
                    </div>
                    <table class="table table-hover table-xs">
                        <thead>
                        <tr class="text-center">
                            <th class="text-center"> مقدار پول</th>
                            <th class="text-center">توضیحات</th>
                            <th class="text-center hideOnPrint">ویرایش</th>
                            {{-- <th class="text-center hideOnPrint">حذف</th> --}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($details as $r)
                            <tr class=" text-center">
                                <td style="direction: ltr">{{ $debits->currency_code ?: ($debits->amount_af ? 'AFN' : 'USD') }} {{ number_format($r->amount, 2) }}</td>
                                <td>{{$r->description}}</td>
                                <td class="hideOnPrint"><a href="/dashboard/expense-details/{{$r->id}}/edit"
                                       class="btn btn-sm btn-info hideOnPrint"><i
                                                class="fa fa-pencil"></i>&nbsp;
                                        ویرایش</a></td>
                                {{-- <td>
                                    <button onclick="RemovePayment({{ $r->id }})"
                                        class="btn btn-danger btn-sm hideOnPrint"><i class="fa fa-remove"></i> &nbsp;
                                        حذف
                                    </button>
                                </td> --}}
                            </tr>
                        @endforeach
        
                        </tbody>
                    </table>
                    
                    
                </div>
            </div>
        </div>
    </div>
    </div>
 
    <!-- navbar -->
   
   
@endsection
