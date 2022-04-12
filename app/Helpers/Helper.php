<?php

use App\Models\Permission;
use App\Models\Request as Message;


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
