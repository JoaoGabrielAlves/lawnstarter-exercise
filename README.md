# LawnStarter Exercise - Star Wars API

A full-stack application using Laravel and React that searches the Star Wars API and tracks usage statistics.

## 📚 Project Documentation

- **[Docker Setup](DOCKER.md)**: For running the app with Docker.
- **[Technical Decisions](TECHNICAL_DECISIONS.md)**: Explains architecture and technology choices.
- **[Submission Overview](SUBMISSION.md)**: A quick summary for reviewers.
- **[Short Questions Answers](SHORT_QUESTIONS_ANSWERS.md)**: Assignment question responses.
- **[Exercise Feedback](FEEDBACK.md)**: Feedback on the exercise.

## 🚀 Quick Start (Docker)

**Prerequisites**: Docker Desktop must be installed and running.

1. Clone the repository and navigate into the project directory.
2. Create the environment file: `cp .env.example .env`
3. Run the setup script: `./docker-setup.sh`
4. Access the application at: <http://localhost:8080>

The setup script handles container creation, dependency installation, database migration, and background worker initialization.

## ⚙️ Manual Docker Commands

For step-by-step control:

```bash
# Start containers in detached mode
./vendor/bin/sail up -d

# Install dependencies and build assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

# Run database migrations
./vendor/bin/sail artisan migrate

# Start background workers (in separate terminals)
./vendor/bin/sail artisan queue:work &
./vendor/bin/sail artisan schedule:work &
```

## 📝 API Endpoints

- `GET /api/v1/starwars/search/{resource}?query={term}`
- `GET /api/v1/starwars/people/{id}`
- `GET /api/v1/starwars/films/{id}`
- `GET /api/v1/statistics`

## ✨ Features

- Real-time search of the official Star Wars API.
- Caching for API responses to improve performance.
- Detailed views for characters and films.
- Asynchronous background job processing for analytics.
- Automated statistics computation every five minutes.
- Fully containerized with a simple, one-command setup.

## 💻 Local Setup (Without Docker)

1. **Requirements**: PHP 8.2+, Node.js 18+, PostgreSQL, Redis.
2. **Install**: `composer install && npm install`.
3. **Configure**: Set up your database credentials in `.env`.
4. **Backend**: `php artisan migrate && php artisan serve`.
5. **Frontend**: `npm run dev`.

## License

MIT License
