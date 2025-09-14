<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClienteService
{
    /**
     * Cria um novo cliente no sistema.
     *
     * @param array $data
     * @param int|null $idUsuario
     * @param \Illuminate\Http\UploadedFile|null $fotoFile
     * @return Cliente
     * @throws \Exception
     */
    public function createCliente(array $data, ?int $idUsuario = null, $fotoFile = null): Cliente
    {
        DB::beginTransaction();
        try {
            $fotoPath = null;
            if ($fotoFile) {
                $fotoPath = Storage::disk('public')->put('clientes_fotos', $fotoFile);
            }

            $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);

            $cliente = Cliente::create([
                'nome' => $data['nome'],
                'cpf' => $data['cpf'],
                'dataNascimento' => $data['dataNascimento'],
                'status' => $data['status'] ?? 'Ativo',
                'foto' => $fotoPath,
                'idUsuario' => $idUsuario,
            ]);

            DB::commit();
            return $cliente;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao criar cliente: " . $e->getMessage(), [
                'data' => $data,
                'id_usuario' => $idUsuario,
                'foto_uploaded' => (bool)$fotoFile
            ]);
            throw new \Exception("Falha ao cadastrar cliente: " . $e->getMessage());
        }
    }

    /**
     * Atualiza um cliente existente.
     *
     * @param Cliente $cliente
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $fotoFile
     * @return Cliente
     * @throws \Exception
     */
    public function updateCliente(Cliente $cliente, array $data, $fotoFile = null): Cliente
    {
        DB::beginTransaction();
        try {
            $oldFotoPath = $cliente->foto;
            $fotoPath = $oldFotoPath;

            if ($fotoFile) {
                if ($oldFotoPath) {
                    Storage::disk('public')->delete($oldFotoPath);
                }
                $fotoPath = Storage::disk('public')->put('clientes_fotos', $fotoFile);
            } elseif (isset($data['remover_foto']) && $data['remover_foto']) {
                if ($oldFotoPath) {
                    Storage::disk('public')->delete($oldFotoPath);
                }
                $fotoPath = null;
            }

            $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);

            $cliente->fill(array_merge($data, ['foto' => $fotoPath]))->save();

            DB::commit();
            return $cliente;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar cliente ID {$cliente->id}: " . $e->getMessage(), [
                'data' => $data,
                'cliente_id' => $cliente->id,
                'foto_uploaded' => (bool)$fotoFile
            ]);
            throw new \Exception("Falha ao atualizar cliente: " . $e->getMessage());
        }
    }

    /**
     * Exclui um cliente.
     *
     * @param Cliente $cliente
     * @return bool
     * @throws \Exception
     */
    public function deleteCliente(Cliente $cliente): bool
    {
        DB::beginTransaction();
        try {
            if ($cliente->foto) {
                Storage::disk('public')->delete($cliente->foto);
            }

            $deleted = $cliente->delete();
            DB::commit();
            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir cliente ID {$cliente->id}: " . $e->getMessage(), ['cliente_id' => $cliente->id]);
            throw new \Exception("Falha ao excluir cliente: " . $e->getMessage());
        }
    }

    /**
     * Obtém todos os clientes, opcionalmente paginados.
     * @param int|null $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllClientes(?int $perPage = null)
    {
        if ($perPage) {
            return Cliente::paginate($perPage);
        }
        return Cliente::all();
    }
}