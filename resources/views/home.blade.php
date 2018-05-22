@extends('layouts.app')

@section('content')
<div class="container">

    
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
