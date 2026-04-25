# Aperlex Frontend (Next.js)

This is the real Next.js frontend for the existing Node API backend.

## Run

1. Copy env:

```bash
cp .env.example .env.local
```

2. Install:

```bash
npm install
```

3. Start dev server:

```bash
npm run dev
```

Frontend runs at http://localhost:3001

## Required backend

Run your Node API backend at http://localhost:3000 (default in env).

## Implemented pages

- /login
- /dashboard
- /projects
- /tasks
- /messenger

## Notes

- Auth token is stored in localStorage as `auth_token`.
- API base URL is controlled by `NEXT_PUBLIC_API_URL`.
- Socket URL is controlled by `NEXT_PUBLIC_SOCKET_URL`.
