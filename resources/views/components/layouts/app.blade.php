<?php /**
 * Wrapper component so Livewire can reference `components.layouts.app`.
 * It simply renders the existing `layouts.app` structure and forwards the slot and header.
 */ ?>

@props(['header' => null])

@includeWhen(true, 'layouts.app', ['header' => $header, 'slot' => $slot])
