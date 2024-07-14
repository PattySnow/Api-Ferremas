<?php

namespace App\Http\Controllers;


use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\AddItemsToBranchesService;

class BranchController extends Controller
{
    protected $addItemsToBranchesService;

    public function __construct(AddItemsToBranchesService $addItemsToBranchesService)
    {
        $this->addItemsToBranchesService = $addItemsToBranchesService;
        
    }

    public function index()
    {
        $branches = Branch::all();

        if($branches->isEmpty()){
            $data = [
                'mensaje' => 'No hay sucursales disponibles',
                'status' => 200
            ];
            return response()->json($data);
        }

        return response()->json($branches, 200);
    }

    public function createBranch(Request $request)
    {
        // Validar los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:100',
            'address' => 'required|string|min:3|max:100'
        ]);

        if ($validator->fails()) {
            $data = [
                'mensaje' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400); 
        }

        // Crear la sucursal
        $branch = Branch::create($request->all());

        if (!$branch) {
            $data = [
                'mensaje' => 'Error al crear la sucursal',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        // Utilizar el servicio para asociar los productos con stock 0
        $this->addItemsToBranchesService->addItemsToBranches($branch);

        $data = [
            'mensaje' => 'Sucursal creada exitosamente y productos asociados con cantidad de inventario 0',
            'sucursal' => $branch->toArray(),
            'status' => 201
        ];

        return response()->json($data, 201);
        
    }

   

    /**
     * Store a newly created resource in storage.
     */
//     public function store(Request $request)
// {
//     // Validar los datos de la solicitud
//     $validator = Validator::make($request->all(), [
//         'nombre' => 'required|string|min:3|max:100',
//         'ubicacion' => 'required|string|min:3|max:100'
//     ]);

//     if ($validator->fails()) {
//         $data = [
//             'mensaje'=> 'Error en la validación de los datos',
//             'errors' => $validator->errors(),
//             'status'=> 400
//         ];
//         return response()->json($data, 400); 
//     }

//     // Crear la sucursal
//     $sucursal = Sucursal::create($request->all());

//     if (!$sucursal) {
//         $data = [
//             'mensaje'=> 'Error al crear la sucursal',
//             'status'=> 500
//         ];
//         return response()->json($data, 500);
//     }

//     // Obtener todos los productos
//     $productos = Producto::all();

//     // Asociar los productos con la sucursal con cantidad de inventario 0
//     foreach ($productos as $producto) {
//             Inventario::create([
//             'sucursal_id' => $sucursal->id,
//             'producto_id' => $producto->id,
//             'stock' => 0
//         ]);
//     }

//     $data = [
//         'mensaje' => 'Sucursal creada exitosamente y productos asociados con cantidad de inventario 0',
//         'sucursal' => $sucursal,
//         'status' => 201
//     ];
//     return response()->json($data, 201);
// }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branch = Branch::find($id);
        
        if (!$branch){
            $data = [
                'mensaje' => 'Sucursal no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $data = [
            'sucursal'=> $branch,
            'status' => 200
            ];
            return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branch = Branch::find($id);
    
    if (!$branch){
        $data = [
            'message' => 'Sucursal no encontrada',
            'status' => 404
        ];
        return response()->json($data, 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'nullable|string|min:3|max:100',
        'address' => 'nullable|string|min:3|max:100'
    ]);
    
    if ($validator->fails()) {
        $data = [
            'mensaje'=> 'Error al actualizar la sucursal',
            'errors' => $validator->errors(),
            'status'=> 400
        ];
        return response()->json($data, 400);
    }

    $branch->update($request->all());

    $data = [
        'mensaje' => 'Sucursal actualizada exitosamente',
        'sucursal' => $branch,
        'status' => 200
    ];
    return response()->json($data, 200);
}

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = Branch::find($id);
        
        if (!$branch){
            $data = [
                'mensaje' => 'Sucursal no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $branch->delete();

        $data = [
            'mensaje' => 'Sucursal eliminada',
            'status' => 200
            ];
            return response()->json($data, 200);
    }
    
}
