<?php

namespace App\Http\Controllers;

use App\Photo;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    protected $photo;

    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    /**
     * いいね
     *
     * @param string $id
     * @return array
     */
    public function like(string $id)
    {
        $photo = $this->photo->where('id', $id)->with('likes')->first();

        if (!$photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return ["photo_id" => $id];
    }

    /**
     * いいね解除
     *
     * @param string $id
     * @return array
     */
    public function unlike(string $id)
    {
        $photo = $this->photo->where('id', $id)->with('likes')->first();

        if (!$photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ["photo_id" => $id];
    }
}
