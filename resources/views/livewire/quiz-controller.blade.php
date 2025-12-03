@use('App\Enums\QuestionStatus')

<div class="flex justify-center items-start pt-12 min-h-screen bg-zinc-50 dark:bg-zinc-950 p-4">
    
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
            @if($question && $question->status != QuestionStatus::Closed)
                <div class="mb-8 text-center justify-space-center">
                    <h3 class="mb-2 text-zinc-500">LA DOMANDA È:</h3>
                    <h2 class="text-3xl font-bold text-zinc-800 dark:text-zinc-100 leading-tight">
                        {{ $question->body }}
                    </h2>
                </div>

                @if($question->status === QuestionStatus::Active)
                    <div x-data="
                        { 
                            expiry: {{ $question->timer_ends_at?->timestamp * 1000 }}, 
                            remaining: 0,
                            update() {
                                this.remaining = Math.max(0, Math.ceil((this.expiry - Date.now()) / 1000));
                            }
                        }" 
                        x-init="update(); setInterval(() => update(), 100)"
                        class="absolute top-4 right-4 font-mono text-xl text-zinc-400"
                    >
                        <span x-text="remaining"></span>s
                    </div>
                @endif
            @else
                <div class="text-zinc-400 italic text-xl animate-pulse">
                    In attesa della domanda...
                </div>
            @endif
        </div>

        @if($is_admin)
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

                @elseif($question->status === QuestionStatus::Buzzed)
                    <div class="flex gap-4 justify-center">
                        todo
                    </div>

                @else
                    <div class="text-center text-sm text-zinc-400">
                        Attendere prenotazioni...
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
