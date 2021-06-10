<?php

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
