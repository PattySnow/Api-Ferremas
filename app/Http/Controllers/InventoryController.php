<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Branch;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($branch_id)
    {
        $branch = Branch::find($branch_id);
        if (!$branch) {
            return response()->json(['message' => 'Sucursal no encontrada'], 404);
        }

        $inventory = Inventory::with('item')
            ->where('branch_id', $branch_id)
            ->get(['item_id', 'quantity']);

        if ($inventory->isEmpty()) {
            return response()->json(['message' => 'No hay productos en esta sucursal'], 404);
        }

        return response()->json($inventory);
    }

    //Método para mostrar la cantidad de un producto específico en una sucursal específica
    public function show($branch_id, $item_id)
    {
        // Verificar si la sucursal existe
        $branchExists = Branch::find($branch_id);
        if (!$branchExists) {
            return response()->json(['message' => 'Sucursal no encontrada'], 404);
        }

        // Verificar si el producto existe
        $itemExists = Item::find($item_id);
        if (!$itemExists) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Buscar en el inventario la cantidad del producto en la sucursal específica
        $inventory = Inventory::with('item')
            ->where('branch_id', $branch_id)
            ->where('item_id', $item_id)
            ->first(['item_id', 'quantity']);

        if (!$inventory) {
            return response()->json(['message' => 'Producto no encontrado en esta sucursal'], 404);
        }

        return response()->json($inventory);
    }

    // Método para actualizar la cantidad de un producto en una sucursal
    public function update(Request $request, $branch_id, $item_id)
    {
        // Verificar si la sucursal existe
        $branch = Branch::find($branch_id);
        if (!$branch) {
            return response()->json(['message' => 'Sucursal no encontrada'], 404);
        }

        // Verificar si el producto existe
        $item = Item::find($item_id);
        if (!$item) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $inventory = Inventory::where('branch_id', $branch_id)
            ->where('item_id', $item_id)
            ->first();

        if (!$inventory) {
            return response()->json(['message' => 'Producto no encontrado en esta sucursal'], 404);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory->quantity = $request->input('quantity');
        $inventory->save();

        return response()->json([
            'message' => "Cantidad del producto '{$item->name}' en la sucursal '{$branch->name}' actualizada con éxito",
            'data' => $inventory
        ]);
    }


    // Método para establecer la cantidad de un producto en 0
    public function resetStock($branch_id, $item_id)
    {
        // Verificar si la sucursal existe
        $branch = Branch::find($branch_id);
        if (!$branch) {
            return response()->json(['message' => 'Sucursal no encontrada'], 404);
        }

        // Verificar si el producto existe
        $item = Item::find($item_id);
        if (!$item) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $inventory = Inventory::where('branch_id', $branch_id)
            ->where('item_id', $item_id)
            ->first();

        if (!$inventory) {
            return response()->json(['message' => 'Producto no encontrado en esta sucursal'], 404);
        }

        // Establecer el stock del producto en 0
        $inventory->quantity = 0;
        $inventory->save();

        return response()->json([
            'message' => "Cantidad del producto '{$item->name}' en la sucursal '{$branch->name}' establecida en 0",
            'data' => $inventory
        ]);
    }

    
}
