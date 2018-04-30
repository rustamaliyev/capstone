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
               
                //don't save header row
                if($row[0] == 'FirstName') {continue;} 
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

                    $staging->save();
                    //STEP TWO CHECK THE ADDRESS VALIDITY VIA GOOGLE MAPS API
                    $addressParams = $row[2].$row[3].$row[4].$row[5].$row[6];
                    $formatAddressParams = str_replace(" ", "+", $addressParams);
                    $validateAddress = "https://maps.googleapis.com/maps/api/geocode/json?address=".$formatAddressParams."&key=".$apiKey;        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $validateAddress);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $json = curl_exec($ch);    
                        $validAddress = json_decode($json,true);     
                
                
                        if (isset($validAddress['results'][0]['formatted_address'])) {
                            echo $validAddress['results'][0]['formatted_address'];    
                        }                
                          
                        
                        
                   
                }
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            echo "Process Time: {$time}";    
        }
    }
}