---
description: Cómo configurar el ambiente de pruebas local con Docker
---

Para ejecutar este proyecto localmente, sigue estos pasos:

### Pre-requisitos
1. Tener **Docker Desktop** instalado y en ejecución en tu Windows.
2. Una terminal (PowerShell o Git Bash) abierta en la carpeta del proyecto.

### Pasos de Configuración

1. **Preparar el archivo de entorno**
   Copia el archivo de ejemplo:
   ```powershell
   cp .env.example .env
   ```

2. **Levantar los contenedores**
   Este comando descargará las imágenes y construirá el contenedor de Laravel:
   ```powershell
   docker-compose up -d --build
   ```

3. **Instalar dependencias de PHP**
   Ejecuta composer dentro del contenedor:
   ```powershell
   docker-compose exec app composer install
   ```

4. **Generar la clave de la aplicación**
   ```powershell
   docker-compose exec app php artisan key:generate
   ```

5. **Ejecutar las migraciones y crear las vistas SQL**
   Esto creará las tablas y las vistas en la base de datos PostgreSQL local:
   ```powershell
   docker-compose exec app php artisan migrate
   ```

### Verificación
- Accede a la aplicación en: `http://localhost`
- La base de datos PostgreSQL estará disponible en el puerto `5433` de tu localhost.
- El usuario de la BD es `analitica_app` y la contraseña `password` (configurado en `.env`).
