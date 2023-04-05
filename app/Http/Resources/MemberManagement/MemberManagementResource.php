<?php

namespace App\Http\Resources\MemberManagement;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberManagementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invite_type' => $this->invite_type,
            'module_type' => $this->module_type,
            'invite_status' => $this->invite_status,
            'email' => $this->email,
            'email_status' => $this->email_status,
            'email_resend_status' => $this->email_resend_status,
            'is_exist' => $this->is_exist,
            'is_evaluator' => $this->is_evaluator,
            'is_join_request' => $this->is_join_request,
            'join_request_status' => $this->join_request_status,
            'auto_invite_status' => $this->auto_invite_status,
            'user_status' => $this->user_status,
        ];
    }
}
