 @if(session('success'))
         <div class="alert alert-success alert-dismissable text-center" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">×</span>
          </button>
          <h3 class="alert-heading font-size-h4 font-w400">Success</h3>
          <p class="mb-0">{{ session('success') }}</p>
        </div>
    @endif                            
                            
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#home-pills" data-toggle="tab" aria-expanded="true">Upload Files</a>
                                </li>
                                <li class=""><a href="#records" data-toggle="tab" aria-expanded="false">All Records</a>
                                </li>
                              
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="home-pills">
                                    <h4>Upload Files</h4>
                                      <!--<form method="post" id="importForm" action="import" enctype="multipart/form-data">-->
                                      <form id="importForm" method="post" enctype="multipart/form-data">       
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-group input-group-file csvInputGroup" data-plugin="inputGroupFile">
                                        <span class="input-group-btn">
                                            <span class="btn btn-outline btn-file">
                                                <i class="icon wb-upload" aria-hidden="true"></i>
                                                <input class="form-control" type="file" class="csvInput" name="csvFile" placeholder="Upload File">
                                            </span>
                                            <button type="submit" class="btn btn-success">Submit</button>

                                        </span>
                                        
                                    </div>
                                  
                                </div>

                                 

                            </div>


                        </div>
                        <div class="row">
                            <div class="col-md-12">
                             <div class="alert alert-info" role="alert">
                                        <h4>File must be comma delimited and in the following format:</h4>
                                        <p>Address 1, Address 2, City, State, Zip, List Name, Cash Donation, Previous Attendee</p>
                                   
                            </div>
                            </div>
                            
                          </div>
                                          
                        </form>
                                </div>

                                <div class="tab-pane fade" id="records">
                                <h4>All Records</h4>
                                    
                                    
                                <table width="100%" class="table table-striped table-bordered table-hover" id="working">
                                <thead>
                                    <tr>
                                        <th id="id">ID</th>
                                        <th id="username">Username</th>
                                        <th id="fName">First Name</th>
                                        <th id="lName">Last Name</th>
                                        <th id="addr1">Address1</th>
                                        <th id="addr2">Address2</th>
                                        <th id="city">City</th>
                                        <th id="state">State</th>
                                        <th id="zip">Zip</th>
                                        <th id="listName">List</th>
                                        <th id="cashDonation">Donation Amount</th>
                                        <th id="previousAttendee">Previous Attendee</th>
                           
                                      
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                       
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                                    
                                     <a href="delete" class="btn btn-sm btn-danger" data-method="delete"  data-token="{{csrf_token()}}" data-confirm="Are you sure?">Delete All Records (Working, Staging and Users)</a> 
                                </div>
                          
                            </div>


