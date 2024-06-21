<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Services\AddItemWithoutStockService;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    protected $addItemWithoutStockService;

    public function __construct(AddItemWithoutStockService $addItemWithoutStockService)
    {
        $this->addItemWithoutStockService = $addItemWithoutStockService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'description' => 'required|string|min:10|max:250',
            'price' => 'required|numeric|min:50',
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($validator->fails()) {
            $data = [
                'mensaje' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        // Crear el producto con inventario inicial usando el servicio
        $item = $this->addItemWithoutStockService->AddItemWithoutStock(
            $request->name,
            $request->description,
            $request->price,
            $request->category_id
        );

        if (!$item) {
            $data = [
                'mensaje' => 'Error al crear el producto o establecer su inventario',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'mensaje' => 'Producto creado exitosamente y stock inicial establecido en 0 en todas las sucursales',
            'producto' => $item,
            'status' => 201
        ];
        return response()->json($data, 201);
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Item::find($id);

        if (!$item) {
            $data = [
                'mensaje' => 'Producto no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $data = [
            'producto' => $item,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            $data = [
                'mensaje' => 'Producto no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|min:3|max:100',
            'description' => 'nullable|string|min:10|max:250',
            'price' => 'nullable|numeric|min:50',
            'category_id' => 'nullable|min:1',
        ]);

        if ($validator->fails()) {
            $data = [
                'mensaje' => 'Error al actualizar el item',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $item->update($request->all());

        $data = [
            'mensaje' => 'Producto actualizado exitosamente',
            'producto' => $item,
            'status' => 200
        ];
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::find($id);

        if (!$item) {
            $data = [
                'mensaje' => 'Producto no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $item->delete();

        $data = [
            'mensaje' => 'Producto eliminado',
            'status' => 200
        ];
        return response()->json($data, 200);
    }
}
