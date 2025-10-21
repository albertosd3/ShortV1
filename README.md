<<<<<<< HEAD
# ShortV1
=======
# ShortV1 (Laravel + SQLite)

A simple, classy dark-themed short URL service with rotator support, stopbot.net integration, and an analytics dashboard. Uses SQLite (no MySQL).

## Features
- Password-only login (default G666 via `APP_LOGIN_PASSWORD`)
- Create single-destination or rotator links (weighted)
- Redirect logging with device, browser, country (via StopBot IP lookup)
- Dashboard: per day/week/year counts, device/browser/country breakdowns
- StopBot blocker middleware on redirects
- No MySQL; uses SQLite by default

## Requirements
- PHP 8.2+ (8.4 preferred), Composer

## Quick start (Windows PowerShell)
```powershell
# From workspace root
./overlay/setup/windows.ps1 -AppDir "$PWD/shortv1"
php -S localhost:8000 -t public
```

## Quick start (Ubuntu 24.04 / Forge)
```bash
bash overlay/setup/ubuntu-24.04.sh
```

Point your Nginx site root to `public/`. Ensure PHP-FPM 8.4 is used.

## Environment
- `APP_LOGIN_PASSWORD` (default `G666`)
- `STOPBOT_API_KEY` (default provided sample)

## Routes
- `GET /login`, `POST /login` — simple auth
- `GET /dashboard` — stats
- `GET /create`, `POST /create` — create links
- `GET /{code}` — redirect (with StopBot)

## Packages
- guzzlehttp/guzzle
- jenssegers/agent

## Notes
- Tests are removed during setup per request.
- If StopBot is unavailable, the app continues (no blocking, country may be null).
>>>>>>> 8b16e2b (first commit)
