<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ClienteService
{
    /**
     * @param array $data
     * @param int|null $idUsuario
     * @param \Illuminate\Http\UploadedFile|null $fotoFile
     * @return Cliente
     * @throws Exception
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

            $data['idUsuario'] = $idUsuario;

            $cliente = Cliente::create(array_merge($data, ['foto' => $fotoPath]));

            DB::commit();
            return $cliente;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erro ao criar cliente: " . $e->getMessage(), [
                'data' => $data,
                'id_usuario' => $idUsuario,
                'foto_uploaded' => (bool)$fotoFile
            ]);
            throw new Exception("Falha ao cadastrar cliente: " . $e->getMessage());
        }
    }

    /**
     * @param Cliente $cliente
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $fotoFile
     * @return Cliente
     * @throws Exception
     */
    public function updateCliente(Cliente $cliente, array $data, $fotoFile = null): Cliente
    {
        DB::beginTransaction();
        try {
            if (isset($data['status']) && $data['status'] === 'Ativo' && $cliente->status !== 'Ativo') {
                $mensalidadeVencida = \App\Models\Mensalidade::where('idCliente', $cliente->idCliente)
                    ->where('status', 'Pendente')
                    ->where('dataVencimento', '<', \Carbon\Carbon::today())
                    ->exists();
                
                if ($mensalidadeVencida) {
                    throw new Exception("Não é possível alterar o status para Ativo. O cliente possui mensalidades vencidas não pagas.");
                }
            }

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

            $data['foto'] = $fotoPath;

            $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);

            $cliente->fill($data)->save();

            DB::commit();
            return $cliente;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar cliente ID {$cliente->idCliente}: " . $e->getMessage(), [
                'data' => $data,
                'cliente_id' => $cliente->idCliente,
                'foto_uploaded' => (bool)$fotoFile
            ]);
            throw new Exception("Falha ao atualizar cliente: " . $e->getMessage());
        }
    }

    /**
     * @param Cliente $cliente
     * @return bool
     * @throws Exception
     */
    public function deleteCliente(Cliente $cliente): bool
    {
        DB::beginTransaction();
        try {
            if ($cliente->foto) {
                Storage::disk('public')->delete($cliente->foto);
            }

            if ($cliente->faceDescriptor) {
                $cliente->faceDescriptor->delete();
            }

            $deleted = $cliente->delete();
            DB::commit();
            return $deleted;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir cliente ID {$cliente->idCliente}: " . $e->getMessage(), ['cliente_id' => $cliente->idCliente]);
            throw new Exception("Falha ao excluir cliente: " . $e->getMessage());
        }
    }

    /**
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