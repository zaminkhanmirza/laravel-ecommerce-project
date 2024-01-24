<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    // Define the relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
