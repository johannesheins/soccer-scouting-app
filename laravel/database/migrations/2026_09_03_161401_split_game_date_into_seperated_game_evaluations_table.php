<?php

use App\CustomSchema\EvaluationSchema;
use App\Models\Club;
use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        EvaluationSchema::create('evaluations', function (Blueprint $table): void {
            $table->id();
            $table->date('date')->nullable();
            $table->string('strengths', 255)->nullable();
            $table->string('weaknesses', 255)->nullable();
            $table->tinyText('remarks')->nullable();
            $table->foreignId('recommendation_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->tinyText('comment')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::table('game_evaluations', function (Blueprint $table){
            $table->foreignIdFor(Player::class)->nullable()->after('id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Club::class, 'home_team_id')->nullable()->after('player_id')->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignIdFor(Club::class, 'guest_team_id')->nullable()->after('home_team_id')->constrained()->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('player_evaluations', function (Blueprint $table){
            $table->foreignIdFor(Player::class)->nullable()->after('id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Club::class, 'home_team_id')->nullable()->after('player_id')->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignIdFor(Club::class, 'guest_team_id')->nullable()->after('home_team_id')->constrained()->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        EvaluationSchema::drop('evaluations');
    }
};
