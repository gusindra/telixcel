<?php

use App\Models\Permission;
use App\Models\ProductLine;
use App\Models\Request as Message;
use Illuminate\Support\Facades\Auth;

 /**
 * Get the previous request
 *
 * @return object
 */
function getPreviousRequest($request)
{
    return Message::where('client_id', $request->client_id)->where('id', '<', $request->id)->orderBy('id','desc')->first();
}

 /**
 * check is first request
 *
 * @return object
 */
function checkFirstRequest($request)
{
    return Message::where('client_id', $request->client_id)->where('user_id', $request->user_id)->count();
}

/**
 * bind to blade template
 * replacements is the data
 * template is the message
 *
 * @param  mixed $replacements
 * @param  mixed $template
 * @return void
 */
function bind_to_template($replacements, $template)
{
    return preg_replace_callback('/{(.+?)}/', function($matches) use ($replacements)
    {
    	try {
    		return $replacements[$matches[1]];
    	} catch (Exception $e) {
    		return '';
    	}

    }, $template);
}

/**
 * slugify make slug from text
 *
 * @param  mixed $text
 * @param  mixed $divider
 * @return void
 */
function slugify($text, string $divider = '-')
{
  // replace non letter or digits by divider
  $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, $divider);

  // remove duplicate divider
  $text = preg_replace('~-+~', $divider, $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}

function agentStatus($teamuser)
{
    foreach($teamuser as $key => $user)
    {
        if($user->status == "Online")
        {
            return "Online";
        }
        else
        {
            if($teamuser->count()==$key+1)
            {
                if(!$user->status){
                    return "Offline";
                }
                return "Away";
            }
        }
    }
}

function countMsg($client)
{
    return Message::where('client_id', $client->uuid)->where('is_read', 0)->count();
}

function attachmentExt($link_attachment)
{
    $image = ['jpg', 'jpeg', 'png'];
    $audio = ['aac', 'amr', 'mpeg', 'ogg', 'mp3'];
    $video = ['mp4', '3gp'];
    $document = ['pdf', 'txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

    $ext = substr(strrchr($link_attachment, '.'), 1);
    if(in_array($ext, $image)){
        $extension = 'image';
    }elseif(in_array($ext, $audio)){
        $extension = 'audio';
    }elseif(in_array($ext, $video)){
        $extension = 'video';
    }elseif(in_array($ext, $document)){
        $extension = 'document';
    }else{
        return false;
    }

    return $extension;
}

function get_permission($type=null, $for=null){
    if($type==null){
	    return Permission::get();
    }elseif($type=='admin'){
        $avaiable = ['CUSTOMER', 'TEMPLATE', 'CAMPAIGN', 'ORDER', 'PRODUCT', 'BILLING', 'TEAM', 'PROJECT', 'SETTING'];
    } elseif ( $type=='team' ){
        $avaiable = ['CHAT', 'CUSTOMER', 'TICKET', 'TEMPLATE', 'TEAM'];
    }else{
        return Permission::get();
    }
	return Permission::whereIn('model', $avaiable)->orderBy('model', 'ASC')->get();
}

function isJSON($string){
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function checkPermisissions($array_id){
    $user = Auth::user();
    if($user->super->first()){
        if($user->super->first()->role=='superadmin'){
            return true;
        }
    }elseif($user->role){
        foreach($user->role as $role){
            // dd($role->role->permission);
            foreach($role->role->permission as $permission){
                if (in_array($permission->model, $array_id)){
                    return true;
                }
            }
        }
    }
    return false;
}

function checkRoles($role_id){
    $user = Auth::user();
    if($user->super->first()){
        if($user->super->first()->role=='superadmin'){
            return true;
        }
    }elseif($user->role){
        foreach($user->role as $role){
            if ( $role->id == $role_id ){
                return true;
            }
        }
    }
    return false;
}

function disableInput($status){
    return $status == 'draft' ? false:true;
}

/**
 * balance user saldo
 *
 * @param  mixed $user
 * @param  mixed $team_id optional
 * @param  mixed $type default to check total or log balance
 * @return void
 */
function balance($user, $team_id=0, $type='total')
{
    if($type=='total'){
        $balance = 0;
        if($user->balance($team_id)->first()){
            if($team_id>0){
                $balance = $user->balance($team_id)->first()->balance;
            }else{
                if(count($user->balance($team_id)->groupBy('team_id')->get())>1){
                    $saldos = $user->balance($team_id)->whereRaw('id IN (select MAX(id) FROM saldo_users GROUP BY team_id)')->get();
                }else{
                    $saldos = $user->balance($team_id)->get();
                }
                // foreach($saldos as $saldo){
                    // $balance = $balance + $saldo->balance;
                    if($saldos)
                        $balance = $saldos[0]->balance;
                // }
            }
        }
        return $balance;
    }
    if($type=='test'){
        // return count($user->balance($team_id)->groupBy('team_id')->get());
        if(count($user->balance($team_id)->groupBy('team_id')->get())>1){
            return $user->balance($team_id)->whereRaw('id IN (select MAX(id) FROM saldo_users GROUP BY team_id)')->get();
        }else{
            return $user->balance($team_id)->get();
        }
    }
    return $user->balance($team_id)->orderBy('id', 'desc')->get();
}

function estimationSaldo(){
    $master = ProductLine::where('name', 'Telixcel')->first();
    return $master->items;
}

