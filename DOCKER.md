# Docker Setup Guide

This guide provides complete Docker setup instructions for the project, using Laravel Sail.

## Prerequisites

- Docker Desktop for Mac
- Git (pre-installed on most modern operating systems)

## Quick Setup

From your terminal, clone the repository and run the setup script:

```bash
git clone <repository-url>
cd lawnstarter-exercise
./docker-setup.sh
```

The script automates the entire setup:
- Starts all Docker containers.
- Installs backend and frontend dependencies.
- Builds frontend assets.
- Runs database migrations.
- Starts background queue and schedule workers.

Once complete, access the application at <http://localhost:8080> (or the `APP_PORT` set in your `.env` file).

## What's Included

The Docker environment consists of three services:
- **Application**: An image with PHP 8.4, Node.js 22, and Nginx.
- **Database**: PostgreSQL 17 with a persistent volume for data.
- **Cache**: Redis for caching, session storage, and job queues.

## Manual Commands

For more granular control, you can run the setup steps manually.

```bash
# Start all containers in detached mode
./vendor/bin/sail up -d

# Install Node.js dependencies and build assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

# Run database migrations
./vendor/bin/sail artisan migrate

# Start background workers (use separate terminal sessions)
./vendor/bin/sail artisan queue:work
./vendor/bin/sail artisan schedule:work
```

## Common Development Commands

```bash
# View running containers
./vendor/bin/sail ps

# Check container logs
./vendor/bin/sail logs

# Stop all services
./vendor/bin/sail down

# Run the test suite
./vendor/bin/sail test

# Access the PostgreSQL CLI
./vendor/bin/sail psql

# Open a shell inside the application container
./vendor/bin/sail shell
```

## Environment Configuration

The `.env` file is pre-configured to work with Docker. The default `APP_PORT` is `8080`. If this port is taken, you can change it in your `.env` file.

## Troubleshooting

- **Docker not running**: Ensure Docker Desktop is started and has finished initializing.
- **Port conflicts**: Change `APP_PORT` in your `.env` file to an available port.
- **Container issues**: Run `./vendor/bin/sail down && ./vendor/bin/sail up -d` to fully restart the services.

## Convenience Alias

Add this to your shell profile for shorter commands:

```bash
alias sail="./vendor/bin/sail"
```

Then use: `sail up -d`, `sail artisan migrate`, etc.

## Monitoring Jobs & Queues

To verify background jobs and scheduled tasks are working:

```bash
# Check scheduled tasks
./vendor/bin/sail artisan schedule:list

# Process queue jobs manually
./vendor/bin/sail artisan queue:work --once

# Start persistent queue worker
./vendor/bin/sail artisan queue:work &

# Start scheduler (for statistics computation)
./vendor/bin/sail artisan schedule:work &

# Check database for processed data
./vendor/bin/sail artisan tinker --execute="echo 'Query Logs: ' . \App\Models\QueryLog::count();"
```

## Production Notes

This setup is development-focused. For production:

- Set `APP_ENV=production`
- Use managed database services
- Configure proper queue worker scaling
- Set up load balancing
