<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Branch;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los productos y sucursales
        $productos = Item::all();
        $sucursales = Branch::all();

        foreach ($productos as $producto) {
            foreach ($sucursales as $sucursal) {
                // Verificar si ya existe un registro en la tabla inventories para este producto y sucursal
                $existingInventory = Inventory::where('item_id', $producto->id)
                                              ->where('branch_id', $sucursal->id)
                                              ->first();

                if ($existingInventory) {
                    // Si existe, actualiza la cantidad
                    $existingInventory->quantity = 100;
                    $existingInventory->save();
                } else {
                    // Si no existe, crea un nuevo registro en la tabla inventories
                    Inventory::create([
                        'item_id' => $producto->id,
                        'branch_id' => $sucursal->id,
                        'quantity' => 100,
                    ]);
                }
            }
        }
    }
}
