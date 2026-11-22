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
        // create_bids_table
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('developer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('price', 15, 2);
            $table->unsignedInteger('duration_days');
            $table->text('proposal');
            $table->enum('status', ['pending', 'won', 'rejected'])->default('pending');
            $table->timestamps();
            $table->unique(['project_id', 'developer_id']); // 1 developer = 1 bid per proyek
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
