<?php

namespace App\Services;

use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProdutoService
{
    /**
     * Cria um novo produto.
     *
     * @param array $data Dados do produto (nome, categoria, preco, estoque, descricao, imagem)
     * @param \Illuminate\Http\UploadedFile|null $imagemFile Arquivo de imagem opcional
     * @return Produto
     * @throws \Exception
     */
    public function createProduto(array $data, $imagemFile = null): Produto
    {
        DB::beginTransaction();
        try {
            $imagemPath = null;
            if ($imagemFile) {
                // Armazena a imagem na pasta 'public/produtos_imagens'
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
     * Atualiza um produto existente.
     *
     * @param Produto $produto O model Produto a ser atualizado
     * @param array $data Novos dados do produto
     * @param \Illuminate\Http\UploadedFile|null $imagemFile Nova imagem opcional
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
                // Exclui a imagem antiga se existir
                if ($oldImagePath) {
                    Storage::disk('public')->delete($oldImagePath);
                }
                $imagePath = Storage::disk('public')->put('produtos_imagens', $imagemFile);
            } elseif (isset($data['remover_imagem']) && $data['remover_imagem']) {
                // Remove a imagem se a opção for marcada
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
     * Exclui um produto.
     *
     * @param Produto $produto O model Produto a ser excluído
     * @return bool
     * @throws \Exception
     */
    public function deleteProduto(Produto $produto): bool
    {
        DB::beginTransaction();
        try {
            // Exclui a imagem associada, se existir
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
     * Obtém todos os produtos, opcionalmente paginados.
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
     * Obtém um produto pelo ID.
     * @param int $id
     * @return Produto|null
     */
    public function getProdutoById(int $id): ?Produto
    {
        return Produto::find($id);
    }
}