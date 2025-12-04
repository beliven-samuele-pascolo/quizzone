<?php

use App\Enums\UserRole;
use App\Livewire\QuizController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can render the Livewirecomponent and see new question input', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    // verifica che il componente Livewire venga caricato e che mostri elementi riservati agli admin
    Livewire::actingAs($admin)
        ->test(QuizController::class)
        ->assertStatus(200)
        ->assertSeeHtml('wire:model="questionText"');
});

test('player can render the Livewire component but cannot see new question input', function () {
    $player = User::factory()->create(['role' => UserRole::Player]);

    // verifica che il componente Livewire venga caricato, ma non mostri elementi riservati agli admin
    Livewire::actingAs($player)
        ->test(QuizController::class)
        ->assertStatus(200)
        ->assertDontSeeHtml('wire:model="questionText"');
});
