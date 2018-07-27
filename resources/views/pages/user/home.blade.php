@extends('layouts.admin_template')

@section('title', 'Dashboard')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
        </h1>
    </section>

    <!-- Main content -->
    <style>
        section.content div.box div.box-body table#example2 thead tr th {
            width: 19%;
            height: 40px;
            color: red;
            font-weight: bold;
            font-size: 20px;
            border: 1px dotted gray;
        }
        section.content div.box div.box-body table#example2 tbody tr td {
            width: 19%;
            height: 40px;
            color: blue;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            border: 1px dotted gray;
        }
    </style>
    <section class="content container-fluid">
        @if($visma_data != null)
            <div class="box">
            <div class="box-body">
                <div class="row">
                   <div class="box">
                        <div class="box-header">
                          <h3 class="box-title" style="font-size: 25px">Onging Projects</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                          <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                  <th >Project no</th>
                                  <th >Project name</th>
                                  <th >Customer</th>
                                  <th >Start Date</th>
                                  <th >End Date</th>
                                  <th >Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($visma_data as $data)
                                    <tr>
                                      <td>{{$data->Number}}</td>
                                      <td>{{$data->Name}}</td>
                                      <td>{{$data->CustomerName}}</td>
                                      <td>{{$data->StartDate}}</td>
                                      <td>{{$data->EndDate}}</td>
                                      <td>@if ($data->Status == 0) Ongoing @endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                          </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
        @endif
    </section>
    <!-- /.content -->
@endsection