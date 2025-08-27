@extends('layouts.app')

@section('title', 'Dashboard - Sistema JyM')

@section('content')
    <h1>Bem-vindo ao Sistema JyM, {{ Auth::user()->nome }}!</h1>
    <p>Selecione uma opção no menu lateral para começar.</p>
    {{-- Você pode adicionar mais conteúdo ou widgets aqui --}}
@endsection