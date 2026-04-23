@extends('dsh.master')

@section('content')
  <div id="PaidToDA">

    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-lg-4 col-md-4 col-sm-4 ">
                <table class="table table-sm table-hover text-right">
                  <thead>

                  </thead>
                  <tbody>

                  <tr>
                    <td style="color: dodgerblue;"><b>REPAIRS DETAILS</b></td>
                    <td style="color: dodgerblue;"><b></b></td>

                  </tr>
                  <tr>
                    <td style="direction: ltr"><b>{{$kachaee_number}} - {{$team->name}}</b></td>
                    <td style="direction: ltr">Bill#:</td>
                  </tr>
                  <tr>
                    <td><b>{{Carbon\Carbon::today()->format('Y-m-d')}}</b></td>
                    <td style="direction: ltr">Bill Date:</td>
                  </tr>

                  </tbody>
                </table>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 ">
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 ">
                <table class="table table-sm table-hover text-left">
                  <thead>

                  </thead>
                  <tbody>
                  <tr style="direction: ltr;text-align: left;font-size: 15px;">

                    <td style="color: dodgerblue;"><b>{{$team->name}}</b></td>
                    <td style="color: dodgerblue;"><b>To</b></td>
                  </tr>
                  <tr style="direction: ltr;text-align: left">
                    <td><b>{{$team->name}}</b></td>
                    <td>Name :</td>
                  </tr>
                  <tr style="direction: ltr;text-align: left">
                    <td><b>{{$team->address}}</b></td>
                    <td>Address :</td>
                  </tr>
                  <tr style="direction: ltr;text-align: left">
                    <td><b>{{$team->contact_no}}</b></td>
                    <td>Phone :</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="card-body">
           <div class="row">
             <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
             <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 hideOnPrint">
               <div class="btn btn-primary btn-sm hideOnPrint pull-left" onclick="printPage('PaidToDA')"
                    style="position: relative;float: left;"><i class="fa fa-print"></i> Print
               </div>
             </div>
           </div>

            <div class="table-responsive">
              <hr style="height: 3px;width: 100%;color: #0b97c4;background-color: #0b97c4">
              <table class="table table-sm table-hover" style="direction: ltr;text-align: left">
                <thead>

                <tr>

                  <td><b>ITEM#</b></td>
                  <td><b>ITEM DESCRIPTION</b></td>
                  <td style="direction: rtl;text-align: right"><b>ITEM NOTE</b></td>
                  <td><b>HEIGHT</b></td>
                  <td><b>WIDTH</b></td>
                  <td><b>AREA</b></td>
                  <td><b>UNIT PRICE</b></td>
                  <td><b>TOTAL</b></td>


                </tr>
                </thead>

                <tbody>
                @php($grand_total = 0)
                @php($area = 0)
                @forelse ($carpet_repairs as $repair)
                  <tr style="direction:ltr;">
                    <td>{{$repair->carpet->carpet_no}}</td>
                    <td>{{$repair->carpet->type->carpet_type}}</td>
                    <td style="direction: rtl;text-align: right">{{$repair->description}}</td>
                    <td style="direction: ltr">{{$repair->carpet->height}} m</td>
                    <td style="direction: ltr">{{$repair->carpet->width}} m</td>
                    <td style="direction: ltr"><span style="display: none;">{{$area +=$repair->carpet->area}}</span> {{$repair->carpet->area}} m <sup>2</sup></td>
                    <td style="direction: ltr">{{$repair->price}} af</td>
                    <td><span style="display: none">{{$grand_total += $repair->af_total_price}}</span> {{$repair->af_total_price}} af</td>
                  </tr>
                @empty
                  <h4 class="text-info text-center">هنوز موردی ثبت نشده است</h4>
                @endforelse


                </tbody>
              </table>
              <hr style="height: 3px;color: #0b97c4;background-color: #0b97c4">

          </div>
            <div class="row">

              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">

                <table class="table table-xs table-hover">
                  <tbody>
                  <tr>
                    <td style="direction: ltr"> &nbsp;{{$quantity}} pcs</td>
                    <td style="color: #0b97c4">QUANTITY</td>

                  </tr>
                  <tr>
                    <td style="direction: ltr"> &nbsp;{{$area}} m <sup>2</sup></td>
                    <td style="color: #0b97c4">TOTAL</td>

                  </tr>

                  <tr>
                    <td style="direction: ltr">&nbsp;{{$grand_total}} AF </td>
                    <td style="color: #0b97c4">TOTAL AMOUNT</td>

                  </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">

              </div>

            </div>
        </div>
      </div>
    </div>

  </div>
  </div>

@endsection
