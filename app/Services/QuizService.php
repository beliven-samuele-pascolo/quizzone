<?php

namespace App\Services;

use App\Enums\QuestionStatus;
use App\Enums\UserRole;
use App\Events\QuestionUpdated;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function getCurrentQuestion(): ?Question
    {
        return Question::where('status', QuestionStatus::Active)->first();
    }

    public function startNewQuestion(string $text): void
    {
        // Transaction per garantire che tutte le operazioni siano svolte correttamente
        DB::transaction(function () use ($text) {

            // reset ban giocatori
            User::where('role', UserRole::Player)->update(['banned' => false]);

            Question::create([
                'body' => $text,
                'status' => QuestionStatus::Active,
                'timer_ends_at' => now()->addSeconds(30),
            ]);
        });

        // Notifica a tutti i giocatori che la domanda è stata posta
        QuestionUpdated::dispatch();
    }

    // Resetta il gioco -> azzera punteggi e chiude domande attive
    public function resetGame(): void
    {
        DB::transaction(function () {
            User::query()->update(['score' => 0, 'banned' => false]);
            Question::whereIn('status', [QuestionStatus::Active, QuestionStatus::Buzzed])->update(['status' => QuestionStatus::Closed]);
        });

        // Notifica a tutti i giocatori che la domanda è stata post
        QuestionUpdated::dispatch();
    }
}
