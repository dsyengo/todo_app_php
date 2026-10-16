<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('priority', 10)->default('medium')->after('description');
            $table->timestamp('due_at')->nullable()->after('priority');

            $table->index(['user_id', 'priority']);
            $table->index(['user_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'priority']);
            $table->dropIndex(['user_id', 'due_at']);
            $table->dropColumn(['priority', 'due_at']);
        });
    }
};
