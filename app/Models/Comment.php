<?php

namespace App\Models;

use App\Models\Comment as ModelComent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'topic_id',
        'parent_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(ModelComent::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ModelComent::class, 'parent_id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
