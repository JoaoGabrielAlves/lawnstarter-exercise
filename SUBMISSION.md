# LawnStarter Exercise - Submission

Docker setup for the Star Wars API exercise using Laravel Sail.

## Requirements Met ✅

✅ **Full Docker containerization** - Everything runs in containers  
✅ **Mac-friendly** - Laravel Sail works great on Mac  
✅ **Simple setup** - One command gets everything running  

## Quick Start

1. Make sure Docker Desktop is running
2. Run: `./docker-setup.sh`
3. Open <http://localhost:8080>

## What's Running

- **Laravel App**: PHP 8.4 + Node.js + Nginx
- **PostgreSQL**: Database with persistent storage  
- **Redis**: Handles caching and background jobs
- **Full Features**: API, frontend, statistics, queue processing

## Testing It Out

```bash
# Test the API
curl "http://localhost:8080/api/v1/starwars/search/people?query=luke"
curl "http://localhost:8080/api/v1/statistics"

# Run the test suite
./vendor/bin/sail test
```

## Why Laravel Sail

- Official Laravel Docker solution
- Zero configuration needed
- Includes everything (PHP, Node.js, databases)
- Optimized for Mac development
- Easy to scale up for production

## Files Added

- `docker-compose.yml` - Container configuration
- `docker-setup.sh` - One-command setup
- `DOCKER.md` - Detailed setup guide
- `.dockerignore` - Build optimization

The app should work immediately after running the setup script on any Mac with Docker Desktop.
