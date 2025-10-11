<?php

namespace App\Services;

use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProdutoService
{
    /**
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $imagemFile
     * @return Produto
     * @throws \Exception
     */
    public function createProduto(array $data, $imagemFile = null): Produto
    {
        DB::beginTransaction();
        try {
            $imagemPath = null;
            if ($imagemFile) {
                $imagemPath = Storage::disk('public')->put('produtos_imagens', $imagemFile);
            }

            $produto = Produto::create([
                'nome' => $data['nome'],
                'categoria' => $data['categoria'],
                'preco' => $data['preco'],
                'estoque' => $data['estoque'],
                'descricao' => $data['descricao'] ?? null,
                'imagem' => $imagemPath,
            ]);

            DB::commit();
            return $produto;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao criar produto: " . $e->getMessage(), [
                'data' => $data,
                'imagem_uploaded' => (bool)$imagemFile
            ]);
            throw new \Exception("Falha ao cadastrar produto: " . $e->getMessage());
        }
    }

    /**
     * @param Produto $produto
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $imagemFile
     * @return Produto
     * @throws \Exception
     */
    public function updateProduto(Produto $produto, array $data, $imagemFile = null): Produto
    {
        DB::beginTransaction();
        try {
            $oldImagePath = $produto->imagem;
            $imagePath = $oldImagePath;

            if ($imagemFile) {
                if ($oldImagePath) {
                    Storage::disk('public')->delete($oldImagePath);
                }
                $imagePath = Storage::disk('public')->put('produtos_imagens', $imagemFile);
            } elseif (isset($data['remover_imagem']) && $data['remover_imagem']) {
                if ($oldImagePath) {
                    Storage::disk('public')->delete($oldImagePath);
                }
                $imagePath = null;
            }

            $produto->fill(array_merge($data, ['imagem' => $imagePath]))->save();

            DB::commit();
            return $produto;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao atualizar produto ID {$produto->idProduto}: " . $e->getMessage(), [
                'data' => $data,
                'produto_id' => $produto->idProduto,
                'imagem_uploaded' => (bool)$imagemFile
            ]);
            throw new \Exception("Falha ao atualizar produto: " . $e->getMessage());
        }
    }

    /**
     * @param Produto $produto
     * @throws \Exception
     */
    public function deleteProduto(Produto $produto): bool
    {
        DB::beginTransaction();
        try {
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }

            $deleted = $produto->delete();
            DB::commit();
            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir produto ID {$produto->idProduto}: " . $e->getMessage(), ['produto_id' => $produto->idProduto]);
            throw new \Exception("Falha ao excluir produto: " . $e->getMessage());
        }
    }

    /**
     * @param int|null $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllProdutos(?int $perPage = null)
    {
        if ($perPage) {
            return Produto::paginate($perPage);
        }
        return Produto::all();
    }

    /**
     * @param int $id
     * @return Produto|null
     */
    public function getProdutoById(int $id): ?Produto
    {
        return Produto::find($id);
    }
}