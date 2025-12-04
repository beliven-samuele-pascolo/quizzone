<?php

namespace App\Livewire;

use App\Enums\QuestionStatus;
use App\Models\Question;
use App\Services\QuizService;
use Livewire\Component;

class TimerController extends Component
{
    public Question $question;

    public bool $isAdmin = false;

    // Ricalcola ogni secondo
    public function render(QuizService $service)
    {
        $remaining = 0;

        if (in_array($this->question->status, [QuestionStatus::Active, QuestionStatus::Buzzed]) && $this->question->timer_ends_at) {
            $diff = now()->diffInSeconds($this->question->timer_ends_at, false);
            $remaining = max(0, (int) $diff);

            if ($diff <= 0 && $this->isAdmin) {
                $service->checkAndCloseIfExpired();

                // forza il refresh del timer
                $this->dispatch('timer-expired');
            }
        }

        return view('livewire.timer-controller', [
            'remaining' => $remaining,
        ]);
    }
}
