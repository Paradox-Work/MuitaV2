{{-- WRONG: --}}
@extends('layouts.app')

{{-- CORRECT FOR BREEZE: --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Page title goes here --}}
            @if(auth()->user()->hasRole('admin'))
                👑 Admin Dashboard
            @elseif(auth()->user()->hasRole('inspector'))
                🔍 Inspector Dashboard
            @elseif(auth()->user()->hasRole('broker'))
                📄 Broker Dashboard
            @elseif(auth()->user()->hasRole('analyst'))
                📊 Analyst Dashboard
            @else
                Dashboard
            @endif
        </h2>
    </x-slot>
    
    {{-- Your content here --}}
</x-app-layout>