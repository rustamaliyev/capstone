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
class ImportController extends Controller
{
public function importCSV(Request $request)
    {
    
        $apiKey = 'AIzaSyCPt110uwWaetZfoerFQmzy4iWk2230frY';
        sleep(1);
    
  
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
               
                //SKIP IF HEADER ROW
                if($row[0] == 'FirstName') {continue;} 
                //SKIP IF ADDRESS OR CITY COLUMNS ARE EMPTY
                if($row[2] == '' || $row[4] == '') {continue;}
                
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
                    //$staging->cashDonation = $row[8];
                    //$staging->previousAttendee = $row[9];
                    //SAVE TO STAGING TABLE
                    $staging->save();
                
                    //STEP TWO CHECK THE ADDRESS VALIDITY VIA GOOGLE GEOCODING API
                    //DATA CLEANUP TO GET CORRECT RESULTS FROM GOOGLE
                    $addr1 = str_replace("#", "apt", $row[2]); 
                    $addressParams = $addr1.$row[3].$row[4].$row[5].$row[6];
                    $addressParams = str_replace(" ", "+", $addressParams);
                
                    //SEND DATA OVER TO GOOGLE API USING CURL
                    $validateAddress = "https://maps.googleapis.com/maps/api/geocode/json?address=".$addressParams."&sensor=true&key=".$apiKey;        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $validateAddress);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $json = curl_exec($ch);    
                     //DECODE JSON DATA
                        $validAddress = json_decode($json,true);    
                     //IF ANY RESULT FROM GOOGLE  
                        if (isset($validAddress['results'][0])) {
                            $googleFormattedAddress = explode(",", $validAddress['results'][0]['formatted_address']);
                     //SAVE GOOGLE RESULTS INTO VARIABLES       
                             $googleAddr1 = $googleFormattedAddress[0];
                             $googleCity = $googleFormattedAddress[1];
                             $googleStateZip = explode(" ", $googleFormattedAddress[2]);
                             $googleState = $googleStateZip[0];
                             $googleZip = $googleStateZip[2];
                            
                        //CREATE NEW USER
                            $fNameFirstChar = substr(strtolower($row[0]), 0, 1);
                            $userData = [
                                'username' => $fNameFirstChar.strtolower($row[1]),
                                'password' => bcrypt('123456'),
                                'isAdmin' => 0,
                            ];
                        //SAVE INTO WORKING TABLE
                            $newUser = User::create($userData);   
                            $working = new Working();
                            $working->userID = $newUser->id;
                            $working->stagingID = $staging->id;
                            $working->fName = $row[0];
                            $working->lName = $row[1];
                            $working->addr1 = $googleAddr1;
                            $working->city =  $googleCity;
                            $working->state = $googleState;
                            $working->zip = $googleZip;
                            $working->save();
                            
                            
                            //echo $validAddress['results'][0]['formatted_address'].'<br>';
                        }     
                
                        
                    
            
                }
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            echo "Process Time: {$time}";    
        }
    }
}