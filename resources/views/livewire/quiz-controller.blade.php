@use('App\Enums\QuestionStatus')

<div class="flex justify-center items-start pt-8 min-h-screen bg-zinc-50 dark:bg-zinc-950 p-4 flex-col gap-6">
    @if($is_admin)
        <flux:button wire:click="resetGame" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300" icon="arrow-path">
            Nuovo gioco
        </flux:button>
    @endif
    <div class="w-full max-w-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-lg overflow-hidden flex flex-col">
        
        <div class="bg-zinc-100 dark:bg-zinc-950 p-4 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex justify-between items-center mb-2">
                <flux:heading size="xl">Quizzone</flux:heading>
                @if($is_admin)
                    <div class="text-xs font-mono text-zinc-500">SEI IL CONDUTTORE, CONDUCI</div>
                @endif
            </div>
            <div class="flex gap-4 overflow-x-auto pb-2">
                @foreach($players as $player)
                    <div class="flex flex-col items-center min-w-[60px] {{ $player->banned ? 'opacity-40 grayscale' : '' }}">
                        <span class="text-[10px] font-bold uppercase truncate max-w-full">{{ $player->name }}</span>
                        <span class="font-mono font-bold {{ $player->score >= 4 ? 'text-green-500' : 'text-zinc-600 dark:text-zinc-400' }}">
                            {{ $player->score }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-8 min-h-[400px] flex flex-col justify-center items-center relative">
            @if($game_winner)
                <div class="text-center animate-bounce space-y-4">
                    <flux:heading size="xl" class="text-green-600">GIOCO TERMINATO!</flux:heading>
                    <div class="text-2xl font-bold">{{ $game_winner->name }}</div>
                    <p>Ha raggiunto 5 punti.</p>
                </div>
            @else
                @if($question && $question->status != QuestionStatus::Closed)
                    <div class="mb-8 text-center justify-space-center">
                        <h3 class="mb-2 text-zinc-500">LA DOMANDA È:</h3>
                        <h2 class="text-3xl font-bold text-zinc-800 dark:text-zinc-100 leading-tight">
                            {{ $question->body }}
                        </h2>
                    </div>

                    @if($question->timer_ends_at)
                        <div class="absolute top-0 right-0 p-4">
                            <livewire:timer-controller :question="$question" :is-admin="$is_admin" :key="'timer-'.$question->id" />
                        </div>
                    @endif

                    @if($question->status === QuestionStatus::Active)
                        @if(!$is_admin)
                            @if(!$i_am_banned)
                                <button wire:click="attemptBuzz" class="bg-red-600 border-8 border-red-800 text-white shadow-2xl hover:scale-105 hover:bg-red-500 active:scale-95 transition-all flex flex-col items-center justify-center group px-8 py-6 rounded-full">
                                    <span class="text-xl font-black tracking-widest group-hover:animate-pulse">PRENOTA</span>
                                    <div wire:loading class="mt-2 text-sm">Invio...</div>
                                </button>
                            @else
                                <div class="text-center p-6 bg-red-100 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                                    <flux:heading size="lg" class="text-red-600">Eliminato</flux:heading>
                                    <p class="text-sm text-red-500">Aspetta il prossimo turno.</p>
                                </div>
                            @endif
                        @endif
                    
                    @elseif($question->status === QuestionStatus::Buzzed)
                        <div class="text-center w-full animate-in zoom-in duration-200">
                            @if($is_the_one_who_buzzed)
                                @if($question->answer === null)
                                    <flux:heading size="xl" class="text-green-600 dark:text-green-500">
                                        Tocca a te rispondere!
                                    </flux:heading>
                                    <p class="text-zinc-500">Ora scrivi la tua risposta qui sotto.</p>
                                    <div class="mt-6 text-center">
                                        <flux:heading size="sm" class="mb-2 text-zinc-500">SCRIVI LA TUA RISPOSTA:</flux:heading>
                                        <div class="flex gap-2 w-full">
                                            <div class="flex-grow">
                                                <flux:input wire:model="answerText" placeholder="La tua risposta..." />
                                            </div>
                                            <flux:button wire:click="writeAnswer" variant="primary" icon="paper-airplane">
                                                Invia
                                            </flux:button>
                                        </div>
                                    </div>
                                @else
                                    <flux:heading size="xl" class="text-green-600 dark:text-green-500">
                                        Hai risposto!
                                    </flux:heading>
                                    <p class="text-zinc-500">Ora attendi la verifica del conduttore.</p>
                                @endif
                            @else
                                <flux:heading size="xl" class="text-yellow-600 dark:text-yellow-500">
                                    {{ $buzzed_user->name }}
                                </flux:heading>
                                <p class="text-zinc-500">Ha prenotato la risposta.</p>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="text-zinc-400 italic text-xl animate-pulse">
                        In attesa della domanda...
                    </div>
                @endif
            @endif
        </div>

        @if($is_admin && !$game_winner)
            <div class="bg-zinc-50 dark:bg-zinc-950 p-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:heading size="sm" class="mb-3 text-indigo-500 uppercase">CONSOLE CONDUTTORE</flux:heading>
                
                @if(!$question || $question->status === QuestionStatus::Pending)
                    <div class="flex gap-2 w-full">
                        <div class="flex-grow">
                            <flux:input wire:model="questionText" placeholder="Scrivi la domanda..." />
                        </div>
                        <flux:button wire:click="startGame" variant="primary" icon="play">
                            Invia
                        </flux:button>
                    </div>

                @elseif($question->status === QuestionStatus::Buzzed && $question->answer !== null)
                    <flux:heading size="sm" class="mt-5 text-zinc-500 uppercase"> La risposta di {{ $buzzed_user->name }} è:</flux:heading>
                    <flux:heading size="lg" class="mb-8">{{ $question->answer }}</flux:heading>
                    <div class="flex gap-4 justify-center">

                        <flux:button wire:click="verifyAnswer(true)" class="w-1/2 !bg-green-600 hover:bg-green-500 text-white border-none">
                            Corretta
                        </flux:button>
                        <flux:button wire:click="verifyAnswer(false)" variant="danger" class="w-1/2">
                            Errata
                        </flux:button>
                    </div>
                @else
                    <div class="text-center my-12 text-sm text-zinc-400">
                        I giocatori stanno pensando...
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
