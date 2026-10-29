<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flipbooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['pdf', 'images']);
            $table->string('pdf_path')->nullable();   // للكتب من نوع PDF
            $table->json('images')->nullable();       // مسارات الصور مرتبة (للكتب من نوع images)
            $table->boolean('rtl')->default(true);    // تقليب من اليمين لليسار (كتب عربية)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flipbooks');
    }
};
