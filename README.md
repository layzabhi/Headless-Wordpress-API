# Headless WordPress × Next.js

A production-ready portfolio system built on a **WordPress headless CMS** backend and a **Next.js 14** frontend. WordPress handles all content management — posts, projects, media — while Next.js takes care of everything the user actually sees.

> Built as a portfolio showcase, but structured cleanly enough to fork and make your own.

---

## Why This Stack?

WordPress powers ~43% of the web for a reason — its admin UI is hard to beat for non-technical content editors. But its frontend templating (PHP + Blade) hasn't aged particularly well. This project keeps what's great about WordPress (the CMS experience, media library, plugin ecosystem) and replaces what isn't (the frontend) with a modern React-based stack.

The result: editors get the WordPress dashboard they already know, and visitors get a fast, modern site.

---

## What's Inside

```
headless-wp-nextjs/
│
├── wordpress-plugin/               # The WordPress side
│   ├── headless-wp-api.php         # Plugin entry point
│   └── includes/
│       ├── class-custom-post-types.php     # Projects, Services, Team Members
│       ├── class-custom-taxonomies.php     # Project Category, Tech Stack
│       ├── class-rest-api-endpoints.php    # Custom /wp-json routes
│       ├── class-jwt-auth.php              # Token-based auth for protected routes
│       └── class-acf-setup.php             # ACF field groups & configuration
│
└── nextjs-frontend/                # The Next.js side
    └── src/
        ├── app/
        │   ├── layout.tsx                  # Root layout with fonts, metadata
        │   ├── page.tsx                    # Homepage
        │   └── projects/
        │       ├── page.tsx                # Projects listing
        │       └── [slug]/page.tsx         # Individual project page
        ├── components/
        │   ├── Header.tsx
        │   ├── Footer.tsx
        │   └── ProjectCard.tsx
        └── lib/
            ├── types.ts                    # All TypeScript interfaces
            └── hooks.ts                    # Custom React hooks (usePosts, etc.)
```

---

## Features

**WordPress Plugin**
- Registers `project`, `service`, and `team_member` custom post types — all REST API–enabled
- Custom taxonomies: `project_category` and `tech_stack` for filtering
- Custom REST endpoints under `/wp-json/hwa/v1/` for richer, combined data responses
- JWT authentication for any protected write operations
- ACF field groups wired up programmatically (no manual setup needed after install)
- CORS headers configured so the Next.js dev server can talk to WordPress out of the box

**Next.js Frontend**
- App Router with React Server Components — pages load fast without unnecessary client JS
- Fully typed API responses using TypeScript interfaces
- `usePosts` and `usePost` hooks for clean data fetching in client components
- Static generation with `generateStaticParams` for project detail pages
- Tailwind CSS for styling — utility-first, easy to customise
- SEO metadata generated per page from WordPress content

---

## Getting Started

You'll need two things running locally: a WordPress site (with this plugin installed) and the Next.js dev server.

### Prerequisites

- PHP 8.1+
- WordPress 6.0+
- [Advanced Custom Fields (ACF)](https://www.advancedcustomfields.com/) plugin — free version works fine
- Node.js 18+
- A local WordPress environment ([LocalWP](https://localwp.com/) is the easiest option)

---

### Step 1 — Set Up WordPress

**1a. Install WordPress locally**

If you haven't already, download [LocalWP](https://localwp.com/) and create a new site. You'll end up with something like `http://headless-wp.local` as your WordPress URL — keep note of it.

**1b. Install the plugin**

Copy the `wordpress-plugin/` folder into your WordPress plugins directory:

```bash
# If using LocalWP on macOS, the path looks something like:
cp -r wordpress-plugin ~/Library/Application\ Support/Local/sites/your-site/app/public/wp-content/plugins/headless-wp-api
```

Then go to **WordPress Admin → Plugins** and activate **Headless WP API**.

**1c. Install ACF**

Go to **Plugins → Add New**, search for "Advanced Custom Fields", install and activate it. The plugin will register all field groups automatically on activation.

**1d. Generate a JWT Secret**

In `wp-config.php`, add this line (use any long random string):

```php
define('JWT_AUTH_SECRET_KEY', 'your-super-secret-key-change-this');
```

**1e. Enable pretty permalinks**

Go to **Settings → Permalinks** and choose "Post name". This is required for the REST API to work correctly.

**1f. Verify the API is working**

Open your browser and hit:

```
http://headless-wp.local/wp-json/wp/v2/projects
```

You should see a JSON response (empty array `[]` is fine — it means no projects exist yet, but the endpoint works).

---

### Step 2 — Set Up Next.js

**2a. Install dependencies**

```bash
cd nextjs-frontend
npm install
```

**2b. Configure environment variables**

Create a `.env.local` file in the `nextjs-frontend/` directory:

```env
# The URL of your local WordPress site
NEXT_PUBLIC_WP_API_URL=http://headless-wp.local/wp-json

# Only needed if you're using authenticated/write endpoints
WP_API_USERNAME=your-wp-username
WP_API_PASSWORD=your-wp-application-password
```

> **Application passwords** are different from your login password. Go to **WordPress Admin → Users → Your Profile → Application Passwords** to generate one.

**2c. Start the dev server**

```bash
npm run dev
```

Visit `http://localhost:3000` — you should see the homepage pulling in data from WordPress.

---

### Step 3 — Add Some Content

Back in your WordPress admin:

1. Go to **Projects → Add New**
2. Fill in a title, body content, and featured image
3. Assign a **Project Category** (e.g. "Web Design") and **Tech Stack** tags (e.g. "React", "TypeScript")
4. Fill in the ACF fields: project URL, GitHub link, year, client name
5. Publish it

Refresh your Next.js frontend — the project should appear on the `/projects` page.

---

## API Reference

The plugin exposes WordPress's default REST API plus a set of custom endpoints for richer responses.

### Default WP Endpoints (used by frontend)

| Endpoint | Description |
|---|---|
| `GET /wp-json/wp/v2/projects` | All projects, paginated |
| `GET /wp-json/wp/v2/projects?slug={slug}` | Single project by slug |
| `GET /wp-json/wp/v2/projects?project_category={id}` | Filter by category |
| `GET /wp-json/wp/v2/projects?tech_stack={id}` | Filter by tech stack |
| `GET /wp-json/wp/v2/project_category` | All project categories |
| `GET /wp-json/wp/v2/tech_stack` | All tech stack tags |

### Custom Endpoints (`/wp-json/hwa/v1/`)

| Endpoint | Description |
|---|---|
| `GET /hwa/v1/projects` | Projects with ACF fields pre-embedded |
| `GET /hwa/v1/projects/{slug}` | Single project with all metadata |
| `GET /hwa/v1/featured` | Homepage featured projects (up to 3) |
| `POST /hwa/v1/auth/token` | Get JWT token (username + password) |

### Authentication

Protected endpoints require a Bearer token in the `Authorization` header:

```bash
# 1. Get a token
curl -X POST http://headless-wp.local/wp-json/hwa/v1/auth/token \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "your-password"}'

# 2. Use the token
curl http://headless-wp.local/wp-json/hwa/v1/protected-endpoint \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1Qi..."
```

---

## Deployment

### WordPress (Backend)

Deploy WordPress to any standard PHP host — [Kinsta](https://kinsta.com/), [WP Engine](https://wpengine.com/), [Cloudways](https://www.cloudways.com/), or a plain VPS all work fine.

After deploying, update the `allowed_origins` array in `headless-wp-api.php` with your production frontend URL:

```php
private array $allowed_origins = [
    'https://yourfrontenddomain.com',
];
```

### Next.js (Frontend)

The cleanest option is [Vercel](https://vercel.com/) — it's built for Next.js and has a generous free tier.

```bash
# Install Vercel CLI
npm i -g vercel

# Deploy from the nextjs-frontend directory
cd nextjs-frontend
vercel
```

You'll be prompted to connect your GitHub repo and set environment variables. Set `NEXT_PUBLIC_WP_API_URL` to your live WordPress URL.

For static site generation (faster, cheaper):

```bash
npm run build   # Generates the static site
```

---

## Environment Variables Reference

| Variable | Required | Description |
|---|---|---|
| `NEXT_PUBLIC_WP_API_URL` | ✅ Yes | Base URL of the WordPress REST API |
| `WP_API_USERNAME` | Only for auth | WordPress username for protected routes |
| `WP_API_PASSWORD` | Only for auth | WordPress application password |

> Never commit `.env.local` to git. It's already in `.gitignore`.

---

## Common Issues

**"No posts showing on frontend"**  
Check that pretty permalinks are enabled in WordPress (Settings → Permalinks → save once). Also verify `NEXT_PUBLIC_WP_API_URL` doesn't have a trailing slash.

**CORS errors in the browser console**  
Make sure your `localhost:3000` is in the `$allowed_origins` array in `headless-wp-api.php` and that the plugin is active.

**ACF fields not appearing in API response**  
The `show_in_rest` option needs to be `true` on each field group. The `class-acf-setup.php` file handles this, but double-check that ACF is installed before activating this plugin.

**JWT token invalid / expired**  
Tokens expire after 7 days by default. Re-authenticate to get a fresh one. If it's invalid immediately, check that `JWT_AUTH_SECRET_KEY` is set in `wp-config.php`.

---

## Tech Stack

| Layer | Technology |
|---|---|
| CMS | WordPress 6.x |
| Custom Fields | Advanced Custom Fields (ACF) |
| Auth | JWT (JSON Web Tokens) |
| Frontend | Next.js 14 (App Router) |
| Language | TypeScript |
| Styling | Tailwind CSS |
| Deployment | Vercel (frontend) + any PHP host (WP) |

---

## Contributing

This is a personal portfolio project, but if you spot a bug or have a suggestion — issues and PRs are welcome.

1. Fork the repo
2. Create a feature branch: `git checkout -b fix/cors-headers`
3. Commit your changes: `git commit -m 'fix: tighten cors origin check'`
4. Push and open a PR

Please follow [Conventional Commits](https://www.conventionalcommits.org/) for commit messages. It keeps the history readable.

---

## License

MIT — do whatever you want with it. A link back or a star on the repo is always appreciated but never required.

---

*Built with the idea that a good CMS and a good frontend shouldn't have to be the same thing.*