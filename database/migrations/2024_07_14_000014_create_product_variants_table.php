<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->string('unit');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
};
