<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        return PostResource::collection(Post::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required'],
            'description' => ['required'],
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg'
        ]);
        $data['user_id'] = Auth::id();

        $post = Post::create($data);

        $post->saveImages($request->file('images'));

        return new PostResource($post);
    }

    public function show(int $id)
    {
        if (!$post = Post::find($id)) {
            return response()->json([], 404);
        }
        return new PostResource($post);
    }

    public function update(Request $request, int $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([], 404);
        }

        $data = $request->validate([
            'title' => ['required'],
            'description' => ['required'],
            'images' => 'sometimes|required|array',
            'images.*' => 'image|mimes:jpg,jpeg'
        ]);

        $post->update($data);

        if ($request->hasFile('images')) {
            $post->saveImages($request->file('images'));
        }

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $post->deleteImages();
        $post->delete();
        return response()->json();
    }
}
