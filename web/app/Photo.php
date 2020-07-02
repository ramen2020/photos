<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    /** プライマリキーの型 */
    protected $keyType = 'string';

    // アクセサ
    protected $appends = [
        'likes_count', 'liked_by_user',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!Arr::get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    /**
     * ランダムなID値をid属性に代入
     *
     */
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId(12);
    }

    /**
     * ランダムなID値を生成
     *
     * @return string
     */
    private function getRandomId($sum)
    {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);
        $id = "";

        for ($i = 0; $i < $sum; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * リレーションシップ - commentsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy('id', 'desc');
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    /**
     * アクセサ - likes_count
     *
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    /**
     * アクセサ - liked_by_user
     * userがいいねしているか判断する
     *
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }
}
