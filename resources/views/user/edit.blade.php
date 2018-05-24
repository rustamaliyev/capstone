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
                                  @if( Auth::user()->isAdmin == 0)
          
                                              <form method="post" enctype="multipart/form-data" action="{{action('UserController@update', $userInfo->id)}}">
                                                @csrf
                                               
                                                <div class="row">
                                                  <div class="col-md-12"></div>
                                                  <div class="form-group col-md-6">
                                                    <label for="name">First Name:</label>
                                                    <input required type="text" class="form-control" name="fName" value="{{$userInfo->fName}}">
                                                  </div>
                                                  <div class="form-group col-md-6">
                                                    <label for="name">Last Name:</label>
                                                    <input required type="text" class="form-control" name="lName" value="{{$userInfo->lName}}">
                                                  </div>  
                                                </div>
                                                  
                                                <div class="row">
                                                  <div class="col-md-12"></div>
                                                  <div class="form-group col-md-12">
                                                    <label for="name">Address1:</label>
                                                    <input required type="text" class="form-control" name="addr1" value="{{$userInfo->addr1}}">
                                                  </div>
                                                 
                                                </div>
                                                <div class="row">
                                                  <div class="col-md-12"></div>
                                                  <div class="form-group col-md-12">
                                                    <label for="name">Address2:</label>
                                                    <input type="text" class="form-control" name="addr2" value="{{$userInfo->addr2}}">
                                                  </div>
                                                 
                                                </div> 
                                                 <div class="row">
                                                  <div class="col-md-12"></div>
                                                  <div class="form-group col-md-4">
                                                    <label for="name">City:</label>
                                                    <input required type="text" class="form-control" name="city" value="{{$userInfo->city}}">
                                                  </div>
                                                    <div class="form-group col-md-4">
                                                    <label for="name">State:</label>
                                                    <input required type="text" class="form-control" name="state" value="{{$userInfo->state}}">
                                                  </div>
                                                    <div class="form-group col-md-4">
                                                    <label for="name">Zip:</label>
                                                    <input required type="text" class="form-control" name="zip" value="{{$userInfo->zip}}">
                                                  </div>     
                                                 </div>
                                                  <div class="row">
                                                  <div class="col-md-12"></div>
                                                  <div class="form-group col-md-12">
                                                    <label for="name">Cash Donation:</label>
                                                    <input type="text" class="form-control" name="cashDonation" value="{{$userInfo->cashDonation}}">
                                                  </div>
                                                     
                                                 </div>
                                                  
                                 
                                                <div class="row">
                                                  <div class="col-md-12"></div>
                                                  <div class="form-group col-md-4">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a class="btn btn-default btn-close" href="{{ route('home') }}">Cancel</a>  
                                                  </div>
                                                   
                                                </div>
                                              </form>
                                    @endif 

                            </div>
                            <!-- /.panel-body -->
                        </div>
           
            @endif 
    
  </div> 
</div>
@endsection
