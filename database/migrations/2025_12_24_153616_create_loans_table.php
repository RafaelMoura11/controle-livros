<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();

            $table->string('borrower_name');
            $table->string('borrower_contact')->nullable();

            $table->date('loaned_at');
            $table->date('due_at');
            $table->date('returned_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'returned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
