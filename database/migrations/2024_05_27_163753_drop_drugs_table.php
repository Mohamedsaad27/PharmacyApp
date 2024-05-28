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
        Schema::table('product_drugs', function (Blueprint $table) {
            $table->dropForeign(['drug_id']);
        });
        Schema::dropIfExists('drugs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
