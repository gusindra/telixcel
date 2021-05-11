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
