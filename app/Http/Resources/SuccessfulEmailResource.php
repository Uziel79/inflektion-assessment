<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessfulEmailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'affiliate_id' => $this->affiliate_id,
            'envelope' => $this->envelope,
            'from' => $this->from,
            'subject' => $this->subject,
            'to' => $this->to,
            'dkim' => $this->dkim,
            'SPF' => $this->SPF,
            'spam_score' => $this->spam_score,
            'email' => $this->email,
            'raw_text' => $this->raw_text,
            'sender_ip' => $this->sender_ip,
            'timestamp' => $this->timestamp,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
