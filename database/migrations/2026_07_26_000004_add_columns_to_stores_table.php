<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('operational_hours')->nullable()->after('photo');
            $table->text('notes')->nullable()->after('operational_hours');
        });
    }
    public function down(): void {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['operational_hours', 'notes']);
        });
    }
};
