@extends('layouts.admin_template')

@section('title', 'Applications')

@section('page_level_css')
    <link href="{{ url('/') }}/css/pages/manageapp/index.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
       <img src="https://www.vismaonline.com/Resources/Images/visma-logo.svg">
    </section>

    <!-- Main content -->
   <style>
        section.content div.box div.box-body table#example2 thead tr th {
            width: 19%;
            height: 40px;
            color: gray;
            font-weight: bold;
            font-size: 18px;
          /*  border: 1px solid lightgray;*/
            text-align: center;
        }
        section.content div.box div.box-body table#example2 tbody tr td {
            width: 19%;
            height: 40px;
            color: black;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            /*border: 1px solid lightgray;*/
            font-family: initial;
        }
        table.visma-table {
          margin-bottom: 30px !important;
        }
        .table-header, .table-row td {
          text-align: center;
        }
    </style>
    <section class="content container-fluid">
 
     <div class="box">
          <div class="box-header">
            <h3 class="box-title" style="font-size: 28px">WooCommerce Orders</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            @if($woocommerce_data == null)
            <h3 style="text-align: center;">No Data</h3>
            @else
            <div class="box-body">
              <table id="woocommerce-table" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th class="table-header">Order</th>
                  <th class="table-header">Status</th>
                  <th class="table-header">Billing</th>
                  <th class="table-header">Send to</th>
                  <th class="table-header">Overall</th>
                </tr>
                </thead>
                <tbody>
                @foreach($woocommerce_data as $data)
                <tr class="table-row">
                  <td style="color:#0073aa;font-size:13px;">#{{$data->id}}{{($data->billing)->first_name}}{{($data->billing)->last_name}}</td>
                  <td>
                    @if($data->status === 'completed')
                      <button class="btn btn-info">{{$data->status}}</button>
                    @endif
                    @if($data->status === 'on-hold')
                      <button class="btn btn-warning">{{$data->status}}</button>
                    @endif
                    @if($data->status === 'processing')
                      <button class="btn btn-success">{{$data->status}}</button>
                    @endif
                    @if($data->status === 'failed')
                      <button class="btn btn-danger">{{$data->status}}</button>
                    @endif
                     @if($data->status === 'cancelled')
                      <button class="btn">{{$data->status}}</button>
                    @endif
                  </td>
                  <td>
                    {{($data->billing)->first_name}}  
                    {{($data->billing)->last_name}},
                    {{($data->billing)->address_1}}
                    {{($data->billing)->address_2}},
                    {{($data->billing)->postcode}}
                    {{($data->billing)->city}}
                    <br/>
                    <b>payment</b>
                    <br/>
                    <i>({{$data->payment_method}})</i>
                  </td>
                  <td style="color:#0073aa;font-size:13px;">
                    {{($data->billing)->first_name}}  
                    {{($data->billing)->last_name}},
                    {{($data->billing)->address_1}}
                    {{($data->billing)->address_2}},
                    {{($data->billing)->postcode}}
                    {{($data->billing)->city}}
                    <br/>
                    {{($data->billing)->email}}
                  </td>
                  <td>
                    {{$data->total}}{{$data->currency}}
                    <br/>
                    <b style="color:#999;"><i>MVA</i></b><br/>
                    {{$data->total_tax}}{{$data->currency}}
                  </td>
                </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                  <th>Rendering engine</th>
                  <th>Browser</th>
                  <th>Platform(s)</th>
                  <th>Engine version</th>
                  <th>CSS grade</th>
                </tr>
                </tfoot>
              </table>
            </div>        
          @endif
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    
    </section>
    
    <!-- /.content -->
@endsection





