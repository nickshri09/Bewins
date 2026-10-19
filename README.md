# Bewins Services

Bewins is a Laravel web application for managing customers, financial products, service orders, and internal operations for Bewin Services Private Limited. It includes an administrative dashboard and a client API used by the mobile application.

## Technology

- PHP 8.1 or newer
- Laravel 10
- MySQL or another Laravel-supported database
- Composer
- Node.js and npm (for frontend asset development/builds)
- Laravel Sanctum (API authentication)

## Main modules

- Client and office-user management, branches, roles, and permissions
- BCS investments, loans, fixed deposits, repayments, and expected returns
- Wallets, deposits, withdrawals, and transaction records
- Client referral network and commission history, including up to three customer referral levels
- BCS closure certificates and email status tracking
- Investment and loan agreement generation, storage, and email delivery
- Service catalogue, mobile service orders, and Razorpay payment flows
- Activity history, reports, knowledge base, and dashboard administration

The working behavior and available screens are defined by the routes, controllers, migrations, and views in this repository. Payment, mail, and other external integrations require valid environment credentials.

## Local setup

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Create a local `.env` using your team's approved environment template. This repository intentionally does not include production credentials. Configure at least `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`, database connection values, mail settings, and any payment-provider keys you need locally.

3. Generate an application key if one is not already set:

   ```bash
   php artisan key:generate
   ```

4. Create the local database, then run migrations:

   ```bash
   php artisan migrate
   ```

   Only seed a development database when you have reviewed the seeders and intend to add their sample/default records:

   ```bash
   php artisan db:seed
   ```

5. Link Laravel's public storage directory when the application needs it:

   ```bash
   php artisan storage:link
   ```

6. Install frontend packages and build assets:

   ```bash
   npm install
   npm run build
   ```

   For frontend development, run `npm run dev` in a separate terminal.

7. Start the local web server:

   ```bash
   php artisan serve
   ```

Open the URL printed by Artisan (usually `http://127.0.0.1:8000`). Sign-in accounts are environment-specific; obtain local credentials from the project administrator rather than using production credentials in development.

## Mobile API

API routes are declared in [`routes/api.php`](routes/api.php) and are served under `/api`. The current route set includes:

| Area | Routes |
| --- | --- |
| Authentication | `POST /api/client/register`, `POST /api/client/login`, forgot-password, OTP verification, and reset-password routes |
| Profile and branches | Client profile read/update and branch listing |
| Investments and deposits | Investment create/list/detail and fixed-deposit routes |
| Loans | Loan create/list/detail and loan-product listing |
| Referrals | `GET /api/client/referrals/summary` |
| Wallet and withdrawals | Wallet summary, transactions, investment history, add-money/payment verification, investment list, and withdrawal requests |
| Service orders | Categories, services, service details, Razorpay/wallet orders, payment verification, and customer order history |

Several routes are grouped behind Sanctum authentication. Check the route middleware and controller authorization before exposing an endpoint to a mobile client; do not assume every route in the file has identical authentication requirements. Use HTTPS and keep API tokens and provider credentials out of source control.

To inspect the full registered route list locally:

```bash
php artisan route:list --path=api
```

## Agreements and email

Agreement PDF templates are maintained as Blade views, while uploaded/generated agreement files are associated with the relevant investment or loan. The agreement email service uses Laravel's configured mail transport. Update the templates in one place when approved agreement wording changes, and configure/test mail settings in a non-production environment before enabling delivery.

## Configuration and operational notes

- Never commit `.env`, API keys, payment secrets, customer identity documents, database dumps, or production uploads.
- Configure a dedicated development database and mail transport before testing workflows that write records or send messages.
- Scheduled behavior is configured through Laravel's console kernel; configure the server scheduler if the deployment depends on scheduled tasks.
- Apply new migrations during deployment using the team's backup and release process.


## Useful commands

```bash
php artisan route:list
php artisan migrate:status
php artisan cache:clear
php artisan config:clear
```

## License

This is a private project. Use and distribution are subject to the organization’s ownership and licensing terms.
