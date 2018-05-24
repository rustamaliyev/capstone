<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Staging;
use App\Working;
use Auth;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Database\QueryException;

class ImportController extends Controller
{
    
public function importCSV2(Request $request){
 
$apiKey = 'AIzaSyCPt110uwWaetZfoerFQmzy4iWk2230frY';
  

        //check if file is not empty
        if ($request->hasFile('csvFile')) {
            
             //process the file
            $file = $request->csvFile;
            $reader = Reader::createFromPath($file, 'r');
                            
            $size =  sizeof($reader);
            foreach ($reader as $index => $row) {
                 
                
                }
            
          
        }
        
    
  
    
    $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]; echo "Process Time: {$time}";  
                
    
}
    
    
public function importCSV(Request $request)
    {
   
    
        $apiKey = 'AIzaSyCPt110uwWaetZfoerFQmzy4iWk2230frY';
        //sleep(1);
    
 
        //check if file is not empty
        if ($request->hasFile('csvFile')) {
            
              
            //process the file
            $file = $request->csvFile;
            $reader = Reader::createFromPath($file, 'r');   
            
     
            foreach ($reader as $index => $row) {
                
    
                
                
                //STEP ONE save raw data into staging table to maintina data integrity                        
                //$row is an array where each item represent a CSV data cell
                //$index is the CSV row index
                

                $staging = new Staging();  
             
                //SKIP IF FOLLOWING IS TRUE: IS HEADER ROW, ADDRESS OR CITY COLUMNS ARE EMPTY,ADDRESS1 CONTAINS P.O. BOX                
                if($row[0] == 'FirstName' || $row[2] == '' || $row[4] == '' || preg_match('/(?:P(?:ost(?:al)?)?[\.\-\s]*(?:(?:O(?:ffice)?[\.\-\s]*)?B(?:ox|in|\b|\d)|o(?:ffice|\b)(?:[-\s]*\d)|code)|box[-\s\b]*\d)/i',$row[2]))
                {continue;}                

                
                
                    try {
                          $staging->fName = $row[0];
                            $staging->lName = $row[1];
                            $staging->addr1 = $row[2];
                            $staging->addr2 = $row[3];
                            $staging->city =  $row[4];
                            $staging->state = $row[5];
                            //check if zip code contains empty value
                            if($row[6] == '') {
                                $staging->zip = '00000';
                            }else {
                                $staging->zip = $row[6];
                            }
                            $staging->listName = $row[7];
                            //check if cash donation column exist
                            if (isset($row[8]) && is_numeric($row[8])) {
                             $staging->cashDonation = $row[8];
                            }
                            //check if previous attendee column exist
                            if (isset($row[9])) {
                             $staging->previousAttendee = $row[9];
                            }

                            //SAVE TO STAGING TABLE
                            $staging->save();

                            } catch (QueryException $e) {
                                echo 'Caught exception: ',  $e->getMessage(), "\n";
                                continue;

                            } catch (PDOException $e) {
                                 echo 'Caught exception: ',  $e->getMessage(), "\n";
                                continue;
                            }      
                
              
                    //STEP TWO CHECK THE ADDRESS VALIDITY VIA GOOGLE GEOCODING API
                    //DATA CLEANUP TO GET CORRECT RESULTS FROM GOOGLE
                    $addr1 = str_replace("#", "apt", $row[2]); 
                    $addressParams = $addr1.$row[3].$row[4].$row[5].$row[6];
                    $addressParams = str_replace(" ", "+", $addressParams);
                
                    //SEND DATA OVER TO GOOGLE API USING CURL
                    $validateAddress = "https://maps.googleapis.com/maps/api/geocode/json?address=".$addressParams."&sensor=false&key=".$apiKey;        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $validateAddress);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $json = curl_exec($ch);    
                     //DECODE JSON DATA
                        $validAddress = json_decode($json,true);  
                
                        $address_out = null;
                            $parts = array( 
                              'unit'=>array('subpremise'),    
                              'street_number'=>array('street_number'),
                              'address'=>array('route'),    
                              'city'=>array('locality'), 
                              'state'=>array('administrative_area_level_1'), 
                              'zip'=>array('postal_code'), 
                            ); 
                           
                              
                            
                            //return $address_out; 
        
                
                     //IF ANY RESULT FROM GOOGLE  
           if (!empty($validAddress['results'][0]['address_components'])) {            
                            
                            //get all address components from the api and store them a new array
                           $ac = $validAddress['results'][0]['address_components']; 
                              foreach($parts as $need=>&$types) { 
                                foreach($ac as &$a) { 
                                  if (in_array($a['types'][0],$types)) $address_out[$need] = $a['short_name']; 
                                  elseif (empty($address_out[$need])) $address_out[$need] = ''; 
                                } 
                              }  
                            
                            //build address1 string
                            $address = $address_out['street_number'].' '.$address_out['address'];
                            $fullRecord = $row[0].' '.$row[1].' '.$address.' '.$address_out['unit'].' '.$address_out['city'].' '.$address_out['state'].' '.$address_out['zip'];
                        //CHECK IF RECORD DOES NOT EXIST IN THE WORKING TABLE(WE CHECK FOR ADDRESS1 AND LAST NAME)
               
               
              
                            
                            
                            
                            $working = \App\Working::all();
                          
                            if(count($working) > 0) {
                                
                              
                                
                             //Levenshtein edit distance - loop thru working table records to find a match 
                            foreach ($working as $working) {
                               // echo $fullRecord.'<br>'; 
                            $fullRecordDB = $working->fName.' '.$working->lName.' '.$working->addr1.' '.$working->addr2.' '.$working->city.' '.$working->state.' '.$working->zip;
                            //get the match score between csv row and already existing rows in the database
                            echo $matchScore = levenshtein($fullRecord,$fullRecordDB).'<br>';
                                    if($matchScore < 20){
                                                    
                                               echo 'match found not saving'.$matchScore.' '.$fullRecord.' <--> '.$fullRecordDB.'<br>';
                                               
                                            }   
 
                                                      
                                     else if($matchScore > 20) {
                                                echo 'match NOT found saving'.$matchScore.' '.$fullRecord.' <--> '.$fullRecordDB.'<br>';
                                         
                                            //SAVE INTO WORKING TABLE BLOCK
                                                   //CREATE NEW USER HE DOESN'T EXIST YET

                                                    $fNameFirstChar = substr(strtolower($row[0]), 0, 1);
                                                    //construct username based on first initial, last name and zip code 
                                                    $username = $fNameFirstChar.strtolower($row[1]).$address_out['zip'];
                                                    //clean up username from illegal chars
                                                    $username = str_replace(['-', '.', ' '], "", $username); 

                                                     if (User::where('username', '=', $username)->count() == 0) {      
                                                        $userData = [
                                                            'username' => $username,
                                                            'password' => bcrypt('123456'),
                                                            'isAdmin' => 0,
                                                        ];
                                                        $newUser = User::create($userData);  
                                                         //}    
                                                    //SAVE INTO WORKING TABLE
                                                        echo 'SAVING: '.$row[0].' '.$row[1].' '.$address; 
                                                        $working = new Working();
                                                        $working->userID = $newUser->id;
                                                        $working->stagingID = $staging->id;
                                                        $working->fName = $row[0];
                                                        $working->lName = $row[1];
                                                        $working->addr1 = $address;
                                                        if($address_out['unit'] !=='') {
                                                        $working->addr2 ='# '.$address_out['unit'];
                                                        }
                                                        $working->city =  $address_out['city'];
                                                        $working->state = $address_out['state'];
                                                        $working->zip = $address_out['zip'];


                                                        $working->listName = $row[7]; 
                                                        //check if cash donation column exist
                                                        if (isset($row[8]) && is_numeric($row[8])) {
                                                         $working->cashDonation = $row[8];
                                                        } 
                                                        //check if previous attendee column exist
                                                        if (isset($row[9])) {
                                                         $working->previousAttendee = $row[9];
                                                        } 
                                    
                                                        $working->save();
                                                            
                                                     }
                                         //END SAVE INTO WORKING TABLE BLOCK
                                    }
                           
                                                   
                               }   
                              
    
                            }else {
                                              echo 'empty table save one record';
                                                    //SAVE INTO WORKING TABLE BLOCK
                                                   //CREATE NEW USER HE DOESN'T EXIST YET

                                                    $fNameFirstChar = substr(strtolower($row[0]), 0, 1);
                                                    //construct username based on first initial, last name and zip code 
                                                    $username = $fNameFirstChar.strtolower($row[1]).$address_out['zip'];
                                                    //clean up username from illegal chars
                                                    $username = str_replace(['-', '.', ' '], "", $username); 

                                                     if (User::where('username', '=', $username)->count() == 0) {      
                                                        $userData = [
                                                            'username' => $username,
                                                            'password' => bcrypt('123456'),
                                                            'isAdmin' => 0,
                                                        ];
                                                        $newUser = User::create($userData);  
                                                         //}    
                                                    //SAVE INTO WORKING TABLE
                                                         echo 'SAVING: '.$row[0].' '.$row[1].' '.$address;
                                                        $working = new Working();
                                                        $working->userID = $newUser->id;
                                                        $working->stagingID = $staging->id;
                                                        $working->fName = $row[0];
                                                        $working->lName = $row[1];
                                                        $working->addr1 = $address;
                                                        if($address_out['unit'] !=='') {
                                                        $working->addr2 ='# '.$address_out['unit'];
                                                        }
                                                        $working->city =  $address_out['city'];
                                                        $working->state = $address_out['state'];
                                                        $working->zip = $address_out['zip'];


                                                        $working->listName = $row[7]; 
                                                        //check if cash donation column exist
                                                        if (isset($row[8]) && is_numeric($row[8])) {
                                                         $working->cashDonation = $row[8];
                                                        } 
                                                        //check if previous attendee column exist
                                                        if (isset($row[9])) {
                                                         $working->previousAttendee = $row[9];
                                                        } 
                                    
                                                        $working->save();
                                                            
                                                     }
                                         //END SAVE INTO WORKING TABLE BLOCK
                                
                            }
            
            }
            
            
            
            //return redirect('home')->with('success', 'Success! File has been uploaded!'.'Total Processing Time: '.$time. ' Sorry I am slow there was a lot to process!');
        
        }
    
      }
 $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
 echo "Completed! Process Time: {$time}";         
//end of main function
}
    
  
    
    
    
    
    public function getAllRecords()
    {
        
        //$working = Working::all();
        $working = Working::with('user')->get();
        echo '{ "data":'.$working->toJson().'}';


    }
    
    public function deleteAllRecords()
    {
        
        \App\Working::query()->delete();
        \App\Staging::query()->delete();
        \App\User::query()->where('isAdmin',0)->delete();
       
        return redirect('home')->with('success', 'Success! Deleted all records!');


    }
    
}