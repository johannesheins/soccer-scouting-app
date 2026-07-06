<?php

use App\Models\Evaluation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table){
            $table->date('kickoff_date')->nullable()->after('kickoff');
            $table->time('kickoff_time')->nullable()->after('kickoff_date');
        });

        foreach(Evaluation::all() as $evaluation){
            $kickoff = new DateTime($evaluation->kickoff);
            $evaluation->update([
                'kickoff_date' => $kickoff->format('Y-m-d'),
                'kickoff_time' => $kickoff->format('H:i'),
            ]);
        }

        Schema::table('evaluations', function (Blueprint $table){
            $table->dropColumn('kickoff');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table){
            $table->dateTime('kickoff')->nullable()->after('kickoff_time');
        });

        foreach (Evaluation::all() as $evaluation) {
            $kickoff = new DateTime("$evaluation->kickoff_date $evaluation->kickoff_time");
            $evaluation->update([
                'kickoff' => $kickoff->format('Y-m-d H:i:s'),
            ]);
        }

        Schema::table('evaluations', function (Blueprint $table){
            $table->dropColumn('kickoff_date');
            $table->dropColumn('kickoff_time');
        });
    }
};
