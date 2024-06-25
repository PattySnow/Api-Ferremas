
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Api Ferremas

La API de Ferremas está diseñada para facilitar la operación de un e-commerce especializado en productos de ferretería. Este sistema proporciona diferentes módulos para manejar diversas funcionalidades necesarias para la gestión eficiente de la tienda en línea "Ferremas".

## Tecnología
- Lenguaje: PHP 8.1
- Framework: Laravel 10
- 
## Módulos.

- Autenticación y manejo de usuarios.
- Gestión de roles y permisos.
- Gestión de sucursales.
- Gestión de inventario.
- Gestión de productos y categorías.
- Gestión de carrito de compra.
- Gestión de orden de pago con transacción mediante el gateway de Webpay Plus.
- Gestión de ordenes de despacho.


## Herramientas a utilizar:

- Laragon
- Cmder (Terminal integrado en Laragon)
- HeidiSQL (Cliente MySQL integrado en Laragon)

## Como realizar las pruebas

- Clone el repositorio dentro de la carpeta www de laragon que por defecto se instala en C:\Laragon\www\
- Acceda a Laragon e inicie todos los servicios.
- En HeidiSQL acceda a Laragon.MySQL con el user root y password vacía.
- Cree una base de datos con nombre "ferremas".
- Desde el cmder acceda a la carpeta del proyecto.
     ```bash
    cd Api-Ferremas
    ```
- Ejecute el comando
  ```bash
    php artisan migrate
    ```
   Esto creará las tablas en la base de datos que ya creamos (ferremas).
- Luego para poblar la base de datos con datos de prueba ejecute el comando
  ```bash
    php artisan db:seed
    ```
- Ahora estás listo para probar los endpoints de nuestra api.
  Puedes hacerlo desde Postman o tu herramienta de preferencia.

## Endpoints

A continuación se detallan los endpoints disponibles y cómo puedes probarlos utilizando Postman.
Recuerda usar la URL base http://127.0.0.1:8000 seguido del endpoint a probar.

### Autenticación

1. **Registrar Cliente**
   - **Endpoint**: `POST /api/register`
   - **Descripción**: Registra a un cliente.
   - **Body**: 
     ```json
     {
       "name": "John",
       "email": "john@example.com",
       "password": "password",
       "password_confirmation": "password"
     }
     ```

2. **Registrar Empleado**
   - **Endpoint**: `POST /api/register_employed`
   - **Descripción**: Registra a un empleado (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "name": "Jane",
       "email": "jane@example.com",
       "password": "password"
       "password_confirmation": "password"
     }
     ```

3. **Iniciar Sesión**
   - **Endpoint**: `POST /api/login`
   - **Descripción**: Inicia sesión de un usuario.
   - **Body**:
     ```json
     {
       "email": "john@example.com",
       "password": "password"
     }
     ```

4. **Cerrar Sesión**
   - **Endpoint**: `POST /api/logout`
   - **Descripción**: Cierra sesión de un usuario (requiere autenticación).
   - **Headers**: `Authorization: Bearer {token}`

### Usuarios

1. **Obtener Usuarios**
   - **Endpoint**: `GET /api/users`
   - **Descripción**: Obtiene la lista de usuarios registrados (requiere autenticación y rol administrador).
   - **Headers**: `Authorization: Bearer {token}`

2. **Obtener Usuario por ID**
   - **Endpoint**: `GET /api/users/{id}`
   - **Descripción**: Obtiene un usuario específico por ID (requiere autenticación).
   - **Headers**: `Authorization: Bearer {token}`

3. **Actualizar Usuario**
   - **Endpoint**: `PUT /api/users/{id}`
   - **Descripción**: Actualiza los datos de un usuario (requiere autenticación).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "name": "John Updated",
     }
     ```

4. **Eliminar Usuario**
   - **Endpoint**: `DELETE /api/users/{id}`
   - **Descripción**: Elimina un usuario (requiere autenticación).
   - **Headers**: `Authorization: Bearer {token}`

### Roles

1. **Obtener Roles**
   - **Endpoint**: `GET /api/roles`
   - **Descripción**: Obtiene la lista de roles registrados (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`

2. **Crear Rol**
   - **Endpoint**: `POST /api/roles`
   - **Descripción**: Crea un nuevo rol (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "name": "role_name"
     }
     ```

3. **Obtener Rol por ID**
   - **Endpoint**: `GET /api/roles/{id}`
   - **Descripción**: Obtiene un rol específico por ID (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`

4. **Actualizar Rol**
   - **Endpoint**: `PUT /api/roles/{id}`
   - **Descripción**: Actualiza los datos de un rol (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "name": "updated_role_name"
     }
     ```

5. **Eliminar Rol**
   - **Endpoint**: `DELETE /api/roles/{id}`
   - **Descripción**: Elimina un rol (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`

6. **Asignar Rol a Usuario**
   - **Endpoint**: `POST /api/roles/{user_id}`
   - **Descripción**: Asigna un rol a un usuario (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "role_id": "role_id"
     }
     ```


### Permisos

1. **Obtener Permisos**
   - **Endpoint**: `GET /api/permissions`
   - **Descripción**: Obtiene la lista de permisos registrados (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`

2. **Crear Permiso**
   - **Endpoint**: `POST /api/permissions`
   - **Descripción**: Crea un nuevo permiso (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "name": "permission_name"
     }
     ```

3. **Obtener Permiso por ID**
   - **Endpoint**: `GET /api/permissions/{id}`
   - **Descripción**: Obtiene un permiso específico por ID (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`

4. **Actualizar Permiso**
   - **Endpoint**: `PUT /api/permissions/{id}`
   - **Descripción**: Actualiza los datos de un permiso (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "name": "updated_permission_name"
     }
     ```

5. **Eliminar Permiso**
   - **Endpoint**: `DELETE /api/permissions/{id}`
   - **Descripción**: Elimina un permiso (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`

6. **Asignar Permiso a Rol**
   - **Endpoint**: `POST /api/permissions/assign_role/{role_id}`
   - **Descripción**: Asigna un permiso a un rol (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "permission_id": "ingrese el id"
     }
     ```

7. **Asignar Permiso a Usuario**
   - **Endpoint**: `POST /api/permissions/assign_user/{user_id}`
   - **Descripción**: Asigna un permiso a un usuario (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "permission_id": "ingrese el id"
     }
     ```

8. **Revocar Permiso de Rol**
   - **Endpoint**: `POST /api/permissions/revoke/{role_id}`
   - **Descripción**: Revoca un permiso de un rol (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "permission_id": "ingrese el id"
     }
     ```

9. **Revocar Permiso de Usuario**
   - **Endpoint**: `POST /api/permissions/revoke/user/{user_id}`
   - **Descripción**: Revoca un permiso de un usuario (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "permission_id": "ingrese el id"
     }
     ```

### Productos

1. **Obtener Productos**
   - **Endpoint**: `GET /api/items`
   - **Descripción**: Obtiene todos los productos disponibles.

2. **Obtener Producto por ID**
   - **Endpoint**: `GET /api/items/{id}`
   - **Descripción**: Obtiene un producto específico por ID.

3. **Crear Producto**
   - **Endpoint**: `POST /api/items`
   - **Descripción**: Crea un nuevo producto (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "name": "Hammer",
       "description": "A heavy-duty hammer",
       "price": 5000,
       "category_id": 1
     }
     ```

4. **Actualizar Producto**
   - **Endpoint**: `PUT /api/items/{id}`
   - **Descripción**: Actualiza un producto (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "name": "Updated Hammer",
       "description": "An updated heavy-duty hammer",
       "price": 1000,
       "category_id": 1
     }
     ```

5. **Eliminar Producto**
   - **Endpoint**: `DELETE /api/items/{id}`
   - **Descripción**: Elimina un producto (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
  
### Categorías de Productos

1. **Obtener Categorías**
   - **Endpoint**: `GET /api/categories`
   - **Descripción**: Obtiene todas las categorías.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)

2. **Obtener Categoría por ID**
   - **Endpoint**: `GET /api/categories/{id}`
   - **Descripción**: Obtiene una categoría específica por ID.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)

3. **Crear Categoría**
   - **Endpoint**: `POST /api/categories`
   - **Descripción**: Crea una nueva categoría.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)
   - **Body**:
     ```json
     {
       "name": "category_name"
     }
     ```

4. **Actualizar Categoría**
   - **Endpoint**: `PUT /api/categories/{id}`
   - **Descripción**: Actualiza una categoría.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)
   - **Body**:
     ```json
     {
       "name": "updated_category_name"
     }
     ```

5. **Eliminar Categoría**
   - **Endpoint**: `DELETE /api/categories/{id}`
   - **Descripción**: Elimina una categoría.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)
### Sucursales

1. **Obtener Sucursales**
   - **Endpoint**: `GET /api/branches`
   - **Descripción**: Obtiene todas las sucursales.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)

2. **Obtener Sucursal por ID**
   - **Endpoint**: `GET /api/branches/{id}`
   - **Descripción**: Obtiene una sucursal específica por ID.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)

3. **Crear Sucursal**
   - **Endpoint**: `POST /api/branches`
   - **Descripción**: Crea una nueva sucursal.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)
   - **Body**:
     ```json
     {
       "name": "branch_name",
       "address": "branch_address"
     }
     ```

4. **Actualizar Sucursal**
   - **Endpoint**: `PUT /api/branches/{id}`
   - **Descripción**: Actualiza una sucursal.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)
   - **Body**:
     ```json
     {
       "name": "updated_branch_name",
       "address": "updated_branch_address"
     }
     ```

5. **Eliminar Sucursal**
   - **Endpoint**: `DELETE /api/branches/{id}`
   - **Descripción**: Elimina una sucursal.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)
### Inventario

1. **Obtener Inventario por Sucursal**
   - **Endpoint**: `GET /api/inventories/{branch_id}`
   - **Descripción**: Obtiene el inventario de una sucursal específica.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)

2. **Obtener Inventario por Sucursal y Producto**
   - **Endpoint**: `GET /api/inventories/{branch_id}/{item_id}`
   - **Descripción**: Obtiene el inventario de una sucursal y un producto específico.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)

3. **Actualizar Inventario**
   - **Endpoint**: `PUT /api/inventories/{branch_id}/{item_id}`
   - **Descripción**: Actualiza el inventario de una sucursal y un producto específico.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)
   - **Body**:
     ```json
     {
       "quantity": "updated_quantity"
     }
     ```

4. **Reiniciar Inventario**
   - **Endpoint**: `PATCH /api/inventories/{branch_id}/{item_id}`
   - **Descripción**: Reinicia el stock de una sucursal y un producto específico a 0.
   - **Headers**: `Authorization: Bearer {token}` (si es necesario)
### Carrito de Compras

1. **Mostrar Carrito**
   - **Endpoint**: `GET /api/cart`
   - **Descripción**: Muestra el carrito de compras del usuario autenticado (requiere autenticación y rol de cliente).
   - **Headers**: `Authorization: Bearer {token}`

2. **Agregar Productos al Carrito**
   - **Endpoint**: `POST /api/cart/add_items`
   - **Descripción**: Agrega productos al carrito de compras del usuario autenticado.
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "items": [
         {
           "item_id": "item_id",
           "quantity": "quantity"
         }
       ],
       "delivery_type": "Pick Up" // or "Shipping"
     }
     ```

3. **Remover Producto del Carrito**
   - **Endpoint**: `DELETE /api/cart/remove_item/{item_id}`
   - **Descripción**: Remueve un producto del carrito de compras del usuario autenticado.
   - **Headers**: `Authorization: Bearer {token}`

4. **Vaciar Carrito**
   - **Endpoint**: `DELETE /api/cart/empty_cart`
   - **Descripción**: Vacía todos los productos del carrito de compras del usuario autenticado.
   - **Headers**: `Authorization: Bearer {token}`

5. **Cambiar Sucursal de Compra**
   - **Endpoint**: `PATCH /api/cart/change_branch/{branch_id}`
   - **Descripción**: Cambia la sucursal donde el usuario desea comprar.
   - **Headers**: `Authorization: Bearer {token}`

### Proceso de Pago

1. **Iniciar Proceso de Compra en Webpay**
   - **Endpoint**: `POST /api/webpay`
   - **Descripción**: Inicia el proceso de compra en Webpay (requiere autenticación y rol de cliente).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**: No es necesario un body específico, solo la autenticación y tener productos agregados en el carrito.
   - Esta operación te devolvera una URL de transbank la cual debes abrir en el navegador para simular el proceso de pago. Puedes ver la siguiente [documentación](https://www.transbankdevelopers.cl/documentacion/como_empezar#tarjetas-de-prueba) de la api de transbank la cual proporciona tarjetas de prueba para completar el flujo de compra.

2. **Cancelar Compra en Webpay**
   - **Endpoint**: `PUT /api/webpay/cancel`
   - **Descripción**: Cancela la compra en Webpay (requiere autenticación y rol de cliente).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**: No es necesario un body específico, solo la autenticación y tener un carrito pendiente.

### Órdenes de Compra

1. **Obtener Órdenes de Compra**
   - **Endpoint**: `GET /api/order_details`
   - **Descripción**: Obtiene todas las órdenes de compra (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`

2. **Obtener Orden de Compra por ID**
   - **Endpoint**: `GET /api/order_details/{id}`
   - **Descripción**: Obtiene los detalles de una orden de compra específica por ID (requiere autenticación y permiso de acceso).
   - **Headers**: `Authorization: Bearer {token}`

3. **Actualizar Orden de Compra**
   - **Endpoint**: `PUT /api/order_details/{id}`
   - **Descripción**: Actualiza los detalles de una orden de compra (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "status": "paid"
     }
     ```

4. **Eliminar Orden de Compra**
   - **Endpoint**: `DELETE /api/order_details/{id}`
   - **Descripción**: Elimina una orden de compra (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`
  
### Órdenes de Despacho

Para generar una orden de despacho, debes agregar productos a tu carrito de compras especificando `"delivery_type": "Shipping"`. De esta forma podrás interactuar con los siguientes endpoints:

1. **Obtener Órdenes de Despacho**
   - **Endpoint**: `GET /api/shipping_order`
   - **Descripción**: Obtiene todas las órdenes de despacho.
   - **Headers**: `Authorization: Bearer {token}`

2. **Obtener Orden de Despacho por ID**
   - **Endpoint**: `GET /api/shipping_order/{shippingOrder_id}`
   - **Descripción**: Obtiene una orden de despacho específica por ID (requiere autenticación y permiso de acceso).
   - **Headers**: `Authorization: Bearer {token}`

3. **Actualizar Orden de Despacho**
   - **Endpoint**: `PATCH /api/shipping_order/{shippingOrder_id}`
   - **Descripción**: Actualiza una orden de despacho (requiere autenticación y rol de empleado).
   - **Headers**: `Authorization: Bearer {token}`
   - **Body**:
     ```json
     {
       "status": "preparing"
     }
     ```

4. **Eliminar Orden de Despacho**
   - **Endpoint**: `DELETE /api/shipping_order/{shippingOrder_id}`
   - **Descripción**: Elimina una orden de despacho (requiere autenticación y rol de administrador).
   - **Headers**: `Authorization: Bearer {token}`



### Ejemplo de Uso en Postman

1. **Configurar la Colección**:
   - Crea una nueva colección en Postman llamada "Ferremas API".
   - Añade una nueva solicitud para cada endpoint descrito anteriormente.

2. **Autenticación**:
   - Para endpoints que requieren autenticación, asegúrate de iniciar sesión primero y obtener el token de acceso.
   - Añade el token a las cabeceras de las solicitudes que lo requieran utilizando el formato `Authorization: Bearer {token}`.

3. **Probar Endpoints**:
   - Configura los parámetros, cabeceras, y cuerpos de las solicitudes según las descripciones proporcionadas.
   - Envía las solicitudes y verifica las respuestas.

