<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    public function saveImages(array $images)
    {
        foreach ($images as $imageFile) {
            $imageFile->storeAs('public/images/', $imageFile->hashName());

            $image = new Image();
            $image->name = $imageFile->getClientOriginalName();
            $image->hash_name = $imageFile->hashName();
            $image->post_id = $this->id;

            $image->save();
        }
    }

    public function deleteImages()
    {
        foreach ($this->images as $image) {
            Storage::disk('public')->delete('images/'. $image->hash_name);
            $image->forceDelete();
        }
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
