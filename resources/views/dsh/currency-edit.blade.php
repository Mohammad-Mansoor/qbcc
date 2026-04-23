@extends('dsh.master')
@section('title' , 'Currency Editing')
@section('content')
<!-- form -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h5 class="pull-right">ویرایش نرخ دالر</h5>
            </div>
      
            <div class="card-body">
                <div class="all-form-element-inner">
                    <form action="/dashboard/update-currency/{{$id->id}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="form-group fill">
                                    <label class="pull-right">مبلغ دالر را وارد کنید</label>
                                    <input type="text" class="form-control" name="amount" value="{{$id->amount}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <div class="form-group fill">
                                    <button class="btn btn-primary btn-sm" type="submit"> <span class="fa fa-pencil"></span> بروز کردن</button>
                                    <button class="btn btn-warning btn-sm" type="reset"> <a href="/dashboard"> انصراف</a></button>
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

@section('footer-plugins')
<script type="text/javascript" src="/dsh/inmask/dist/jquery.inputmask.js"></script>
<script>
    $(document).ready(function(){
        $('#phone_no').inputmask({mask:['(99) 99-999-999']});  //static mask
        $('#contract_date').persianDatepicker();

        // price 
        $("#price").blur(function(){
            var price = $('#price').val();
            var mainPrice = parseFloat(price).toFixed(2);
            $("#price").val(mainPrice);
        });
    });

</script>
@endsection