<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_contact_id_to_loans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('contact_id')
                ->nullable()
                ->after('book_id')
                ->constrained('contacts')
                ->nullOnDelete();

            $table->index('contact_id');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
        });
    }
};
