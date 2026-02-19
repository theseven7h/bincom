# Bincom Election Results System

A Laravel 11 web application for viewing and managing 2011 Nigerian general election results.

Built as part of the Bincom Junior/Experienced Software Developer preliminary online test.

---

## Features

| Page | Description |
|------|-------------|
| **Q1 – Polling Unit Results** | Cascading dropdowns (State → LGA → Ward → PU) to view results for any polling unit |
| **Q2 – LGA Summed Results** | Sums all polling unit scores within an LGA (does NOT use `announced_lga_results` table) |
| **Q3 – Add New Results** | Form to store party scores for a polling unit with full validation |

---

## Tech Stack

- **Backend:** Laravel 11 (PHP 8.3)
- **Frontend:** Bootstrap 5, vanilla JS (fetch API for AJAX dropdowns)
- **Database:** MySQL (`bincomphptest`)
- **Hosting:** Railway (recommended) or Render via Docker

---

## Local Setup

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 5.7+ or 8.0
- Node.js (optional, only if you add Vite assets)

### Steps

**1. Clone the repo**
```bash
git clone <your-repo-url>
cd bincom-test
```

**2. Install PHP dependencies**
```bash
composer install
```

**3. Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bincomphptest
DB_USERNAME=root
DB_PASSWORD=your_password
```

**4. Create the database and import the SQL dump**
```bash
mysql -u root -p -e "CREATE DATABASE bincomphptest;"
mysql -u root -p bincomphptest < bincom_test.sql
```

**5. Run the app**
```bash
php artisan serve
```

Visit: [http://localhost:8000](http://localhost:8000)

---

## Deployment on Railway (Recommended)

Railway supports Laravel + MySQL natively via Docker. This is the simplest free-tier option.

### Steps

**1. Push your code to GitHub**
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/bincom-test.git
git push -u origin main
```

**2. Create Railway project**
- Go to [railway.app](https://railway.app) and sign in with GitHub
- Click **New Project → Deploy from GitHub repo**
- Select your `bincom-test` repo
- Railway will detect the `Dockerfile` and build automatically

**3. Add a MySQL database**
- In your Railway project dashboard, click **+ New**
- Select **Database → MySQL**
- Railway will automatically inject `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE` as environment variables

**4. Set Laravel environment variables**
In Railway → your app service → Variables tab, add:
```
APP_KEY=          ← Railway entrypoint generates this automatically
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

**5. Import the SQL data**
After the first deploy, open Railway's MySQL service → **Connect** tab → use the provided connection string with a MySQL client to import `bincom_test.sql`:
```bash
mysql -h <host> -P <port> -u <user> -p<password> <database> < bincom_test.sql
```

**6. Done!**
Railway gives you a public URL like `https://bincom-test-production.up.railway.app`

---

## Database Schema Overview

```
states          → state_id, state_name
lga             → uniqueid, lga_id, lga_name, state_id
ward            → uniqueid, ward_id, ward_name, lga_id
polling_unit    → uniqueid, polling_unit_id, ward_id, lga_id, polling_unit_name
party           → id, partyid, partyname
announced_pu_results → result_id, polling_unit_uniqueid, party_abbreviation, party_score
```

Key relationships:
- `polling_unit.lga_id` → `lga.lga_id`
- `polling_unit.ward_id` → `ward.ward_id`
- `lga.state_id` → `states.state_id`
- `announced_pu_results.polling_unit_uniqueid` → `polling_unit.uniqueid`

---

## AJAX Endpoints

| Endpoint | Description |
|----------|-------------|
| `GET /ajax/lgas/{stateId}` | Get LGAs for a state |
| `GET /ajax/wards/{lgaId}` | Get wards for an LGA |
| `GET /ajax/polling-units/{wardId}` | Get polling units for a ward |
| `GET /ajax/pu-results/{pollingUnitId}` | Get results for a polling unit |
| `GET /ajax/lga-results/{lgaId}` | Get summed results for an LGA |
| `GET /ajax/parties` | Get all parties |

---

## Notes

- **Q2 compliance:** The LGA results page deliberately sums `announced_pu_results` grouped by party — it does NOT query the `announced_lga_results` table, as specified in the test instructions.
- **Delta State (ID 25)** is pre-selected as the default state, per the test specification.
- **Q3 save logic:** Saving new results for a polling unit first deletes any existing results for that unit (to avoid duplicates), then inserts fresh rows — one per party.
