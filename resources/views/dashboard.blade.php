@extends('layouts.app')

@section('title', 'Dashboard - Sistema JyM')

@section('content')
    <h1>Bem-vindo ao Sistema JyM, {{ Auth::user()->nome }}!</h1>
    <p>Selecione uma opção no menu lateral para começar.</p>

    <hr style="margin: 30px 0; border-top: 1px solid #ccc;">

    <h2 style="color: #333; margin-bottom: 20px;">Ações Rápidas</h2>
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <a href="{{ route('reconhecimento') }}" target="_blank" style="text-decoration: none;">
            <button style="
                padding: 15px 30px;
                font-size: 1.1em;
                background-color: #6a1b9a; /* Deep purple */
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: bold;
                transition: background-color 0.3s ease, transform 0.2s ease;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            ">
                Abrir Tela de Acesso para Clientes
            </button>
        </a>

        <a href="{{ route('clientes.create') }}" style="text-decoration: none;">
            <button style="
                padding: 15px 30px;
                font-size: 1.1em;
                background-color: #007bff; /* Blue */
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: bold;
                transition: background-color 0.3s ease, transform 0.2s ease;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            ">
                Cadastrar Novo Cliente
            </button>
        </a>

    </div>
@endsection