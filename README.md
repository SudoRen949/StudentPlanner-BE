# Student AI Study Planner - Backend (Laravel API)

The robust RESTful API gateway driving the Student AI Study Planner. Built with Laravel, this backend serves as a secure proxy to the cloud-hosted Supabase PostgreSQL database and orchestrates high-speed inference communication with the Groq engine.

## Tech Stack

- **Framework:** Laravel 10/11
- **Database:** Supabase (Cloud PostgreSQL)
- **AI Integration:** `openai-php/client` (via custom factory binding)
- **LLM Engine:** Groq Service Layer (`llama-3.3-70b-versatile`)
- **HTTP Client:** Guzzle (configured for secure enterprise/local routing parameters)

## API Endpoints Core Specification

### AI Utilities
- `POST /api/schedule/generate` - Leverages student contexts to map structured JSON arrays into individual `schedules` schema blocks.
- `POST /api/chat/send` - Handshakes array history structures with Groq to maintain message context threads.

### Assignment Management
- `GET /api/assignments/get/{id}` - Extracts uncompleted student tasks sorted by upcoming deadlines.
- `GET /api/assignments/get/{id}` - Extracts finished records (`completed: true`) sorted by completion timestamps.

### User Management
- `POST /api/reset-password` - Validates criteria and updates account passwords.
- `DELETE /api/delete-account/{id}` - Safely cascades and wipes student profiles from the Supabase instance.

## Installation & Setup

1. **Navigate to the backend directory:**
   ```bash
   cd backend
2. **Install necessary libraries:**
   ```bash
   composer install
3. **Run development environment:**
   ```bash
   php artisan serve

## Deploying to Wasmer CLI

1. **Run deploymeny:**
   ```bash
   wasmer deploy
