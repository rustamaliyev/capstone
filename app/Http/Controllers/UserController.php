<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Audit;
use App\Staging;
use App\Working;
use Auth;
use League\Csv\Reader;
use League\Csv\Statement;

class UserController extends Controller
{
 	
     public function edit($id)
        {
            //THIS METHOD GETS CURRENT USER INFO
            $userInfo = Working::where('id',$id)->first();
  
            return view('user.edit')->with('userInfo', $userInfo);
        }
    
        public function update(Request $request, $id)
        {
           //THIS METHOD UPDATES CURRENT USER INFO
           $working = Working::find($id);
           $working->fName = $request['fName'];
           $working->lName = $request['lName'];    
           $working->addr1 = $request['addr1'];
           $working->addr2 = $request['addr2'];
           $working->city = $request['city'];
           $working->state = $request['state'];
           $working->zip = $request['zip'];
           $working->cashDonation = $request['cashDonation'];  
            
            //ON UPDATE IT SAVES CHANGES INTO AUDIT TABLE
           $modifiedAttributes = $working->getDirty();  
           //save changes to audit table  
            foreach($modifiedAttributes as $key => $value) {
                
            if($value)    {
              $audit = new Audit();   
              $audit->workingID = $id;
              $audit->fieldName = $key;
              $audit->fieldValue = $value;
              $audit->changedByUserID = $working->userID;    
              $audit->save();    
                }
            }  
             
          
           $working->save();
            
           //ALL DONE REDIRECT BACK        
           return redirect('home');


        }
    
}

