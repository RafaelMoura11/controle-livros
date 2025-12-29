<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'book_id',
        'contact_id',
        'borrower_name',
        'borrower_contact',
        'loaned_at',
        'due_at',
        'returned_at',
        'notes',
    ];

    protected $casts = [
        'loaned_at' => 'date',
        'due_at' => 'date',
        'returned_at' => 'date',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isActive(): bool
    {
        return $this->returned_at === null;
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
