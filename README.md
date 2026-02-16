# QhorizonPM

Project management website built with Laravel MVC for Admin and User roles.

## Features
- Admin and User roles with role-based access
- Project and task CRUD
- Dashboard with stats, recent projects, and overdue tasks
- Vietnamese and English UI toggle
- Deployment-ready for shared hosting

## Tech Stack
- Laravel 12 (MVC)
- MySQL
- Blade + Tailwind (custom theme)

## Local Setup
1. Copy environment file:
   - Windows PowerShell:
     - `Copy-Item .env.example .env`
2. Update database connection in `.env`.
3. Install dependencies:
   - `composer install`
4. Generate key:
   - `php artisan key:generate`
5. Run migrations + seed:
   - `php artisan migrate --seed`
6. Build assets:
   - `npm install`
   - `npm run build`
7. Serve locally:
   - `php artisan serve`

## Realtime Messenger (WebSocket)
1. Install Node packages:
   - `npm install`
2. Set realtime variables in `.env`:
   - `REALTIME_SERVER_URL=http://127.0.0.1:8081`
   - `REALTIME_WS_URL=ws://127.0.0.1:8081`
   - `REALTIME_SECRET=change-this-secret`
3. Start realtime server:
   - `npm run realtime`

The messenger keeps periodic polling as fallback, and uses WebSocket updates when `REALTIME_WS_URL` is configured.

## Demo Accounts
- Admin: `admin@example.com` / `Admin@123`
- User: `user@example.com` / `User@123`

## Shared Hosting Deployment
1. Upload all files to hosting.
2. Point document root to the `public` folder.
3. Create `.env` from `.env.example` and set production values:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL` to your domain
   - `DB_*` for MySQL
4. Run migrations on server:
   - `php artisan migrate --force`
5. Build assets locally and upload `public/build` if your hosting has no Node.
6. Ensure `storage` and `bootstrap/cache` are writable.

## Notes
- Change demo passwords in production.
- Configure mail and queue as needed in `.env`.

## Email Delivery (OTP / Verification)
If you do not receive emails, your app is likely using `MAIL_MAILER=log` (log-only mode).

Use real SMTP (example: Gmail):
1. Enable 2-Step Verification on your Gmail account.
2. Create an App Password in Google Account security.
3. Set these env values:
   - `MAIL_MAILER=smtp`
   - `MAIL_SCHEME=` (leave empty for port 587)
   - `MAIL_HOST=smtp.gmail.com`
   - `MAIL_PORT=587`
   - `MAIL_USERNAME=your_email@gmail.com`
   - `MAIL_PASSWORD=your_16_char_app_password`
   - `MAIL_FROM_ADDRESS=your_email@gmail.com`

Alternative: use implicit TLS with port 465:
- `MAIL_SCHEME=smtps`
- `MAIL_PORT=465`
4. Clear config cache:
   - `php artisan config:clear`

Quick test via Tinker:
- `php artisan tinker`
- `Mail::raw('SMTP test', fn($m) => $m->to('your_receiver@gmail.com')->subject('Test Mail'));`

Registration flow note:
- User account is now created only after OTP verification succeeds.
- Before OTP verification, data is stored in `pending_registrations`.
- Make sure to run migrations after pull/deploy:
   - `php artisan migrate --force`

Transient data cleanup:
- Command: `php artisan app:cleanup-transient-data`
- Scheduled hourly via `routes/console.php`.
- If your host does not run scheduler automatically, run the command periodically as a cron/job.

## Free Deployment (Render + PostgreSQL)
1. Push this repo to Git.
2. Create a new Render Blueprint service and select this repo.
3. Render will read `render.yaml` and create the web service + free database.
4. Set `APP_KEY` in Render environment:
   - Run locally: `php artisan key:generate --show`
   - Copy the output into `APP_KEY`.
5. After deploy, run database migrations once:
   - `php artisan migrate --force`

### No-Shell Option (Auto-migrate on Deploy)
If your plan does not allow Shell, the service can auto-run migrations on every deploy.

- Ensure these env vars are set (already in `render.yaml`):
   - `RUN_MIGRATIONS=true`
   - `RUN_SEED=false`
- If you want demo data on first deploy, set `RUN_SEED=true`, deploy once, then set it back to `false`.

Notes:
- Update `APP_URL` in Render env to your actual domain.
- Change demo passwords in production.
