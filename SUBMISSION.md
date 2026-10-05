# Product CRUD Submission Setup

## Current Services

- LavaLust API: `https://espiritu-angelajane.onrender.com`
- API login: `POST /api/login`
- API registration: `POST /api/register`
- Authenticated products: `GET`, `POST /api/products`; `PUT` and `DELETE /api/products/{id}`

The deployed API was checked and returns JSON `401` for invalid login and unauthenticated product access. The public React URL and GitHub repository URLs still need to be supplied after deployment/publishing.

## Aiven Database

Configure these variables in the backend `.env` locally and as Render environment variables. Never commit `.env`:

- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `DB_SSL_CA` set to the mounted Aiven CA certificate path when TLS verification is required
- `JWT_SECRET` and `REFRESH_TOKEN_KEY`, each generated independently with `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`
- `APP_URL` set to the backend URL
- `FRONTEND_ORIGINS` set to the exact frontend origin; multiple origins are comma-separated

Create the required table in the Aiven SQL console (the equivalent LavaLust migration is `app/migrations/003_create_products_table.php`):

```sql
CREATE TABLE IF NOT EXISTS products (
  id INT NOT NULL AUTO_INCREMENT,
  product_name VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  quantity INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Render Deployment

### API Web Service

- Runtime: Docker
- Root directory: repository root
- Dockerfile: `Dockerfile`
- Set the Aiven, JWT, `APP_URL`, and `FRONTEND_ORIGINS` variables above in Render. Use a Render Secret File for the Aiven CA certificate if required, and point `DB_SSL_CA` to that file.

### React Static Site

- Root directory: `product-frontend`
- Build command: `npm ci && npm run build`
- Publish directory: `dist`
- Environment variable: `VITE_API_URL=https://espiritu-angelajane.onrender.com/api`
- Add the SPA rewrite `/*` to `/index.html` with status `200`.
- After deployment, set `FRONTEND_ORIGINS` on the API service to the exact Render static-site origin and redeploy the API.

## Local Run

Run the API at `http://localhost:8000`, fill the missing local JWT keys in the ignored root `.env`, then run the frontend from `product-frontend` with `npm run dev`. Vite proxies `/api` to the local API.

## Remaining Submission Items

The frontend Render URL, separate GitHub repository URLs, and requested screenshots must be added after the corresponding external services and accounts are available. Do not publish database or token secrets in either repository.
