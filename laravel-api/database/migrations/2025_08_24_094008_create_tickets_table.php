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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('title');
            $table->string('code')->unique();
            $table->longText('description')->nullable();
            $table->integer('status')->default(0); // 0: Open, 1: In Progress, 2: Resolved, 3: Rejected
            $table->integer('priority')->default(0); // 0: Low, 1: Medium, 2: High
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
