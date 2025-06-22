# API de Reservas de Espacios

API RESTful construida con Symfony 7, API Platform y autenticación JWT para gestionar usuarios, espacios y reservas.

# Requisitos

- PHP >= 8.2
- Composer
- Symfony CLI (opcional)
- PostgreSQL o MySQL
- Docker (opcional, para entorno de desarrollo)

# Instalación

```bash
# Clonar el repositorio
git clone https://github.com/johansoncda/reservit-backend.git
cd reservit-backend

# Instalar dependencias
composer install

# Copiar y configurar las variables de entorno
cp .env .env.local
# Edita .env.local con tus credenciales de base de datos, JWT_SECRET, etc.

# Generar claves JWT
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Crear base de datos y migraciones
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Cargar datos iniciales y de ejemplo(fixtures)
php bin/console doctrine:fixtures:load

# Ejecución del servidor
symfony server:start -d
    # o con PHP nativo
php -S localhost:8000 -t public

```

# Documentación de la API

- http://localhost:8000/api/docs #
- El registro de usuario tiene acceso publico.
- El Token se genera al consumir el login_check y se envía a través del header como "Authorization: Bearer {token}".

# Tests

- Se encuentran en la carpeta tests en la raíz del proyecto.
- Crear allí archivos por Entidad o endpoint para conservar el código limpio.
- Se utiliza PHPUnit, entonces las funciones a probar deben llevar "test" como prefijo en el nombre de la función.

```bash
#Para ejecutar los test utilizar:
"php vendor/bin/phpunit" 

```

# Estructura principal

src/
├── Controller/              # Controladores personalizados si necesitas lógica fuera de API Platform(En este caso no se ha usado)
├── DataFixtures/            # Fixtures para cargar datos iniciales (usuarios, espacios, etc.)
├── Entity/                  # Entidades mapeadas con Doctrine (User, Space, Reservation, etc.)
├── Repository/              # Repositorios para consultas personalizadas con Doctrine
├── State/                   # API Platform State Processors/Providers para la inserción y llamado de datos con condiciones
└── Kernel.php               # Entrada principal del framework (puedes dejarlo como viene por defecto)
tests/
└── UserTest.php             # Pruebas de funcionamiento(Crear más archivos para diferentes pruebas)

