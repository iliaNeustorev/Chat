<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'sender_id',
        'recipient_id',
        'sender_role',
        'status',
        'channel_id',
        'parent_id'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function channel()
    {
        return $this->belongTo(Channel::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id', 'id');
    }
}
