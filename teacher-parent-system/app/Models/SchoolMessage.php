<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolMessage extends Model
{
    protected $fillable = ['sender_id', 'audience', 'target_class_ids', 'subject', 'body'];

    protected function casts(): array
    {
        return ['target_class_ids' => 'array'];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class, 'message_id');
    }
}
