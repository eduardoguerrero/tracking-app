# Aeroflash Package Tracking API

REST API for package registration, tracking, and shipment status management. Built with Laravel and Clean Architecture.

## Quick Start

```bash
composer install
php artisan migrate:fresh --seed
php artisan serve --port=8090
```

## API Documentation (Swagger)

Interactive Swagger UI available at: **`http://localhost:8090/api/documentation`**

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

### Example JWT Token

```
POST /api/v1/auth/login
{
    "email": "admin@aeroflash.com",
    "password": "password"
}
```

Use the returned `access_token` in the `Authorization: Bearer <token>` header.

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
php artisan test
```

## Architecture

Clean Architecture with three layers:

```
app/
├── Domain/              # Business entities, enums, repository interfaces
├── Application/         # Use cases, DTOs, response objects
└── Infrastructure/      # Eloquent models, repository implementations, JWT
```

**Patterns**: Repository, Use Case/Interactor, DTO, State Machine (status transitions), Dependency Injection.

## Database

SQLite (file-based). Migrations create tables: `branches`, `couriers`, `vehicles`, `packages`, `status_histories`.

## Security

### CI/CD Pipeline

Automated security scanning via GitHub Actions (`.github/workflows/security-scan.yml`):

| Tool | Type | Description |
|------|------|-------------|
| **Semgrep** | SAST | Static code analysis; runs on push, PR, and weekly schedule |
| **OWASP Dependency-Check** | SCA | Scans Composer dependencies for known CVEs; fails on CVSS >= 7 |

### OWASP Best Practices

- **JWT authentication** on all package endpoints
- **Input validation** via FormRequests (strict rules, SQL Injection/XSS prevention)
- **Parameterized queries** via Eloquent ORM
- **Standardized error responses** (400, 401, 404, 500)
