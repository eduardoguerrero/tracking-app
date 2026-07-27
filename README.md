# Aeroflash Package Tracking API

REST API for package registration, tracking, and shipment status management.

## Quick Start (Local)

```bash
cd backend
composer install
php artisan migrate:fresh --seed
php artisan serve --port=8000
```

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
cd backend
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

### Design Patterns

| Pattern | Where | Why |
|---|---|---|
| **Repository** | `PackageRepositoryInterface` → `EloquentPackageRepository` | Decouples domain logic from persistence. Swap to MongoDB/Redis without touching use cases. |
| **DTO** | `RegisterPackageDTO`, `UpdatePackageStatusDTO`, `AuthResponse`, `PackageResponse` | Immutable typed objects that validate at construction. Controllers never touch raw `$request->all()`. |
| **Use Case / Interactor** | `RegisterPackageUseCase`, `GetPackageUseCase`, `UpdatePackageStatusUseCase` | Single-responsibility action classes. Each encapsulates one business operation with its own validation and orchestration. |
| **State Machine** | `PackageStatusEnum` with `allowedTransitions()` | Explicit status flow; invalid transitions rejected at domain level before persistence. |
| **Dependency Injection** | `AppServiceProvider` bindings | All dependencies resolved by container; testable via interface mocking. |

## Database

SQLite (file-based). Migrations create tables: `branches`, `couriers`, `vehicles`, `packages`, `status_histories`.

## Security

### CI/CD Pipeline

Automated security scanning via GitHub Actions (`.github/workflows/security-scan.yml`) runs on every push, PR, and weekly schedule:

| Tool | Type | What it does |
|------|------|---------------|
| **Semgrep** | SAST | Analyzes source code for vulnerabilities, anti-patterns, and insecure coding patterns |
| **OWASP Dependency-Check** | SCA | Scans `composer.lock` for third-party dependencies with known CVEs; fails on CVSS >= 7 |
| **TruffleHog** | Secrets | Scans the full repository for hardcoded credentials, API keys, tokens, and other sensitive data |

**Key differences**:
- **SAST** (Semgrep) → finds flaws in *your* code
- **SCA** (Dependency-Check) → finds known vulnerabilities in *third-party* libraries
- **Secrets** (TruffleHog) → finds accidentally committed *credentials*

### Authentication — JWT

All package endpoints are protected with **JWT (JSON Web Token)** via `App\Infrastructure\Auth\JwtAuthService`. The implementation uses [`firebase/php-jwt`](https://github.com/firebase/php-jwt) (v7), a lightweight, zero-dependency library that provides:

- **No framework lock-in** — pure JWT encode/decode, portable across any PHP project
- **Algorithm flexibility** — HS256 with configurable secret rotation (`JWT_SECRET` in `.env`)
- **Stateless** — no server-side session storage needed; the token carries all claims
- **Minimal overhead** — unlike heavier solutions (Passport, Sanctum), it adds no database tables or middleware chains

### Input Validation — LoginRequest

`app/Http/Requests/LoginRequest.php` applies layered defenses **before** the request reaches the controller:

| Rule | Prevents | OWASP |
|---|---|---|
| `email:rfc,dns` | Invalid/malformed email injection; verifies domain has MX/A records | A03:2021 Injection |
| `max:100` | Buffer overflow / truncation attacks on email field | A04:2021 Insecure Design |
| `min:6` | Weak passwords bypassing authentication | A07:2021 Identification Failures |
| `prepareForValidation: trim, mb_strtolower` | Duplicate accounts (`Admin@Host.com` ≠ `admin@host.com`); whitespace bypass | A07:2021 |
| `failedValidation → 400 JSON` | Information leakage via HTML error pages | A05:2021 Security Misconfiguration |

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

### SAST / DAST / SCA — Audit Tools

Tools recommended for security auditing this codebase:

| Category | Tool | What it scans |
|---|---|---|
| **SAST** | [Semgrep](https://semgrep.dev) (CI integrated) | Source code for SQLi, XSS, hardcoded secrets, insecure patterns |
| **SCA** | [OWASP Dependency-Check](https://owasp.org/www-project-dependency-check/) (CI integrated) | `composer.lock` for dependencies with known CVEs |
| **Secrets** | [TruffleHog](https://github.com/trufflesecurity/trufflehog) (CI integrated) | Hardcoded credentials, API keys, tokens |
| **DAST** | [OWASP ZAP](https://www.zaproxy.org/) | Running API — fuzzing, injection, auth bypass, CORS |
| **DAST** | [Burp Suite](https://portswigger.net/burp) | Manual penetration testing of all endpoints |
| **DAST** | [Nikto](https://github.com/sullo/nikto) | Web server misconfigurations, outdated components |

Audit workflow:
1. **CI pipeline** runs SAST + SCA + Secrets on every push/PR (automated, zero-config)
2. **Local DAST**: start the API, then run `zap-baseline.py -t http://localhost:8000/api/v1` or `nikto -h http://localhost:8000`
3. **OWASP ASVS** Level 1 checklist applied: authentication (V2), input validation (V5), error handling (V7), API security (V13)

### Docker Services

| Service | Container | Port | Description |
|---|---|---|---|
| **app** | `aeroflash-api` | `8080:80` | Laravel API (nginx + PHP-FPM via supervisor) |
| **mysql** | `aeroflash-mysql` | `3307:3306` | MySQL 8.0 with persistent volume |

`docker-compose.yml` uses:
- **Multi-stage Dockerfile**: `composer` stage builds dependencies, `php:8.3-fpm-alpine` runs the app
- **Healthcheck**: MySQL readiness before app starts
- **Auto-migration**: `entrypoint.sh` runs `php artisan migrate --force` on startup
- **Persistent volume**: `mysql_data` survives container restarts
