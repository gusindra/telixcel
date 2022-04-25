<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id'    => $this->id,
            'type'  => $this->type,
            'text'  => $this->message_content,
            'title' => $this->title,
            'to'    => $this->msisdn,
            'status'=> $this->status,
            'date'  => $this->created_at,
        ];
    }
}
