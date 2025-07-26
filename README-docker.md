# Docker Setup for Agrolink Backend

This guide will help you set up the Agrolink backend using Docker.

## Prerequisites

- Docker and Docker Compose installed on your machine

## Getting Started

1. **Copy the environment file**
   ```bash
   cp .env.docker .env
   ```

2. **Generate application key**
   ```bash
   docker-compose run --rm app php artisan key:generate
   ```

3. **Start the containers**
   ```bash
   docker-compose up -d
   ```

4. **Install PHP dependencies**
   ```bash
   docker-compose exec app composer install
   ```

5. **Run database migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

6. **Seed the database (optional)**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

## Accessing Services

- **Application**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080
  - Username: agrolink
  - Password: secret
  - Server: db

## Useful Commands

- **Stop all containers**: `docker-compose down`
- **View logs**: `docker-compose logs -f`
- **Run Artisan commands**: `docker-compose exec app php artisan [command]`
- **Run Composer commands**: `docker-compose exec app composer [command]`
- **Run tests**: `docker-compose exec app php artisan test`

## Services

- **app**: PHP-FPM 8.1 with Laravel
- **webserver**: Nginx
- **db**: MySQL 8.0
- **phpmyadmin**: PHPMyAdmin for database management

## Volumes

- Database data is persisted in a Docker volume named `dbdata`
- Application code is mounted from your local directory

## Troubleshooting

- If you encounter permission issues, run:
  ```bash
  sudo chown -R $USER:$USER .
  ```

- To rebuild the containers:
  ```bash
  docker-compose down
  docker-compose build --no-cache
  docker-compose up -d
  ```
