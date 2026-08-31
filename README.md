# Sistema de Reportes y Gestión de Despacho Aduanero

Aplicación desarrollada en Laravel con Filament para gestionar importaciones, certificados y reportes operativos del área aduanera. El objetivo principal del proyecto es centralizar la carga de información, validar registros, consultar datos históricos y generar reportes ejecutivos por tipo de solicitud, mes y período de emisión.

## Objetivo del proyecto

Este sistema está orientado a apoyar la operación de despacho aduanero mediante:

- Carga y procesamiento de archivos de importación.
- Registro estructurado de certificados y sus items asociados.
- Búsqueda y consulta de registros por distintos criterios.
- Generación de reportes de control de importación.
- Consolidación mensual de indicadores por tipo de solicitud.
- Exportación de reportes a Excel para análisis y distribución.

La solución se presenta como un panel administrativo basado en Filament, con módulos para administración y reportes, agregando valor para analistas y usuarios que requieren información confiable y exportable.

## Stack tecnológico

El proyecto usa la siguiente base tecnológica:

- PHP: 8.3
- Laravel: 13.8
- Filament: 5.7
- Vite: 8.0.0
- Tailwind CSS: 4.3.3
- Maatwebsite Excel: 4.0
- PHPUnit: 12.5.12
- Composer y NPM para gestión de dependencias y assets

También se incorpora Livewire/Filament para la interfaz de administración y vistas reactivas.

## Funcionalidades principales

- Administración de importaciones.
- Procesamiento de archivos para crear registros de certificados e items.
- Validación de datos y control de registros duplicados o inconsistentes.
- Panel de reportes por rango de fechas.
- Resumen mensual por tipo de solicitud.
- Exportación de resultados en formato Excel.
- Navegación y gestión desde un backend profesional con Filament.

## Requisitos previos

Antes de instalar el proyecto, asegúrate de tener:

- PHP 8.3 o superior
- Composer
- Node.js 18+ / NPM
- Un servidor web local o un entorno como Laragon, XAMPP o similar
- Base de datos compatible con Laravel (por defecto se usa SQLite en el archivo .env.example para desarrollo local)

## Instalación para desarrollo

1. Clona el repositorio:

```bash
git clone <url-del-repositorio>
cd despacho_aduanero
```

2. Instala dependencias de PHP:

```bash
composer install
```

3. Instala dependencias de frontend:

```bash
npm install
```

4. Crea el archivo de entorno:

```bash
copy .env.example .env
```

Si estás en Linux/macOS:

```bash
cp .env.example .env
```

5. Genera la clave de la aplicación:

```bash
php artisan key:generate
```

6. Ejecuta migraciones y seeders:

```bash
php artisan migrate --seed
```

7. Inicia la aplicación en modo desarrollo:

```bash
composer run dev
```

Este comando levanta el entorno con Vite y la aplicación Laravel simultáneamente. Si prefieres hacerlo por separado:

```bash
php artisan serve
npm run dev
```

8. Accede al panel administrativo:

```text
http://localhost:8000/admin
```

> En entornos locales con Laragon, normalmente puedes apuntar al proyecto a la raíz del dominio configurado y luego acceder a /admin.

## Script de desarrollo

El proyecto incluye un script de configuración rápida:

```bash
composer run setup
```

Este comando realiza un flujo base para levantar el proyecto con dependencias, clave de aplicación, migraciones y compilación de assets.

## Despliegue en producción

Para producción, se recomienda preparar el proyecto con entorno seguro y optimizado.

1. Configura las variables de entorno en .env:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

2. Instala solo dependencias de producción:

```bash
composer install --no-dev --optimize-autoloader
```

3. Instala y compila assets frontend:

```bash
npm ci
npm run build
```

4. Ejecuta optimizaciones de Laravel:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan migrate --force
```

5. Configura el servidor web (Nginx o Apache) con PHP-FPM y apunta la raíz a la carpeta public del proyecto.

6. Asegura permisos correctos sobre storage y bootstrap/cache:

```bash
chmod -R 775 storage bootstrap/cache
```

7. Verifica el acceso al panel en la URL pública configurada.

## Estructura relevante del proyecto

- app/Filament: módulos del panel administrativo y páginas del sistema.
- app/Services: servicios de importación y generación de reportes.
- app/Exports: exportación a Excel.
- app/Models: modelos del negocio (certificados, importaciones, auditoría, etc.).
- database/migrations: definición de esquemas.
- resources/js y resources/css: assets frontend.
- public/build: build compilado por Vite.

## Variables de entorno clave

El proyecto usa variables estándar de Laravel; además, la configuración base viene en .env.example. Revísalo antes de poner el proyecto en funcionamiento.

Importante:

- APP_KEY debe estar generado.
- DB_CONNECTION debe apuntar a la base de datos adecuada.
- APP_URL debe corresponder al dominio de despliegue.
- En producción, APP_DEBUG debe estar en false.

## Configuración para archivos de 50 MB

Para permitir la carga de archivos de hasta 50 MB, la configuración del `php.ini` debe incluir lo siguiente:

```ini
upload_max_filesize = 50M
post_max_size = 55M
memory_limit = 256M
max_execution_time = 600
max_input_time = 600
```

> `upload_max_filesize` define el tamaño máximo de cada archivo y `post_max_size` debe ser mayor para aceptar el payload total del request.

Si usas Livewire para cargas temporales de archivos, también debes configurar la variable en el `.env`:

```env
LIVEWIRE_TEMPORARY_FILE_UPLOAD_MAX_SIZE=50M
```

También es válido usar el valor en KB:

```env
LIVEWIRE_TEMPORARY_FILE_UPLOAD_MAX_SIZE=51200
```

En entornos locales con Laragon, normalmente esta configuración se realiza sobre el `php.ini` de la versión de PHP activa y luego se reinicia el servidor local.

## Mantenimiento y buenas prácticas

- Mantén Composer y NPM actualizados.
- No publiques .env en repositorios compartidos.
- Usa migraciones para alterar estructuras de base de datos.
- Ejecuta pruebas antes de desplegar cambios importantes:

```bash
php artisan test
```

## Licencia

Este proyecto se distribuye bajo la licencia MIT.
