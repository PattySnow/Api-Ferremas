
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Api Ferremas

Esta API cuenta con diferentes modulos para poner en marcha un e-commerse para la ferreteria "Ferremas".
Los modulos disponibles son:

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
  Puedes hacerlo desde tu herramienta favorita o ingresando al workspace con las pruebas que ya tenemos predefinidas en Postman en el siguiente [enlace](https://app.getpostman.com/join-team?invite_code=e0485397be8805dc5cc117a5b1027817&target_code=6b94da356bce630d636b53120882e533)


