# Package Dispatch API

API de logistica para gestionar envios, paquetes y mensajeros con Symfony 7.4, PHP 8.3 y PostgreSQL.

## Que problema resuelve

La plataforma permite:

- registrar paquetes y envios
- asignar mensajeros disponibles
- controlar el ciclo de vida del envio
- mantener historial de estados
- consultar listados paginados

El objetivo es evitar asignaciones duplicadas, centralizar la logica de negocio en casos de uso y mantener una base lista para escalar.

## Arquitectura aplicada

Se implemento Clean Architecture por feature:

- `Domain`: reglas de negocio puras, sin Symfony ni Doctrine
- `Application`: casos de uso y DTOs de entrada/salida
- `Infrastructure`: persistencia Doctrine, seguridad JWT y adaptadores tecnicos
- `Presentation`: endpoints REST delgados

### Estructura

- `src/Shared`
- `src/Courier`
- `src/Package`
- `src/Dispatch`

Cada modulo contiene sus capas separadas y el mapeo de Doctrine XML vive dentro de la infraestructura de cada feature.

## Pruebas HTTP

Inclui un archivo `.http` para probar login y el uso del token sin depender de Swagger.

- [API HTTP](/c:/Data/Source/Repos/technical-test-symfony/http/package-dispatch.http)

### Flujo recomendado

1. Ejecuta el login.
2. Copia `response.body.data.token`.
3. Pega ese token en `@token`.
4. Ejecuta las peticiones en orden: `me`, `couriers`, `packages`, `dispatches`, `assign`, `status`, `history`.

## Prueba funcional

Para el test funcional de `GET /api/dispatches/{id}/history`:

1. Crear la base de datos de test si no existe:

```bash
docker compose exec app bash -lc "APP_ENV=test php bin/console doctrine:database:create --if-not-exists"
```

2. Ejecutar la suite funcional:

```bash
docker compose exec app bash -lc "php bin/phpunit --testsuite Functional"
```

El test funcional prepara un esquema mínimo en la base de datos de test, carga los datos necesarios y valida el JSON de respuesta del endpoint protegido con JWT.

## Como levantar el proyecto

1. Levantar los contenedores:

```bash
docker compose up -d --build
```

2. Instalar dependencias dentro del contenedor `app` si hiciera falta:

```bash
docker compose exec app composer install
```

3. Ejecutar migraciones:

```bash
docker compose exec app php bin/console doctrine:migrations:migrate
```

4. Crear o corregir el usuario de prueba:

```bash
docker compose exec app php bin/console app:create-user admin@example.com password --admin
```

Si el usuario ya existe y solo quieres actualizar la contraseña:

```bash
docker compose exec app php bin/console app:reset-user-password admin@example.com password
```

5. Probar la API con el archivo `.http` incluido.

## Base de datos

- Motor: PostgreSQL
- Puerto local: `5432`
- Nombre de base: `package_dispatch`

## Seguridad

- Autenticacion JWT con LexikJWTAuthenticationBundle
- Login: `POST /api/login_check`
- Rutas protegidas con firewall `api`
- Usuario de prueba recomendado: `admin@example.com` / `password`
- Las claves sensibles y secretos locales deben vivir en `app/.env.local`, no en archivos versionados.

## Notas

- La aplicacion corre dentro de Docker.
- El proyecto esta montado en `app/`.
- Los identificadores principales usan UUID.
- Si el login devuelve `Invalid credentials`, recrea el usuario con `app:create-user` o ajusta la clave con `app:reset-user-password`.
