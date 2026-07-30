<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['store_id', 'user_id', 'month', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales_targets');
    }
};
