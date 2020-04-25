<?php

namespace App\Traits;

use App\Like;
use App\User;
use Illuminate\Database\Eloquent\Builder;

trait Likeable
{
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function scopeWithLikes(Builder $query)
    {
        $query->leftJoinSub(
            'select tweet_id, sum(liked) likes, sum(!liked) dislikes from likes group by tweet_id',
            'likes',
            'likes.tweet_id',
            'tweets.id'
        );
    }

    public function like($user = null, $liked = true)
    {
        $userId = $user ? $user->id : auth()->id();

        $this->likes()->updateOrCreate([
            'user_id' => $userId
        ], [
            'liked' => $liked
        ]);
    }

    public function dislike($user = null)
    {
        $this->like($user, false);
    }

    public function isLikedBy(User $user)
    {
        return (bool) $user->likes
            ->where('tweet_id', $this->id)
            ->where('liked', true)
            ->count();
    }

    public function isDislikedBy(User $user)
    {
        return (bool) $user->likes
            ->where('tweet_id', $this->id)
            ->where('liked', false)
            ->count();
    }
}
