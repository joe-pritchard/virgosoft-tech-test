# VirgoSoft technical assessment project

## Setting up and running the backend

### Clone the repository

```bash
    git clone git@github.com:joe-pritchard/virgosoft-tech-test.git
    cd virgosoft-tech-test
    cp .env.example .env
```

### Install PHP dependencies:

If you have PHP and composer installed locally:

```bash
    composer install
```

Otherwise you will need to do the initial composer install via docker:

- **Linux/Mac**:
   ```bash
   docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$(pwd):/app" \
      composer:2 \
      composer install
  
   ./vendor/bin/sail up -d
   ```
- **Windows**:
   ```bash
   docker run --rm `
      -v "${PWD}:/app" `
      composer:2 `
      composer install
  
   docker-compose up -d
   ```

### Install frontend dependencies:

- Linux/Mac:
   ```bash
   sail npm install
   ```

- Windows:
   ```bash
   docker-compose exec app npm install
   ```

### Set up the database

- Linux/Mac:
   ```bash
   sail artisan key:generate
   sail artisan migrate --seed
   ```

- Windows:
   ```bash
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --seed
   ```

### Allow access to the storage directory

- Linux/Mac:
   ```bash
   sail chmod a+wrx -R storage
   sail artisan storage:link
   ```

- Windows:
   ```bash
   docker-compose exec app chmod a+wrx -R storage
   docker-compose exec app php artisan storage:link
   ```

### Run the frontend build

- Linux/Mac:
   ```bash
   sail npm run build
   ```

- Windows:
   ```bash
   docker-compose exec app npm run build
   ```

### Access the application

Open your web browser and navigate to [http://localhost]() or [http://app.test]() to access the application.
