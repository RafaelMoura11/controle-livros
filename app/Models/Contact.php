<?php

// app/Models/Contact.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'notes'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
