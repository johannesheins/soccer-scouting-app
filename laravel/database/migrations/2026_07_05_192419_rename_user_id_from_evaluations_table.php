<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table){
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->foreignIdFor(User::class, 'created_by')->nullable()->after('comment')->constrained()->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table){
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');

            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
        });
    }
};
