# SOCOM Daily Time Record

Web-based PHP + MySQL Daily Time Record system using an RFID reader and RFID tags.

## Features
- **Home (client dashboard)** — live RFID tap-in/tap-out with a Recent Attendance view.
- **Masterlist** — public read-only list of registered users.
- **Admin Login** with session-based auth and CSRF-protected forms.
- **Register Admin User** (first admin can self-register when no admins exist yet).
- **Admin Dashboard** — total users, today's attendance, quick links.
- **Register User with RFID Tag** — duplicate tag prevention (DB unique constraint + app check).
- **Registered Users** management — ID, Name, RFID Tag, Created, Edit/Delete actions.
- **Monthly Report** — calendar view of attendance per day, plus CSV/Excel export (Name, Date, Time In, Time Out).

## Requirements
- PHP 8+ with PDO MySQL extension
- MySQL / MariaDB
- A web server (Apache/Nginx) or PHP's built-in server
- A USB/serial RFID reader configured as a HID keyboard-wedge device (types the tag ID + Enter)

## Deploying to Vercel

This project includes `vercel.json` and uses the community PHP runtime. For
Supabase, run `database.supabase.sql` in the Supabase SQL Editor, then add these
environment variables in the Vercel project settings for **Production** (and
**Preview** if needed):

```text
DB_DRIVER=pgsql
DB_HOST=aws-0-YOUR-REGION.pooler.supabase.com
DB_PORT=6543
DB_NAME=postgres
DB_USER=postgres.YOUR_PROJECT_REF
DB_PASS=your-supabase-database-password
```

Redeploy after adding the variables. Do not use `localhost` for `DB_HOST` on
Vercel. Copy the pooler host, user, and port from Supabase under **Connect**
using the **Transaction pooler** option. The PostgreSQL connection uses SSL
automatically.

The application is PHP-based and currently connects to Supabase through PDO;
it does not use `@supabase/server`. That package is for a Node/TypeScript
server handler and should only be added if one is introduced. A safe variable
template is available in `.env.example`; never commit real Supabase secret
keys.

## Setup
1. Create the database and tables:
   ```
   mysql -u root -p < database.sql
   ```
2. Edit [config/db.php](config/db.php) with your database host/user/password if different from defaults.
3. Serve the project root as your web root, for example with PHP's built-in server:
   ```
   php -S localhost:8000
   ```
4. Visit `http://localhost:8000/admin/register_admin.php` to create the first administrator account (only available while no admins exist).
5. Log in at `http://localhost:8000/admin/login.php` and register RFID users under **Register RFID User**.
6. Open `http://localhost:8000/` on the kiosk/client machine connected to the RFID reader — the scan input auto-focuses and auto-submits on each tap.

## RFID Reader Notes
Most USB RFID readers emulate a keyboard and simply "type" the tag's ID followed by Enter. The home page keeps a hidden-focus text input active at all times so a tap immediately fills and submits the form — no extra drivers required.
