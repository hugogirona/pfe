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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('name_alt')->nullable();
            $table->string('slug')->unique();
            $table->string('country', 2)->default('BE');
            $table->string('postal_code', 4)->index();
            $table->string('searchable');
            $table->decimal('latitude', total: 8, places: 5)->nullable();
            $table->decimal('longitude', total: 8, places: 5)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
