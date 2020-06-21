<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    protected $photo;

    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
        $this->middleware('auth')->except(['index']);;
    }

    /**
     * 写真一覧
     *
     * @return json
     */
    public function index()
    {
        $photos = $this->photo->with(['user'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        return response()->json($photos);
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        $extension = $request->photo->extension();
        $this->photo['filename'] = $this->photo['id'] . '.' . $extension;
        $request->photo->storeAs('public/photo_images', $this->photo['filename']);
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

        // 新規作成のステータスコード201を返す
        return response($this->photo, 201);
    }
}
