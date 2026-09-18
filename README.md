# VentasFix — Backoffice de Gestión

Sistema de administración para VentasFix, desarrollado con laravel. este proyecto ncluye autenticación con JWT, gestión de Usuarios, Productos y Clientes, tanto por interfaz web (backoffice) como por API REST protegida.

## Requisitos previos

- **PHP** 8.2 o superior
- **Composer**
- **Node.js** y **npm**
- **Git**

## Instalación paso a paso

### 1. Clonar el repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
cd VentasFix
```

### 2. Instalar las dependencias de PHP

```bash
composer install
```

### 3. Crear el archivo de configuración `.env`

```bash
cp .env.example .env
```

Abre `.env` y confirma que la conexión a la base de datos esté así:

```
DB_CONNECTION=sqlite
```

### 4. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 5. Instalar JWT y generar su clave secreta

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

Este último comando escribe automáticamente la línea `JWT_SECRET=...` en tu `.env`.

> Si el proyecto ya viene con `tymon/jwt-auth` en `composer.json` (porque ya se subió a git), el `composer install` del paso 2 ya lo instala solo, y este paso 5 se reduce a correr únicamente `php artisan jwt:secret`.

### 6. Crear la base de datos

El proyecto usa SQLite.

**Mac / Linux:**
```bash
touch database/database.sqlite
```

**Windows (CMD):**
```bash
type nul > database\database.sqlite
```

**Windows (PowerShell):**
```bash
New-Item database\database.sqlite
```

### 7. Correr las migraciones

```bash
php artisan migrate
```

Deberías ver las tablas `users`, `cache`, `jobs`, `usuarios`, `productos` y `clientes`, todas terminando en `DONE`.

### 8. Instalar y compilar los assets (Tailwind / JS)

```bash
npm install
npm run build
```

Para desarrollo (recompila automáticamente al guardar cambios), usar en su lugar:
```bash
npm run dev
```
en una terminal aparte, dejándola corriendo en paralelo a `php artisan serve`.

### 9. Levantar el servidor

```bash
php artisan serve
```

Abrir el navegador en:
```
http://127.0.0.1:8000
```

Esto redirige automáticamente a `/login`.

## Crear el primer usuario del sistema

Como el sistema exige login desde el primer momento, hay que crear un usuario antes de poder entrar al backoffice. Se puede hacer con una petición directa a la API (por ejemplo con Postman):

```
POST http://127.0.0.1:8000/api/register
Content-Type: application/json

{
    "rut": "12345678-9",
    "nombre": "Nombre",
    "apellido": "Apellido",
    "email": "usuario@ventasfix.cl",
    "password": "una_clave_de_8_o_mas_caracteres"
}
```
**Importante:** el correo debe terminar obligatoriamente en `@ventasfix.cl`, y el rut debe ser un RUT chileno válido (con dígito verificador correcto), o la petición será rechazada.

### Usuario ya creado para la evaluación

Para facilitar la revisión, ya cree una cuenta lista para usar en el backoffice:

- **Correo:** `profesorbyron@ventasfix.cl`
- **Contraseña:** `profesorpro2026`

No es necesario registrar un usuario nuevo — se puede iniciar sesión directamente en `/login` con estas credenciales.

## Estructura del proyecto

- **Autenticación (JWT):** `POST /api/register`, `POST /api/login`, `POST /api/logout` (protegida). Clave cifrada con `Hash::make()`.
- **API REST protegida con JWT** (requiere header `Authorization: Bearer <token>`):
  - `GET|POST /api/usuarios`, `GET|PUT|DELETE /api/usuarios/{id}`
  - `GET|POST /api/productos`, `GET|PUT|DELETE /api/productos/{id}`
  - `GET|POST /api/clientes`, `GET|PUT|DELETE /api/clientes/{id}`
- **Backoffice web:** `/dashboard`, `/usuarios`, `/productos`, `/clientes` — todas consumen la API interna mediante `fetch()` desde JavaScript, usando el token guardado en `localStorage` tras el login.
- **Diseño atómico:** componentes reutilizables en `resources/views/components/atoms/` (`campo.blade.php`, `boton.blade.php`).
- **Regla de validación personalizada:** `app/Rules/RutValido.php`, implementa el algoritmo de módulo 11 para verificar RUTs chilenos.

## Problemas comunes al levantar el proyecto

| Error | Solución |
|---|---|
| `MissingAppKeyException` | Correr `php artisan key:generate` |
| `ViteManifestNotFoundException` | Correr `npm install` y `npm run build` |
| `Token no encontrado` / `401` al usar la API | Falta el header `Authorization: Bearer <token>`, o no se ha iniciado sesión |
| `Token expirado` | El JWT vence pasado un tiempo; volver a iniciar sesión en `/login` |
| Error de `vendor/autoload.php` no encontrado | Correr `composer install` |
| El backoffice redirige siempre a `/login` | No hay ningún usuario creado aún; ver sección "Crear el primer usuario del sistema" |