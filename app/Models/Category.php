<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes,ImageTrait;

    protected $appends=['image_url'];
    protected $hidden=['pivot','image','created_at','updated_at','deleted_at'];

    protected $guarded = [];

    public function getImageUrlAttribute()
    {
        return $this->getImage($this->image);
    }
    public function experts()
    {
        return $this->hasMany(Expert::class);
    }
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
