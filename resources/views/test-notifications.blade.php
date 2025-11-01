@extends('layouts.app')

@section('title', 'Teste de Notificações')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Teste do Sistema de Notificações Popup</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <button onclick="window.showSuccess('Operação realizada com sucesso!')" 
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
            Notificação de Sucesso
        </button>
        
        <button onclick="window.showError('Ocorreu um erro durante a operação!')" 
                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
            Notificação de Erro
        </button>
        
        <button onclick="window.showWarning('Atenção: Verifique os dados inseridos!')" 
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
            Notificação de Aviso
        </button>
        
        <button onclick="window.showInfo('Informação importante para o usuário!')" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Notificação de Info
        </button>
    </div>
    
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Teste com Múltiplas Notificações</h2>
        <button onclick="testMultipleNotifications()" 
                class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded">
            Mostrar Múltiplas Notificações
        </button>
    </div>
    
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Teste com Notificação Personalizada</h2>
        <button onclick="testCustomNotification()" 
                class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded">
            Notificação Personalizada
        </button>
    </div>
    
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Limpar Todas as Notificações</h2>
        <button onclick="window.notificationManager.clear()" 
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Limpar Todas
        </button>
    </div>
</div>

<script>
function testMultipleNotifications() {
    window.showSuccess('Primeira notificação de sucesso!');
    setTimeout(() => window.showError('Segunda notificação de erro!'), 500);
    setTimeout(() => window.showWarning('Terceira notificação de aviso!'), 1000);
    setTimeout(() => window.showInfo('Quarta notificação de informação!'), 1500);
}

function testCustomNotification() {
    window.showNotification('success', 'Título Personalizado', 'Esta é uma mensagem personalizada com título e duração específica!', 8000);
}
</script>
@endsection