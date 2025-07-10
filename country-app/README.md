# Country Information GraphQL API

This project is a Laravel-based GraphQL web service that provides information about countries, focusing on population density. It consumes an external REST API (`restcountries.com`) and logs API usage to a PostgreSQL database.

## Features

- Fetches country data (name, area, population) from `restcountries.com`.
- Calculates population density for each country.
- Exposes a GraphQL endpoint to retrieve a specified number of countries sorted by population density.
- Logs each request to this endpoint in a `log_use_app` table in a PostgreSQL database.
- Exposes a GraphQL endpoint to list these log entries.

## Technical Stack

- PHP / Laravel
- GraphQL (via Lighthouse PHP)
- PostgreSQL
- Guzzle (for HTTP requests, via Laravel HTTP Client)

## Project Structure

- `app/Services/CountryService.php`: Handles fetching and processing data from the `restcountries.com` API.
- `app/Services/LogService.php`: Handles writing log entries to the database.
- `app/Models/LogEntry.php`: Eloquent model for the `log_use_app` table.
- `graphql/schema.graphql`: Defines the GraphQL schema (types, queries, mutations).
- `app/GraphQL/Queries/`: Contains resolver logic for GraphQL queries.
  - `CountryQuery.php`: Resolver for fetching country data.
  - `LogEntryQuery.php`: Resolver for fetching log data.
- `config/lighthouse.php`: Configuration for the Lighthouse GraphQL library.
- `config/graphql-playground.php`: Configuration for the GraphQL Playground (if used).
- `database/migrations/`: Contains database migrations, including the one for `log_use_app`.

## Installation and Setup

### Prerequisites

- PHP (version ^8.2, as per `composer.json`)
- Composer
- PostgreSQL server
- Node.js & npm (for Laravel frontend asset compilation, if any - though not strictly needed for this backend service)

### Steps

1.  **Clone the repository (or ensure code is in place):**
    ```bash
    # git clone ... (if applicable)
    # cd your-project-directory
    ```

2.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```

3.  **Copy Environment File:**
    If it does not exist, copy `.env.example` to `.env`:
    ```bash
    cp .env.example .env
    ```

4.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```
    *Note: This command might fail for the agent due to environment issues. If so, the user should run it.*

5.  **Configure Database in `.env`:**
    Open the `.env` file and update the database connection details for PostgreSQL. Ensure `DB_DATABASE` is set to `countryapp`.
    Example:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1 # Or your PostgreSQL host
    DB_PORT=5432    # Or your PostgreSQL port
    DB_DATABASE=countryapp
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

6.  **Run Database Migrations:**
    This will create the `log_use_app` table and other standard Laravel tables.
    ```bash
    php artisan migrate
    ```
    *Note: This command might fail for the agent due to environment issues. If so, the user should run it.*

7.  **Ensure Lighthouse Configuration (if not already present):**
    The `config/lighthouse.php` and `config/graphql-playground.php` files should have been published during setup. If they are missing and `php artisan vendor:publish` commands failed for the agent, the user may need to run:
    ```bash
    php artisan vendor:publish --provider="Nuwave\Lighthouse\LighthouseServiceProvider" --tag=config
    php artisan vendor:publish --provider="MLL\GraphQLPlayground\GraphQLPlaygroundServiceProvider" --tag=config
    ```
    *(The GraphQL Playground is optional but helpful for testing).*

## Running the Application

1.  **Start the Laravel Development Server:**
    ```bash
    php artisan serve
    ```
    This will typically start the server at `http://127.0.0.1:8000`.

2.  **Accessing the GraphQL API:**
    - The GraphQL endpoint is available at `/graphql` (e.g., `http://127.0.0.1:8000/graphql`).
    - You can use a GraphQL client (like Postman, Insomnia, or a browser-based playground if installed, e.g., via `mll-lab/laravel-graphql-playground` which is usually available at `/graphql-playground`).

### Example Queries

#### Fetch Top 3 Countries by Population Density:
```graphql
query {
  countries(count: 3) {
    nameCommon
    nameOfficial
    area
    population
    populationDensity
  }
}
```

#### Fetch Log Entries:
```graphql
query {
  logEntries {
    id
    username
    request_timestamp
    num_countries_returned
    countries_details
  }
}
```

## Architecture and Technical Decisions

### Core Architecture
The application follows a standard Laravel project structure, enhanced for GraphQL API delivery using the Lighthouse PHP library. It adheres to principles of Clean Architecture by separating concerns:
-   **Services (`app/Services`)**: Encapsulate business logic.
    -   `CountryService`: Handles all interactions with the `restcountries.com` API, including data transformation and calculation of population density. This keeps the API interaction logic out of the GraphQL resolvers or controllers.
    -   `LogService`: Manages the creation of log entries, abstracting database interaction for logging from the resolvers.
-   **Models (`app/Models`)**: Eloquent models for database interaction (e.g., `LogEntry`).
-   **GraphQL Layer (`app/GraphQL`, `graphql/schema.graphql`):** Managed by Lighthouse.
    -   `schema.graphql`: Schema-first definition of types, queries, and mutations.
    -   Resolvers in `app/GraphQL/Queries` and `app/GraphQL/Mutations` delegate to service classes for actual work.

### Key Technical Decisions
-   **Lighthouse PHP for GraphQL:** Chosen for its schema-first approach, tight integration with Laravel (Eloquent, validation, etc.), and rich feature set (directives, pagination, subscriptions). It promotes a clean way to build GraphQL servers in Laravel.
-   **Laravel HTTP Client (Guzzle wrapper):** Used for consuming the external REST API, providing a fluent and testable interface for HTTP requests.
-   **PostgreSQL:** Selected as the relational database as per requirements. The application is designed to be adaptable to other RDBMS supported by Laravel with minimal changes (primarily `.env` configuration and potentially minor adjustments for DB-specific functions if any were used, though none are at this stage).
-   **Service Layer:** A dedicated service layer (`CountryService`, `LogService`) was implemented to promote high cohesion and low coupling. Resolvers are thin and delegate complex tasks to these services. This aligns with SOLID principles (Single Responsibility Principle for services, Dependency Inversion via constructor injection).
-   **Modularity:**
    -   GraphQL schema and resolvers are organized into their dedicated directories.
    -   Business logic is modularized into service classes.
    -   Database interaction for logging is encapsulated in `LogService`.
-   **Database Adaptability:** By relying on Laravel Eloquent and the standard database configuration mechanism, switching to another supported relational database (e.g., MySQL, SQL Server) primarily involves changing the `.env` configuration and installing the appropriate PHP database driver. No raw SQL queries specific to PostgreSQL have been used.
-   **Logging:** Logging is done directly via the `LogService` upon successful data retrieval in the `countries` query resolver. The `countries_details` field in the log stores a JSON representation of the key details of countries returned, allowing for structured querying if needed in the future, while keeping the main log table schema relatively simple. The username is captured as "anonymous" by default but can be enhanced if authentication is added to the GraphQL layer.

### SOLID Principles
-   **Single Responsibility Principle (SRP):**
    -   `CountryService` is solely responsible for country data operations.
    -   `LogService` is solely responsible for logging operations.
    -   GraphQL resolvers are responsible for handling the GraphQL request/response lifecycle and delegating to services.
-   **Open/Closed Principle (OCP):** The use of services means new queries/mutations or changes to data sources can be added with minimal modification to existing resolver code, by extending services or adding new ones.
-   **Liskov Substitution Principle (LSP):** (Less directly applicable here without inheritance hierarchies in the core logic shown, but interface-based design would uphold this if interfaces were used for services).
-   **Interface Segregation Principle (ISP):** (Similarly, if services implemented interfaces, they would be tailored to specific client needs).
-   **Dependency Inversion Principle (DIP):** Services (`CountryService`, `LogService`) are injected into resolvers (e.g., `CountryQuery`) via constructor injection, allowing for dependencies to be easily managed and mocked for testing. Laravels service container handles the resolution.

This architecture aims for maintainability, testability, and scalability.
---

*Further development could include: more robust error handling, input validation, authentication/authorization, more detailed logging for countries_details (e.g., using a related table or JSONB type in PostgreSQL), and unit/integration tests.*
