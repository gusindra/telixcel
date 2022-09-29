<?php

namespace App\Http\Livewire\Report;

use Livewire\Component;

class Log extends Component
{
    public $filePath = 'apilog.log';
    public $readFile = [];

    public function updatePath(){
        $path = storage_path('logs/'.$this->filePath);
        $data = array();
        // $jsonString = file_get_contents($path);
        // $data = json_decode($jsonString, true);
        // $myfile = fopen($path, "r") or die("Unable to open file!");
        // echo fread($myfile, filesize($path));
        // dd(file_get_contents($path));
        $myfile = file_get_contents($path);
        $firstData = explode('[]', $myfile);
        foreach($firstData as $key => $row){
            if(count($firstData)!=$key+1){
                foreach(explode('array', $row) as $k => $r){
                    if($k==0){
                        if($r!=""){
                            $data[$key]['time'] = str_replace("]","",str_replace("[","", $r));
                        }
                    }else{
                        foreach(explode(') {', $r) as $k1 => $r1){
                            if($k1==0){
                                foreach(explode(",", $r1) as $k2 => $r2){
                                    if($k2==2){
                                        $data[$key]['to'] = str_replace("to =>", "",str_replace("'","", $r2));
                                    }
                                }
                                $data[$key]['data'] = $r1;

                            }else{
                                $data[$key]['user'] = str_replace("}", "",str_replace('"',"",$r1));
                            }
                        }
                    }
                }
            }
        }
        // dd(json_encode($data, true));
        $this->readFile = $data;
        // fclose($myfile);
        // Update Key
        // $data['country.title'] = "Change Manage Country";

        // Write File
        // $newJsonString = json_encode($data, JSON_PRETTY_PRINT);
        // file_put_contents(base_path('resources/lang/en.json'), stripslashes($newJsonString));

        // Get Key Value
    }

    public function read(){
        $path = storage_path('logs/'.$this->filePath);
        $data = array();
        $myfile = file_get_contents($path);
        $firstData = explode('[]', $myfile);
        foreach($firstData as $key => $row){
            if(count($firstData)!=$key+1){
                foreach(explode('array', $row) as $k => $r){
                    if($k==0){
                        if($r!=""){
                            $data[$key]['time'] = preg_replace('/\s+/', '', str_replace("]","",str_replace("[","", $r)));
                        }
                    }else{
                        foreach(explode(') {', $r) as $k1 => $r1){
                            if($k1==0){
                                foreach(explode(",", $r1) as $k2 => $r2){
                                    if($k2==5){
                                        $data[$key]['to'] = preg_replace('/\s+/', '', str_replace("to =>", "",str_replace("'","", $r2)));
                                    }
                                }
                                $data[$key]['data'] = $r1;

                            }else{
                                $data[$key]['user'] = preg_replace('/\s+/', '',str_replace("}", "",str_replace('"',"",$r1)));
                            }
                        }
                    }
                }
            }
        }
        // dd($data);
        // dd(json_encode($data));
        return json_encode($data);
    }

    public function render()
    {
        return view('livewire.report.log', ['logs' => $this->read()]);
    }
}
