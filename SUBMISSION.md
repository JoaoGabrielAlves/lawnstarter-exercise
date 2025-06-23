# LawnStarter Exercise - Submission

This submission uses Laravel Sail for a complete Docker-based development environment.

## Requirements Checklist

- [x] **Fully Containerized**: All services run in Docker containers.
- [x] **Mac-Compatible**: Laravel Sail is optimized for macOS.
- [x] **Simple Setup**: A single script handles the entire setup process.

## 🚀 Quick Start

1. Ensure Docker Desktop is running.
2. Run the setup script: `./docker-setup.sh`
3. Open <http://localhost:8080> in your browser.

## Services

- **Application**: PHP 8.4, Node.js, and Nginx
- **Database**: PostgreSQL with persistent data
- **Cache & Queues**: Redis

## How to Test

```bash
# Test the search API endpoint
curl "http://localhost:8080/api/v1/starwars/search/people?query=luke"

# Test the statistics endpoint
curl "http://localhost:8080/api/v1/statistics"

# Run the full test suite
./vendor/bin/sail test
```

## Rationale for Laravel Sail

Laravel Sail was chosen because it is the official, zero-configuration Docker solution for Laravel. It provides a simple, powerful, and Mac-friendly development experience out of the box.

## Files Added

- `docker-compose.yml`: Defines the Docker services.
- `docker-setup.sh`: Automates the setup process.
- `DOCKER.md`: Provides detailed setup instructions.
