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
    echo 'Started File Processing';
    
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
                         
                        //CHECK IF RECORD DOES NOT EXIST IN THE WORKING TABLE(WE CHECK FOR ADDRESS1 AND LAST NAME)
                         //if (Working::where('addr1', '=', $address)->where('lName', '=', $row[1])->count() == 0) {   
                             
                            
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
                            
                            if(is_int($address_out['zip'])) {
                                $working->zip = $address_out['zip'];
                                
                            }else {
                                $working->zip = '00000';
                            } 
                             
                            
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
                           
                        }     
            
                }
            
   
            
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            echo "Completed! Process Time: {$time}";  
            
            //return redirect('home')->with('success', 'Success! File has been uploaded!'.'Total Processing Time: '.$time. ' Sorry I am slow there was a lot to process!');
        
        }
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