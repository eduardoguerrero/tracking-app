# Aeroflash Package Tracking API

REST API for package registration, tracking, and shipment status management.

## Quick Start Docker

Single-command startup with MySQL:

```bash
docker compose up -d --build
```

- **API**: `http://localhost:8080`
- **Swagger UI**: `http://localhost:8080/api/documentation`
- **MySQL**: `localhost:3307` (credentials below)

Migrations and seed data run automatically on first startup. To rebuild after code changes:

```bash
docker compose down && docker compose up -d --build
```

## Frontend (React)

Web interface for tracking packages. Located in `/frontend`.

```bash
cd frontend
npm install
npm run dev
```

- **App**: `http://localhost:5173`
- **Login**: `admin@aeroflash.com` / `password`
- Points to API at `http://localhost:8080/api/v1` (configurable in `.env`)

Tech stack: **React 18 + Vite + TanStack Query + Tailwind CSS**.

Features:
- State management via **TanStack Query** (caching, no unnecessary re-renders)
- **Loading skeleton**, **empty state**, and **error handling** for all states
- **Responsive** design with Tailwind CSS
#### MySQL Access

```bash
docker compose exec mysql mysql -u aeroflash -paeroflash_secret aeroflash
```

| Field | Value |
|---|---|
| Host | `localhost:3307` |
| Database | `aeroflash` |
| Username | `aeroflash` |
| Password | `aeroflash_secret` |

## API Documentation (Swagger)

Interactive Swagger UI available at: **`http://localhost:8000/api/documentation`**

### Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/v1/auth/login` | No | Authenticate and obtain JWT token |
| `POST` | `/api/v1/packages` | Bearer JWT | Register a new package |
| `GET` | `/api/v1/packages/{tracking_number}` | Bearer JWT | Get package details and tracking history |
| `PATCH` | `/api/v1/packages/{tracking_number}/status` | Bearer JWT | Update package delivery status |

### Status Flow

```
Registered → In Transit → Out for Delivery → Delivered
     ↓            ↓              ↓
  Cancelled   Cancelled      Cancelled
```

- Moving to **In Transit** requires an active `courier_id` and `vehicle_id`
- Invalid transitions return `400`

## Test Credentials

| Field | Value |
|-------|-------|
| Email | `admin@aeroflash.com` |
| Password | `password` |

## Test Data

| Tracking Number | Status |
|-----------------|--------|
| `AF-TEST-001` | Registered |
| `AF-TEST-002` | In Transit |
| `AF-TEST-003` | Out for Delivery |
| `AF-TEST-004` | Delivered |
| `AF-TEST-005` | Cancelled |

## Run Tests

```bash
cd backend
php artisan test
```

### Design Patterns and best practices

Clean Architecture with three layers:

```
app/
├── Domain/              # Business entities, enums, repository interfaces
├── Application/         # Use cases, DTOs, response objects
└── Infrastructure/      # Eloquent models, repository implementations, JWT
```
Repository
DTO
Dependency injection

## Security

### CI/CD Pipeline

Automated security scanning via GitHub Actions (`.github/workflows/security-scan.yml`) runs on every push, PR, and weekly schedule:

| Tool | Type | What it does |
|------|------|---------------|
| **Semgrep** | SAST | Analyzes source code for vulnerabilities, anti-patterns, and insecure coding patterns |
| **OWASP Dependency-Check** | SCA | Scans `composer.lock` for third-party dependencies with known CVEs; fails on CVSS >= 7 |
| **TruffleHog** | Secrets | Scans the full repository for hardcoded credentials, API keys, tokens, and other sensitive data |

### Authentication — JWT

All package endpoints are protected with **JWT (JSON Web Token)** via `App\Infrastructure\Auth\JwtAuthService`. The implementation uses [`firebase/php-jwt`](https://github.com/firebase/php-jwt) (v7), a lightweight, zero-dependency library that provides:

### Input Validation — LoginRequest

`app/Http/Requests/LoginRequest.php` applies layered defenses **before** the request reaches the controller:

### OWASP Best Practices

- **JWT authentication** on all package endpoints (`firebase/php-jwt`, stateless, configurable via `JWT_SECRET`)
- **Rate limiting**: 5 login attempts/minute per IP + email (brute-force protection)
- **Input validation** via FormRequests with sanitization (`trim`, `mb_strtolower`, email DNS check)
- **XSS prevention**: `XssSanitizer` strips HTML tags from inputs; `SecurityHeaders` middleware sets `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy`
- **Parameterized queries** via Eloquent ORM (SQL Injection prevention)
- **bcrypt hashing** via `Hash::check()` with BCRYPT_ROUNDS=12 (constant-time comparison)
- **No user enumeration**: generic "Invalid credentials" message hides whether email or password is wrong
- **Short JWT TTL**: 1-hour token expiry with refresh window of 2 weeks
- **Standardized error responses** (400, 401, 404, 500)
- **TruffleHog** scans every commit for leaked secrets before they reach production

### Docker Services

| Service | Container | Port | Description |
|---|---|---|---|
| **app** | `aeroflash-api` | `8080:80` | Laravel API (nginx + PHP-FPM via supervisor) |
| **mysql** | `aeroflash-mysql` | `3307:3306` | MySQL 8.0 with persistent volume |
