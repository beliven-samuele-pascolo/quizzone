<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\QuizService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class QuizController extends Component
{
    public string $questionText = '';

    #[On('echo:quiz-channel,QuestionUpdated')]
    public function refreshGame()
    {
        //
    }

    public function startGame(QuizService $service)
    {
        if (Auth::user()->role !== UserRole::Admin) {
            return;
        }

        $this->validate(['questionText' => 'required|min:5']);

        $service->startNewQuestion($this->questionText);
        $this->questionText = '';
    }

    public function render(QuizService $service)
    {
        $currentQuestion = $service->getCurrentQuestion();
        $user = Auth::user();

        return view('livewire.quiz-controller', [
            'question' => $currentQuestion,
            'is_admin' => $user->role === UserRole::Admin,
            'players' => User::where('role', UserRole::Player)->orderByDesc('score')->get(),
        ]);
    }
}
