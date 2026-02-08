DeltaCR — Web app scaffold
==========================

Quick start
1. Create a MySQL database (example name: deltacr).
2. Import `create_tables.sql` into the database.
3. Edit `config.php` and set `DB_DSN`, `DB_USER`, `DB_PASS`.
4. Ensure the `logs` directory exists and is writable by the webserver: `chmod 755 logs` (or 775/770 as needed).
5. Ensure PHP has PDO MySQL enabled.
6. Point your webserver document root to the project folder or serve with PHP's built-in server for testing:

```bash
php -S 0.0.0.0:8000 -t /path/to/this/repo
```

Files added
- `index.php` — landing page and nav
- `register.php` — create account (first, last, dob, email, security question, security answer, password, confirm, role)
- `login.php` — sign in with email + password
- `forgot.php` — password recovery: identify -> security question -> reset
- `dashboard.php` — role-specific dashboard with a welcome tour
 - `dashboard.php` — role-specific dashboard with a welcome tour
 - `admin_console.php` — admin UI to manage users and view logs
- `db.php` and `config.php` — PDO helper and configuration + advanced error logging
- `assets/css/styles.css` and `assets/js/app.js` — minimal responsive style and tiny JS
- `create_tables.sql` — SQL schema for `users` table

Admin & troubleshooting
- Logs are written to the file defined by `LOG_PATH` in `config.php` (default: `logs/error.log`).
- If errors occur, check that file for stack traces and messages.
- Enable `ENV` => `development` in `config.php` to display debug output while testing.
- For production set `ENV` => `production` to hide internals; logs will still capture issues.

phpMyAdmin / Admin console
- You can use phpMyAdmin (or Adminer) to manage the DB. Import `create_tables.sql` and manage users from the `users` table.

Security notes
- Passwords and security answers are hashed using `password_hash()`.
- All DB queries use prepared statements.

Next steps
- Integrate email verification if required.
- Add rate-limiting and CAPTCHA for registration and forgot flows.
- Add server-side role checks and fine-grained permissions for admin pages.
 - Add server-side role checks and fine-grained permissions for admin pages (already included basic admin gate in `admin_console.php`).
