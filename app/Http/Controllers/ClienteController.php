<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente; // Importe o Model Cliente
use Illuminate\Support\Facades\Auth; // Para pegar o ID do usuário logado
use Illuminate\Support\Facades\Storage; // Para lidar com armazenamento de arquivos (fotos)

class ClienteController extends Controller
{
    /**
     * Exibe uma lista de clientes. (Corresponde ao seu clientes.php)
     */
    public function index()
    {
        // Pega todos os clientes do banco de dados
        $clientes = Cliente::all(); // Ou Cliente::orderBy('nome')->get(); para ordenar

        // Retorna a view 'clientes.index' e passa a variável $clientes para ela
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Exibe o formulário para criar um novo cliente. (Corresponde ao seu cadastrar_cliente.php)
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Armazena um novo cliente no banco de dados. (Lógica do seu backend/clientes/cadastrar.php)
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados
        $request->validate([
            'cpf' => 'required|string|max:14|unique:Cliente,cpf', // Unique na tabela Cliente
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefone' => 'required|string|max:20',
            'dataNascimento' => 'required|date',
            'plano' => 'required|in:Assinante,Nao Assinante', // Ou 'Não Assinante' dependendo de como você salvou no ENUM
            'foto' => 'nullable|image|max:2048', // Valida se é uma imagem e o tamanho (2MB)
        ], [
            'cpf.unique' => 'Este CPF já está cadastrado.',
            // Mensagens de erro personalizadas para outras regras podem ser adicionadas aqui
        ]);

        $pathFoto = null;
        if ($request->hasFile('foto')) {
            // Armazena a foto na pasta 'public/clientes_fotos' e pega o caminho
            $pathFoto = $request->file('foto')->store('clientes_fotos', 'public');
            // O caminho armazenado será 'clientes_fotos/nome_do_arquivo.jpg'
        }

        try {
            // Cria o cliente usando o Model Eloquent
            Cliente::create([
                'cpf' => $request->cpf,
                'nome' => $request->nome,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'dataNascimento' => $request->dataNascimento,
                'plano' => $request->plano,
                'status' => 'Ativo', // Status inicial, conforme RF09
                'foto' => $pathFoto, // Salva o caminho da foto no banco de dados
                'idUsuario' => Auth::id(), // Salva o ID do usuário logado que está cadastrando
            ]);

            // Redireciona com uma mensagem de sucesso
            return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');

        } catch (\Exception $e) {
            // Em caso de erro, redireciona de volta com mensagem de erro
            // Pode ser útil logar $e->getMessage() para depuração
            return back()->withInput()->with('error', 'Erro ao cadastrar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de um cliente específico. (Não solicitado no seu doc, mas bom ter)
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Exibe o formulário para editar um cliente existente. (Corresponde ao seu editar_cliente.php)
     */
    public function edit(Cliente $cliente) // O Laravel automaticamente encontra o cliente pelo ID/CPF passado na rota
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Atualiza um cliente existente no banco de dados. (Lógica do seu backend/clientes/editar.php)
     */
    public function update(Request $request, Cliente $cliente)
    {
        // 1. Validação dos dados
        $request->validate([
            // unique:NomeDaTabela,coluna,ID_DO_REGISTRO_ATUAL
            // Isso permite que o CPF do próprio cliente seja mantido sem gerar erro de unicidade
            'cpf' => 'required|string|max:14|unique:Cliente,cpf,' . $cliente->idCliente . ',idCliente',
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefone' => 'required|string|max:20',
            'dataNascimento' => 'required|date',
            'plano' => 'required|in:Assinante,Nao Assinante',
            'foto' => 'nullable|image|max:2048', // Valida se é uma imagem e o tamanho (2MB)
        ]);

        $data = $request->except(['_token', '_method', 'foto']); // Pega todos os dados exceto token e método

        if ($request->hasFile('foto')) {
            // Exclui a foto antiga se existir
            if ($cliente->foto) {
                Storage::disk('public')->delete($cliente->foto);
            }
            // Armazena a nova foto
            $data['foto'] = $request->file('foto')->store('clientes_fotos', 'public');
        } elseif ($request->input('remover_foto')) { // Se houver um checkbox para remover foto
            if ($cliente->foto) {
                Storage::disk('public')->delete($cliente->foto);
                $data['foto'] = null; // Remove a referência da foto no banco de dados
            }
        }


        try {
            // Atualiza o cliente usando o Model Eloquent
            $cliente->update($data);

            // Redireciona com uma mensagem de sucesso
            return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso!');

        } catch (\Exception $e) {
            // Em caso de erro, redireciona de volta com mensagem de erro
            return back()->withInput()->with('error', 'Erro ao atualizar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Remove um cliente do banco de dados. (Lógica do seu backend/clientes/excluir.php)
     */
    public function destroy(Cliente $cliente)
    {
        try {
            // Exclui a foto do cliente se existir
            if ($cliente->foto) {
                Storage::disk('public')->delete($cliente->foto);
            }

            // Exclui o cliente usando o Model Eloquent
            $cliente->delete();

            // Redireciona com uma mensagem de sucesso
            return redirect()->route('clientes.index')->with('success', 'Cliente excluído com sucesso!');

        } catch (\Exception $e) {
            // Em caso de erro, redireciona de volta com mensagem de erro
            return back()->with('error', 'Erro ao excluir cliente: ' . $e->getMessage());
        }
    }
}