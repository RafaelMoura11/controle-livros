<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'isbn', 'notes'];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoan(): HasOne
    {
        return $this->hasOne(Loan::class)
            ->whereNull('returned_at')
            ->latestOfMany();
    }

    public function isLoaned(): bool
    {
        // Mais eficiente do que ->activeLoan()->exists() quando já carregou o relation
        return $this->relationLoaded('activeLoan')
            ? (bool) $this->getRelation('activeLoan')
            : $this->activeLoan()->exists();
    }
}
