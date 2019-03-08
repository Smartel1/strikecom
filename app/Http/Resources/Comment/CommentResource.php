<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Resources\Json\Resource;

class CommentResource extends Resource
{
    /**
     * Структура комментария
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'user'    => $this->user_id ? [
                'id'     => $this->user->id,
                'name'   => $this->user->name,
                'email'  => $this->user->email
            ] : null,
            'content' => $this->id,
            'photos'  => $this->photos->pluck('url'),
        ];
    }
}
