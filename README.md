# Laravel Docker Project

This project is a Laravel application running inside Docker containers. It uses PHP-FPM, MySQL, and Nginx, with cron jobs configured for scheduled tasks.

## Prerequisites

Before you begin, make sure you have the following installed on your machine:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)
- [Postman](https://www.postman.com/downloads/) (for testing APIs)

## Project Setup

Follow the steps below to get your Laravel project up and running with Docker.

### 1. Clone the Repository

Clone the repository to your local machine:

    git clone https://github.com/anjoylobo/NewsAggregator.git
    cd your-laravel-repo

### 2. Set Up Environment Variables
Copy the .env.example file to .env:

    cp .env.example .env

Modify the .env file with your specific configuration for the database, application key, and other environment variables.

### 3. Build and Start the Docker Containers
Run the following command to build and start your Docker containers:

    docker-compose up --build -d

This will start the Laravel application with PHP-FPM, MySQL, and Nginx. The containers will run in the background (-d).

### 4. Install Composer Dependencies
Once the containers are running, you'll need to install the project dependencies using Composer:

    docker-compose exec app composer install

### 5. Set Permissions
Ensure the correct permissions are set for Laravel's storage and cache directories:

    docker-compose exec app chmod -R 775 storage bootstrap/cache

### 6. Run Migrations
Run your database migrations using Artisan:

    docker-compose exec app php artisan migrate

### 7. Access the Application
Your Laravel application should now be accessible at http://localhost:8080 in your browser.

### 8. Swagger Documentation
#### Install Swagger (Laravel)
To enable API documentation with Swagger, ensure you have the l5-swagger package installed. If not, run:

    docker-compose exec app composer require "darkaonline/l5-swagger"
#### Generate API Docs
Run the following Artisan command to generate the Swagger JSON documentation:

    docker-compose exec app php artisan l5-swagger:generate
#### Once generated, you can access the Swagger UI at:

    http://localhost:8080/api/documentation
This will display the interactive API documentation, where you can view available routes, parameters, and test API endpoints directly from the UI.

### 9. Postman Collection
#### Import the Postman Collection
To test the API, we have provided a Postman collection that contains all the necessary API endpoints for your Laravel project.
For API testing and exploration, use the Postman collection provided below:

[News Aggregator Postman Collection](https://github.com/anjoylobo/NewsAggregator/blob/main/storage/api-docs/News%20Aggregator.postman_collection.json)

1. Download the collection file by clicking on the link above.
2. Import the collection into Postman:
   - Open Postman.
   - Go to **File > Import**.
   - Select the downloaded `.json` file.
3. Explore and test the API endpoints.

Once imported, you can use the collection to test all available API endpoints directly from Postman.

### 9. Cron Jobs
Cron jobs are configured and will run according to the schedule defined in the crontab file. These jobs are set to run within the Docker container.

### 10. Stopping the Containers
To stop the Docker containers, run:

    docker-compose down
This will stop and remove all containers. If you want to remove the containers and volumes, run:

    docker-compose down -v

## File Structure
1. Dockerfile - Dockerfile for building the Laravel application container.
2. docker-compose.yml - Docker Compose configuration for PHP-FPM, MySQL, and Nginx containers.
3. .env.example - Example environment file for configuring application settings.
4. nginx.conf - Nginx configuration file for routing requests to the Laravel application.
5. crontab - File containing scheduled cron jobs for the application.

## Troubleshooting
1. MySQL connection issues: Ensure that your .env file is properly configured with the correct database connection settings.
2. Permissions issues: Make sure that the storage and bootstrap/cache directories are writable by the web server.
3. Application not loading: Ensure the Docker containers are running by checking with docker ps.

## Contributing
If you'd like to contribute to this project, feel free to fork the repository and submit a pull request. Make sure to follow the coding standards and include appropriate tests with your changes.

### Explanation:

1. **Project Setup**: Step-by-step guide to clone the repository, set up environment variables, and start the Docker containers.
2. **Docker Compose**: Describes how to use `docker-compose` to build and start the application, including installing dependencies and running migrations.
3. **File Structure**: An overview of the files in the project, including the Dockerfile, Nginx configuration, and crontab.
4. **Troubleshooting**: Common issues and how to resolve them.
5. **Contributing**: Encouragement to contribute with clear instructions for forking and submitting pull requests.

Feel free to adjust the URLs, configuration details, or other specifics based on your exact project setup!
