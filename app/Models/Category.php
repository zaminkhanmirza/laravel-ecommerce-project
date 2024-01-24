<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Define the relationship with SubCategory
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
