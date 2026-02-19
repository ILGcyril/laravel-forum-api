<?php

namespace App\Models;

use App\Models\Comment as ModelComent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'topic_id'
    ];
    protected $visible = ['id', 'title', 'content', 'topic_id', 'user_id', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
