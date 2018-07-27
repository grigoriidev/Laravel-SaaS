@extends('layouts.admin_template')

@section('title', 'Applications')

@section('page_level_css')
    <link href="{{ url('/') }}/css/pages/manageapp/index.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/css/pages/manageapp/new.css" rel="stylesheet" type="text/css" />
    
@endsection
@section('page_level_js')
    <script src="{{ url('/') }}/js/pages/manageapp/new.js" type="text/javascript"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            App Settings
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab"><i class="fa fa-key" style="color:#219ddb; font-size: 18px"></i>&nbsp;&nbsp;&nbsp;Credentials</a></li>
              <li><a href="#tab_2" data-toggle="tab"><i class="fa fa-pencil" style="color:#219ddb; font-size: 18px"></i>&nbsp;&nbsp;&nbsp;Rename</a></li>
              <li><a href="#tab_3" data-toggle="tab"><i class="fa fa-remove" style="color:#219ddb; font-size: 18px"></i>&nbsp;&nbsp;&nbsp;&nbspDelete</a></li>
              
              <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <h3><b>Credentials</b></h3>
                <h4>Here you can test the credentials EPTI currently uses to connect to "Visma eAccounting".</h4>
                <br/>
                <button class="btn btn-success" style="color: white;">Test</button>
                <br/>
                <h3><b>Change Credentials</b></h3>
                <h4>Here you can update the credentials EPTI uses to connect to "Visma eAccounting".</h4>
                <br/>
                <h4>To register your Visma eAccounting account please do the following:</h4>
                
                <div style="width:80%" class="ng-scope">
                    <ul>
                        <li style="margin-bottom: 5px;">Press <b>Authenticate</b> to open the Visma eAccounting login page.</li>
                        <li style="margin-bottom: 5px;">Login to Visma eAccounting .</li>
                        <li style="margin-bottom: 5px;"><b>Important!</b> This process requires your browser to allow popup windows. If prompted allow your browser to display pop ups.</li>
                    </ul>
                </div>
                <br/>
                
                <a href="" class="btn btn-success">Authenticate</a>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
                <h3><b>Application Name</b></h3>
                <h4>Here you can change the name of your app.</h4>
                <br/>
                
                {{  Form::open(array('action'=>'AppSettingController@renameApp', 'method' => 'post')) }}  

                    {{  Form::text('appname',$name,['style' => 'width: 30%; height: 35px'])  }}
                    {{  Form::text('app','visma',['style' => 'display: none'])  }}
                    {{  Form::text('id',$id,['style' => 'display: none'])  }}
                    <br/>
                    {{  Form::submit('Save',['class' => 'btn btn-success', 'style' => 'margin-top: 20px;'])  }}

                {{  Form::close()  }} 
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_3">
                <h3><b>Delete Application</b></h3>
                <h4>Here you can delete "Visma eAccounting". Deleted apps will no longer appear in your Applications list.</h4>
                <br/>
                
                <br>
                
                {{  Form::open(array('action'=>'AppSettingController@deleteApp', 'method' => 'post')) }} 

                    {{  Form::text('app','visma',['style' => 'display: none'])  }}
                    {{  Form::text('id',$id,['style' => 'display: none'])  }}
                    {{  Form::submit('Delete',['class' => 'btn btn-danger', 'style' => 'font-size: 15px'])  }}

                {{  Form::close()  }} 
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div>
                </div>
            </div>

        </div>
    </section>
@endsection