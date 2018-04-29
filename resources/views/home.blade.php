@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

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
     <form method="post" id="importForm" action="import" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-group input-group-file csvInputGroup" data-plugin="inputGroupFile">
                                        <span class="input-group-btn">
                                            <span class="btn btn-outline btn-file">
                                                <i class="icon wb-upload" aria-hidden="true"></i>
                                                <input type="file" class="csvInput" name="csvFile" placeholder="Upload File">
                                            </span>
                                            <button type="submit" class="btn btn-success">Submit</button>

                                        </span>
                                    </div>
                                </div>

                                <div class="alert alert-primary" role="alert">
                                    <h4>File must be comma delimited and in the following format:</h4>
                                    <p>Address 1, Address 2, City, State, Zip, List Name, Cash Donation, Previous Attendee</p>
                                </div>

                            </div>


                        </div>
                    </form>
    
  </div> 
</div>
@endsection
