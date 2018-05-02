@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
        
    </div>
    
    <hr>
    <div class="row justify-content-center">
        
        @if( Auth::check() )
        <!--check if user is an Admin-->
       
            <div class="panel panel-default">
                            <div class="panel-heading">
                               
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                  @if( Auth::user()->isAdmin == 1)
                                    @include('admin.index')
                                    @elseif( Auth::user()->isAdmin == 0)
                                        @include('user.index')
                                    @endif 

                            </div>
                            <!-- /.panel-body -->
                        </div>
           
            @endif 
    
  </div> 
</div>
@endsection
