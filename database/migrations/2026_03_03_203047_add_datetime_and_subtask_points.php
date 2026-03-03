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
        Schema::table('projects', function (Blueprint $table) {
            $table->dateTime('start_date')->nullable()->change();
            $table->dateTime('end_date')->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dateTime('due_date')->nullable()->change();
        });

        Schema::table('subtasks', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->date('due_date')->nullable()->change();
        });

        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    }
};
