<?php

namespace Modules\AI\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AI\Entities\AiConversation;

#[Fillable(['conversation_id', 'role', 'content'])]

class Message extends Model
{
    use HasFactory;

   protected $table = 'messages';
  protected $casts = [
        'content' => 'array',
    ];
    /**
     * Summary of conversation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<AiConversation, Message>
     */
    public function conversation()
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }

}
