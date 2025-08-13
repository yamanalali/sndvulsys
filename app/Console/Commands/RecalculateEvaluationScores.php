<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VolunteerEvaluation;

class RecalculateEvaluationScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluations:recalculate-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate overall scores for all volunteer evaluations based on new 5-question system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('إعادة حساب النتائج الإجمالية لجميع التقييمات...');

        $evaluations = VolunteerEvaluation::all();
        $updated = 0;

        foreach ($evaluations as $evaluation) {
            $oldScore = $evaluation->overall_score;
            $newScore = $evaluation->calculateOverallScore();
            
            if ($oldScore != $newScore) {
                $evaluation->update(['overall_score' => $newScore]);
                $updated++;
                
                $this->line("تقييم #{$evaluation->id}: {$oldScore} → {$newScore}");
            }
        }

        $this->info("تم تحديث {$updated} تقييم من أصل {$evaluations->count()}");
        $this->info('انتهت عملية إعادة الحساب بنجاح!');

        return Command::SUCCESS;
    }
}

