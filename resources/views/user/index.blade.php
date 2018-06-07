<b>User id: {{Auth::user()->id}}</b>
<h4>{{Auth::user()->working->fName}} {{Auth::user()->working->lName}}</h4>
<b>Current Address: </b> {{Auth::user()->working->addr1}} {{Auth::user()->working->addr2}}, {{Auth::user()->working->city}}, {{Auth::user()->working->state}} {{Auth::user()->working->zip}}<br>
<b>Donation Amount: </b>{{Auth::user()->working->cashDonation}}<br>
<b>Previous Attendee: </b>{{Auth::user()->working->previousAttendee}}<hr>
<a href="{{url('edit/'.Auth::user()->working->id)}}" class="btn btn-xs btn-primary">Edit My Info</a>

         
                      