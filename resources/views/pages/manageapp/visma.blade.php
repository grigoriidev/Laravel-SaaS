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
    </style>
    <section class="content container-fluid">
 
                   <div class="box">
                        <div class="box-header">
                          <h3 class="box-title" style="font-size: 28px">Transferred Orders</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                          @if($visma_data == null)
                          <h3 style="text-align: center;">No Data</h3>
                          @else
                            <?php $index = 0 ?>
                            @foreach($visma_data as $visma_data)

                              <div class="box-header">
                                <h3 class="box-title" style="font-size: 22px; color: #3c8dbc;">Entry no.{{$visma_data_title[$index]}}</h3>
                              </div>
                              <table id="example2" class="table table-bordered table-hover visma-table">
                                <thead>
                                    <tr>
                                      <th >Account</th>
                                      <th >Transaction Text</th>
                                      <th >Vat Code</th>
                                      <th >Debit</th>
                                      <th >Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($visma_data as $data)
                                        <tr>
                                          <td style="text-align: left;">{{$data->AccountNumber}}<li class="fa  fa-long-arrow-right"></li>{{$data->AccountDescription}}</td>
                                          <td style="text-align: left;">{{$data->TransactionText}}</td>
                                          <td>{{$data->VatCodeAndPercent}}</td>
                                          <td>{{$data->DebitAmount}}</td>
                                          <td>{{$data->CreditAmount}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                              </table>
                              <?php $index++ ?>
                            @endforeach
                          @endif
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
    
    </section>
    <!-- /.content -->
@endsection





