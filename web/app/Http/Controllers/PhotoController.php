<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Comment;
use App\Http\Requests\StorePhoto;
use App\Http\Requests\StoreComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    protected $photo;
    protected $comment;

    public function __construct(Photo $photo, Comment $comment)
    {
        $this->middleware('auth')->except(['index', 'show']);;
        $this->photo = $photo;
        $this->comment = $comment;
    }

    /**
     * 写真一覧
     *
     * @return json
     */
    public function index()
    {
        $photos = $this->photo->with(['user'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate(4);

        return response()->json($photos);
    }

    /**
     * 写真詳細
     *
     * @return void
     */
    public function show($id)
    {
        $photo = $this->photo->with(['user'])->find($id);

        return response()->json($photo) ?? abort(404);;
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        $extension = $request->photo->extension();
        $folder = dirname(__FILE__, 4) . '/public/img/photo_images/';
        $file = $this->photo['id'];
        $this->photo['filename'] = $this->photo['id'] . '.' . $extension;

        // 画像保存（ /public/img/photo_images ）
        move_uploaded_file($_FILES['photo']['tmp_name'], $folder . $this->photo['filename']);

        // データベースエラー時にファイル削除を行うためトランザクション
        DB::beginTransaction();

        try {
            Auth::user()->photos()->save($this->photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            // DBとの不整合を避けるためアップロードしたファイルを削除
            \File::delete('public/photo_images', $this->photo['filename']);
            throw $exception;
        }

        $this->photo['id'] = $file;

        return response($this->photo, 201);
    }

    public function addComment(Photo $photo, StoreComment $request)
    {
        $this->content = $request->get('content');
        $this->comment['user_id'] = Auth::user()->id;
        $photo->comments()->save($this->comment);

        $new_comment = $this->comment::where('id', $this->comment['id'])->with('author')->first();

        return response($new_comment, 201);
    }
}
