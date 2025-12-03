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

    public string $answerText = '';

    #[On('echo:quiz-channel,.game.updated')]
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
        $this->answerText = '';
    }

    public function resetGame(QuizService $service)
    {
        if (Auth::user()->role !== UserRole::Admin) {
            return;
        }
        $service->resetGame();
    }

    public function attemptBuzz(QuizService $service)
    {
        $service->buzz(Auth::user());
        $this->answerText = '';
    }

    public function writeAnswer(QuizService $service)
    {
        $service->answer(Auth::user(), $this->answerText);
        $this->answerText = '';
    }

    public function verifyAnswer(QuizService $service, bool $isCorrect)
    {
        if (Auth::user()->role !== UserRole::Admin) {
            return;
        }
        $service->handleAnswer($isCorrect);
    }

    public function render(QuizService $service)
    {
        $currentQuestion = $service->getCurrentQuestion();
        $user = Auth::user();
        $buzzedUser = null;
        if ($currentQuestion && $currentQuestion->buzzed_user_id) {
            $buzzedUser = User::find($currentQuestion->buzzed_user_id);
        }
        $gameWinner = User::where('score', '>=', 5)->first();

        return view('livewire.quiz-controller', [
            'question' => $currentQuestion,
            'is_admin' => $user->role === UserRole::Admin,
            'players' => User::where('role', UserRole::Player)->orderByDesc('score')->get(),
            'buzzed_user' => $buzzedUser,
            'is_the_one_who_buzzed' => $user->id === ($buzzedUser ? $buzzedUser->id : null),
            'i_am_banned' => $user->banned,
            'game_winner' => $gameWinner,
        ]);
    }
}
