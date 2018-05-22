<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Capstone') }}</title>



 
    
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="{{ asset('vendor/metisMenu/metisMenu.min.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('dist/css/sb-admin-2.css') }}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

    
    <!-- DataTables CSS -->
    <link href="{{ asset('vendor/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css">

    <!-- DataTables Responsive CSS -->
    <link href="{{ asset('vendor/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet" type="text/css">

    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    
</head>
<body>
    

<div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">Capstone Project Group 1</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li>@if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                       
                        <li>
                            
                        <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fa fa-sign-out fa-fw"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>    
                            
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
                
                  
            </ul>
            <!-- /.navbar-top-links -->

      
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Dashboard</h1>
                        <div class="progress-bar"></div>
                        <div class="loading-progress"></div>
                        <div class="progress">
                            <div id="bulk-action-progbar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width:1%">                 
                            </div>
                        </div>
                       
                        <div class="alert alert-info" role="alert" id="status"></div>
                        @yield('content')
                        
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->    
    
    
    
    
    <!-- jQuery -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="{{ asset('vendor/metisMenu/metisMenu.min.js') }}"></script>

    <!-- Custom Theme JavaScript -->
    <script src="{{ asset('dist/js/sb-admin-2.js') }}"></script>

    <!-- DataTables JavaScript -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/dataTables.responsive.js') }}"></script>
    
    <script src="{{ asset('js/jquery.progressTimer.js') }}"></script>
    
    
    <script>
        /*
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });*/

    $(document).ready(function() {
    var table = $('#working').DataTable( {
        "draw": 1,
        "recordsTotal": 2,
        "recordsFiltered": 2,
        "pagingType": "full_numbers",
        "paging": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        "processing": true,
        "serverSide": false,
        "ajax": "<?php echo url('/').'/allrecords'; ?>",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user.username', name: 'username' },
            { data: 'fName', name: 'fName' },
            { data: 'lName', name: 'lName' },
            { data: 'addr1', name: 'addr1' },
            { data: 'addr2', name: 'addr2' },
            { data: 'city', name: 'city' },
            { data: 'state', name: 'state' },
            { data: 'zip', name: 'zip' },
            { data: 'listName', name: 'listName' },
            { data: 'cashDonation', name: 'cashDonation' },
            { data: 'previousAttendee', name: 'previousAttendee' },
            
        
        ]
        } );
        
     //refresh table every 10 seconds
        setInterval( function () {
            table.ajax.reload(null, false);
        }, 10000 );    

    } );    
        
        
    </script>
    
    <script>  
      $(document).ready(function(){  
           $('#importForm').on("submit", function(e){  
                $('#status').html('Processing...'); 
                var start = new Date().getTime();
               
                //https://www.jqueryscript.net/loading/Ajax-Progress-Bar-Plugin-with-jQuery-Bootstrap-progressTimer.html
                var progress = $(".loading-progress").progressTimer({
                  timeLimit: 10,
                  onFinish: function () {
                  
                }
                });

                e.preventDefault(); //form will not submitted  
               
                var percentComplete = 1;
                $.ajax({
                    
                    async: true,
                     url:"<?php echo url('/').'/import'; ?>",  
                     type:"POST",  
                     data:new FormData(this),  
                     contentType:false,          // The content type used when sending data to the server.  
                     cache:false,                // To unable request pages to be cached  
                     processData:false,          // To send DOMDocument or non processed data file it is set to false  
                     success: function(data){  
                          if(data=='Error1')  
                          {  
                               alert("Invalid File");  
                          }  
                          else if(data == "Error2")  
                          {  
                               alert("Please Select File");  
                          }  
                          else  
                          {    
                               $('#status').html(data);  
                          }  
                     } ,    
                    
                    
                 xhr: function(){
                  var xhr = new window.XMLHttpRequest();
                  //Upload progress, request sending to server
                  xhr.upload.addEventListener("progress", function(e){
                   
                   if (e.lengthComputable) {
       
                       alert(e.loaded);
                      percentComplete = parseInt( (e.loaded / e.total * 100), 10);
                      console.log(percentComplete);
                      $('#bulk-action-progbar').data("aria-valuenow",percentComplete);
                      $('#bulk-action-progbar').css("width",percentComplete+'%');

                    }      
                      
                    $('#status').html('Uploading File...');   
                    $('#status').html('File Uploaded');
               
                   
                  }, false);
                  //Download progress, waiting for response from server
                  xhr.addEventListener("progress", function(e){
                      
                  
                    $('#status').html('Processing the file...'); 
                      
                    if (e.lengthComputable) {
                        
                            
                      //percentComplete = (e.loaded / e.total) * 100;
                      percentComplete = parseInt( (e.loaded / e.total * 100), 10);
   
                      $('#bulk-action-progbar').data("aria-valuenow",percentComplete);
                      $('#bulk-action-progbar').css("width",percentComplete+'%');

                    }
                      
                  
                   
                  }, false);
                  return xhr;
            },
                     
                    
                    
                        complete:function(){
                            $('#status').html('File Processed.');   
                        },
                    
                    
                    
                    
                })  
           });  
      });  
 </script>  
    
</body>
</html>
