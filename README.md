# LawnStarter Exercise - Star Wars API

A full-stack application built with Laravel and React that searches the Star Wars API and tracks usage statistics.

## 📚 Documentation

- **[Docker Setup Guide](DOCKER.md)** - Comprehensive Docker setup instructions
- **[Technical Decisions](TECHNICAL_DECISIONS.md)** - Architecture and technology choices explained
- **[Submission Overview](SUBMISSION.md)** - Quick overview for reviewers
- **[Short Questions Answers](SHORT_QUESTIONS_ANSWERS.md)** - Responses to assignment questions
- **[Exercise Feedback](FEEDBACK.md)** - Feedback on the exercise experience

## Quick Start with Docker

**Prerequisites:** Docker Desktop installed and running

1. Clone the repository:

   ```bash
   git clone <your-repo-url>
   cd lawnstarter-exercise
   ```

2. Create your environment file:

   ```bash
   cp .env.example .env
   ```

   The `.env.example` already contains all the Docker-optimized defaults.

3. Run the setup script:

   ```bash
   ./docker-setup.sh
   ```

3. Access the application at <http://localhost:8080>

That's it! The script handles everything: starting containers, installing dependencies, running migrations, and setting up background workers.

## Manual Docker Commands

If you prefer to run commands individually:

```bash
# Create environment file
cp .env.example .env

# Start containers
./vendor/bin/sail up -d

# Install dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

# Setup database
./vendor/bin/sail artisan migrate

# Start background workers
./vendor/bin/sail artisan queue:work &
./vendor/bin/sail artisan schedule:work &
```

## Development

Common commands:

```bash
# Stop containers
./vendor/bin/sail down

# View logs
./vendor/bin/sail logs

# Run tests
./vendor/bin/sail test

# Access container shell
./vendor/bin/sail shell
```

## Architecture

**Backend (Laravel):**

- REST API for Star Wars data
- PostgreSQL database
- Redis for caching and queues
- Background job processing for statistics

**Frontend (React + TypeScript):**

- Search interface
- Character and movie details
- Modern responsive UI

## API Endpoints

- `GET /api/v1/starwars/search/{resource}?query={term}` - Search (resource: people, films, etc.)
- `GET /api/v1/starwars/people/{id}` - Character details
- `GET /api/v1/starwars/films/{id}` - Movie details
- `GET /api/v1/statistics` - Usage statistics

## Features

- Real-time search with the Star Wars API
- Detailed character and movie pages
- Automatic statistics computation every 5 minutes
- Complete request logging and analytics
- Fully containerized with Docker

## Non-Docker Setup

If you prefer running without Docker:

1. Requirements: PHP 8.2+, Node.js 18+, PostgreSQL, Redis
2. Install: `composer install && npm install`
3. Configure `.env` with your database credentials
4. Run: `php artisan migrate && php artisan serve`
5. Frontend: `npm run dev`

## License

MIT License
