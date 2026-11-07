<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function listAll(): Collection
    {
        return Category::all();
    }

    public function findById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);

        return $category;
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $category = Category::lockForUpdate()->findOrFail($id);

            $total = Product::where('category_id', $category->id)->count();
 
            if ($total > 0) {
                $ativos = Product::where('category_id', $category->id)->where('active', 1)->count();
                $inativos = $total - $ativos;
 
                throw new ConflictHttpException(
                    "Não é possível excluir a categoria \"{$category->name}\": ela tem {$total} produto(s) vinculado(s) "
                    . "({$ativos} ativo(s) e {$inativos} inativo(s)). Mova-os para outra categoria antes de excluir."
                );
            }
 
            $category->delete();
        });
    }
}
