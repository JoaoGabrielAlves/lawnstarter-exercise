# Technical Decisions

Key technical choices made for this Star Wars API exercise and why I made them.

## Overall Architecture

I went with a unified Laravel + React application rather than separate frontend/backend repos. This keeps things simple for development and deployment while still giving you a proper API that could be consumed by mobile apps later.

The API uses `/api/v1/` versioning from the start - something I learned the hard way is much easier to add upfront than retrofit later.

## Backend Choices

**Laravel with PHP 8.4**: This was an easy choice since it matches LawnStarter's stack. Laravel's built-in queue system and scheduler made implementing the statistics requirement straightforward.

**PostgreSQL**: I chose this over MySQL mainly for better JSON support and performance with complex queries. The statistics computation involves some aggregation that PostgreSQL handles well.

**Redis for everything**: Rather than mixing cache technologies, I used Redis for caching, sessions, and job queues. Keeps the infrastructure simple and Redis is fast enough for all these use cases.

## Frontend Approach

**React + TypeScript**: Aligns with LawnStarter's preferences. TypeScript catches a lot of bugs during development and makes refactoring much safer.

**Tailwind CSS**: I find utility classes faster for prototyping than writing custom CSS. Also makes it easy to keep designs consistent.

**Vite**: Much faster than Webpack for development builds. The hot reload is nearly instant.

## Background Processing

The statistics requirement needed some thought. I implemented it with:

- **Middleware** that logs every API request to a queue job
- **Scheduled job** that runs every 5 minutes to compute statistics
- **Event system** to decouple the statistics computation from the request logging

This keeps API responses fast while still capturing all the data needed for analytics.

## Docker Setup

I used Laravel Sail because it's designed for exactly this use case - getting a Laravel app running quickly with Docker. It includes everything needed (PHP, Node.js, PostgreSQL, Redis) and works well on Mac.

The alternative would have been writing custom Dockerfiles, but Sail handles all the edge cases and version compatibility issues.

## Things I'd Do Differently With More Time

**Caching**: I'd add more aggressive caching of the Star Wars API responses since that data doesn't change often.

**Error Handling**: The error handling is pretty basic. In a real app I'd want more sophisticated retry logic and better user-facing error messages.

**Testing**: I focused on getting the core functionality working rather than comprehensive test coverage. In production I'd want feature tests for all the API endpoints.

**Mobile Optimization**: The UI works on mobile but isn't really optimized for it. Given that LawnStarter users are probably often on mobile, I'd spend more time on the mobile experience.

## Why These Choices Make Sense for LawnStarter

Most of these decisions align with LawnStarter's existing tech stack:

- Laravel backend matches your PHP infrastructure
- React frontend works with your existing React/React Native setup
- PostgreSQL and Redis are already in your stack
- Docker deployment fits with your Kubernetes infrastructure

The background job processing pattern is also something that would be useful for a marketplace platform - you probably need similar async processing for things like notifications, payment processing, and analytics.
