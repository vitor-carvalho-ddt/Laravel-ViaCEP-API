# Laravel-ViaCEP-API

A simple Laravel Project to build foundational knowledge using ViaCEP API for fetching data.

ViaCEP API Docs: https://viacep.com.br/

## Setup Instructions

### Prerequisites

- Docker
- Docker Compose

### Steps

1. **Clone the repository:**

    ```sh
    git clone https://github.com/your-username/Laravel-ViaCEP-API.git
    cd Laravel-ViaCEP-API
    ```

2. **Copy the [.env.example](http://_vscodecontentref_/1) file to [.env](http://_vscodecontentref_/2) and edit the database configuration:**

    ```sh
    cp .env.example .env
    ```

    Edit the [.env](http://_vscodecontentref_/3) file to match your database configuration.

3. **Build the Docker containers:**

    ```sh
    docker-compose build
    ```

4. **Start the Docker containers:**

    ```sh
    docker-compose up -d
    ```

5. **Access the application container:**

    ```sh
    docker-compose exec app bash
    ```

6. **Install PHP dependencies:**

    ```sh
    composer install
    ```

7. **Generate the application key:**

    ```sh
    php artisan key:generate
    ```

8. **Run the database migrations:**

    ```sh
    php artisan migrate
    ```

9. **Install Node.js dependencies and build assets:**

    ```sh
    npm install
    npm run build
    ```

10. **Access the application:**

    Open your browser and navigate to `http://localhost:8000`.

## What It Does

This project uses the ViaCEP API to fetch and store Brazilian postal code (CEP) data. Users can search for CEP information by entering a CEP directly or by providing a combination of state (UF), city (localidade), and street (logradouro). The application ensures that each user can only store unique CEP data.

### Features

- Search for CEP information using the ViaCEP API.
- Store CEP data in the database.
- Ensure unique CEP data per user.
- Toggle between searching by CEP and searching by UF, localidade, and logradouro.

### Endpoints

- `GET /ceps` - List all CEPs for the authenticated user.
- `POST /ceps` - Store a new CEP for the authenticated user.
- `POST /ceps/store-multiple` - Store multiple CEPs for the authenticated user based on UF, localidade, and logradouro.