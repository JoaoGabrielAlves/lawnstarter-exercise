# Technical Decisions

This document outlines the key technical choices for the Star Wars API exercise.

## Overall Architecture

A unified Laravel + React application was chosen for simplicity in development and deployment. The API is versioned (`/api/v1/`) from the outset to support future iterations.

## Backend Choices

* **Laravel (PHP 8.4)**: Chosen to align with the company's stack and for its excellent built-in support for queues and scheduled jobs, which were essential for the statistics feature.
* **PostgreSQL**: Selected for its robust performance and strong JSON support, which is beneficial for handling and aggregating the statistics data.
* **Redis**: Used for caching, session storage, and queue management to keep the stack simple and leverage its high performance for multiple use cases.

## Frontend Approach

* **React + TypeScript**: Aligns with modern frontend best practices and provides type safety to reduce bugs and improve maintainability.
* **Vite**: Chosen as the build tool for its fast development server and near-instant hot reloads.

## Background Processing

The statistics feature was implemented using a scalable pattern:

* **Middleware** logs each API request by dispatching a job to a queue.
* A **Scheduled Job** runs every five minutes to process the logs and compute statistics.
* An **Event** is dispatched after computation, decoupling the system.

This approach ensures API responses remain fast by offloading the processing to background workers.

## Docker Setup

**Laravel Sail** was used to provide a consistent, one-command Docker development environment. It includes all necessary services (PHP, Node.js, PostgreSQL, Redis) and is optimized for Laravel development, avoiding the need for custom Dockerfiles.

## Areas for Future Improvement

* **Caching**: Add more aggressive caching for the external Star Wars API responses.
* **Error Handling**: Implement more sophisticated retry logic and user-facing error messages.
* **Testing**: Expand test coverage, especially feature tests for all API endpoints.
* **UI/UX**: Refine the UI for a better mobile experience.
