<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('license_plate')->nullable()->after('user_id');
            $table->string('image_path')->nullable()->after('description');
            $table->timestamp('sold_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['license_plate', 'image_path', 'sold_at']);
        });
    }
};
