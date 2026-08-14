<?php

namespace Modules\AI\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AI\Entities\Message;
use Modules\User\Entities\User;

#[Fillable(['user_id', 'title'])]
class AiConversation extends Model
{
    use HasFactory;

    protected $table = 'ai_conversations';
    /**
     * Summary of messages
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Message, AiConversation>
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, AiConversation>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
