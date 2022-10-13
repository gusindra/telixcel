<?php

namespace App\Http\Controllers;

use App\Models\Template;

class TemplateController extends Controller
{

    public function view()
    {
        $template = Template::select('name', 'description as title', 'template_id', 'id', 'type as className')->where('type','!=','helper')->whereHas('teams', function ($query) {
            $query->where([
                'teams.id' => auth()->user()->currentTeam->id
            ]);
        })->get();

        $t1 = '';$t2 = '';$t3 = '';
        $secondKey = 0; $thridKey = 0; $firstKey = 0;$fourthKey = 0;
        $arrayLevel = [];
        $array = [];
        foreach($template as $key => $t){
            if(!is_null($t->template_id) && $t->template_id>0){
                foreach($arrayLevel['first'] as $ch){
                    if($ch->id == $t->template_id){
                        if($ch->child && $t1 != $t){
                            $arr = $ch->child;
                            array_push($arr, $t);
                            $ch->setAttribute('child', $arr);
                        }else{
                            $ch->setAttribute('child', ['0'=>$t]);
                        }
                        $t1 = $t;
                        $arrayLevel['second'][] = $t;
                    }else{
                        if(array_key_exists('second', $arrayLevel)){
                            foreach($arrayLevel['second'] as $ch2){
                                if($ch2->id == $t->template_id){
                                    if($t2 != $t){
                                        if($ch2->child){
                                            $arr2 = $ch2->child;
                                            array_push($arr2, $t);
                                            $ch2->setAttribute('child', $arr2);
                                        }else{
                                            $ch2->setAttribute('child', ['0'=>$t]);
                                        }
                                        $arrayLevel['thrid'][] = $t;
                                    }
                                    $t2 = $t;
                                }else{
                                    if(array_key_exists('thrid', $arrayLevel)){
                                        foreach($arrayLevel['thrid'] as $ch3){
                                            if($ch3->id == $t->template_id){
                                                if($t3 != $t){
                                                    if($ch3->child){
                                                        $arr3 = $ch3->child;
                                                        array_push($arr3, $t);
                                                        $ch3->setAttribute('child', $arr3);
                                                    }else{
                                                        $ch3->setAttribute('child', ['0'=>$t]);
                                                    }
                                                    $arrayLevel['fourth'][] = $t;
                                                }
                                                $t3 = $t;
                                            }else{
                                                $arrayLevel['fifth'][] = $t;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $array[] = $t;
                $arrayLevel['first'][] = $t;
            }
        }
        // return $arrayLevel;
        // return $array;
        // return $template;
        return view('template.view', ['data'=>json_encode($array)]);
    }

    public function show($uuid){
        return view('template.show', ['uuid'=> $uuid]);
    }

    public function edit(Template $template){
        return redirect()->to("/template/". $template->uuid);

    }
}
