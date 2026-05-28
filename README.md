# Lantera Extension Framework

A Laravel package that provides a multi-platform app framework for Shopify and BigCommerce. It handles OAuth install/uninstall flows, session verification, webhooks, and integration with the Lantera AppsManager subscription platform.

## Requirements

- PHP ^8.3
- Laravel 12.x or 13.x

## Installation

Since this package is hosted on a private GitHub repository, add it as a VCS source in your project's `composer.json` before requiring it:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:mukovoz/laravel-commerce-kit.git",
            "no-api": true
        }
    ]
}
```

The `no-api` flag tells Composer to use raw git operations instead of the GitHub REST API, so your SSH key is sufficient — no personal access token required.

Then install the package:

```bash
composer require lantera/extension-framework:^1.0
```

**SSH key** — make sure your SSH key is added to your GitHub account and that `ssh -T git@github.com` succeeds before running Composer.

**CI/CD** — on machines without an SSH agent, pass a deploy key or use HTTPS with a token:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mukovoz/laravel-commerce-kit.git"
        }
    ]
}
```

```bash
COMPOSER_AUTH='{"github-oauth":{"github.com":"YOUR_GITHUB_TOKEN"}}'
```

The package auto-discovers its service provider. To publish the config:

```bash
php artisan vendor:publish --tag=extension-framework-config
```

## App Registration URLs

When registering your app in each platform's developer portal, use the URLs below. Replace `https://your-app.com` with your application's base URL.

### Shopify

| Field | URL |
|---|---|
| **App URL** | `https://your-app.com/shopify/load` |
| **Allowed redirection URL(s)** | `https://your-app.com/shopify/callback` |
| **Uninstall webhook URL** | `https://your-app.com/shopify/uninstall` |

**OAuth flow summary:**

1. Install initiated at `GET /shopify/install?shop={shop}` — redirects to Shopify authorization page.
2. Shopify redirects back to `GET /shopify/callback` with `code`, `state`, `hmac`, and `timestamp`.
3. The callback controller exchanges the code for a permanent access token and creates the site record.
4. Subsequent loads hit `GET /shopify/load` with a Shopify session token; the middleware verifies the JWT signature.

### BigCommerce

| Field | URL |
|---|---|
| **Auth Callback URL** | `https://your-app.com/bigcommerce/install` |
| **Load Callback URL** | `https://your-app.com/bigcommerce/load` |
| **Uninstall Callback URL** | `https://your-app.com/bigcommerce/uninstall` |

> The `BIGCOMMERCE_CALLBACK_URL` env variable must match the **Auth Callback URL** configured in the BigCommerce developer portal exactly.

**OAuth flow summary:**

1. BigCommerce sends `GET /bigcommerce/install?code=...&scope=...&context=stores/{hash}`.
2. The controller exchanges the code at `https://login.bigcommerce.com/oauth2/token` and stores the access token.
3. The merchant's control panel loads the app via `GET /bigcommerce/load` with a signed JWT; the middleware verifies it using `BIGCOMMERCE_CLIENT_SECRET`.

## Environment Variables

Add the following to your application's `.env` file:

```env
# ─── Shopify ────────────────────────────────────────────────
# Found in Partners Dashboard → App → API credentials
SHOPIFY_API_KEY=
SHOPIFY_API_SECRET=
SHOPIFY_WEBHOOK_SECRET=
# Space or comma-separated list of OAuth scopes your app requires
SHOPIFY_SCOPES=read_products,write_products
# Shopify API version to use (default: 2025-01)
SHOPIFY_API_VERSION=2025-01

# ─── BigCommerce ────────────────────────────────────────────
# Found in Developer Portal → My Apps → Edit App → Technical
BIGCOMMERCE_CLIENT_ID=
BIGCOMMERCE_CLIENT_SECRET=
# Must exactly match the Auth Callback URL registered in the portal
BIGCOMMERCE_CALLBACK_URL=https://your-app.com/bigcommerce/install
BIGCOMMERCE_SCOPES=store_v2_products
BIGCOMMERCE_API_VERSION=v2
# Numeric app ID shown in the Developer Portal URL: /manage/app/{id}
BIGCOMMERCE_APP_ID=

# ─── Lantera AppsManager ────────────────────────────────────
# Your app's ID and secret from the AppsManager dashboard
APPS_MANAGER_APPLICATION_ID=
APPS_MANAGER_APPLICATION_SECRET_KEY=
# Override only if using a self-hosted AppsManager instance
APPS_MANAGER_BASE_URL=https://api.appsmanager.com/
APPS_MANAGER_SCRIPT_URL=https://api.appsmanager.com/script
```

### Variable reference

| Variable | Required | Description |
|---|---|---|
| `SHOPIFY_API_KEY` | Yes | Shopify OAuth client ID (used as `client_id` in the authorization URL) |
| `SHOPIFY_API_SECRET` | Yes | Shopify OAuth client secret; also used to verify HMAC signatures on OAuth callbacks |
| `SHOPIFY_WEBHOOK_SECRET` | Yes | Secret used to verify `X-Shopify-Hmac-Sha256` on incoming webhooks |
| `SHOPIFY_SCOPES` | Yes | Comma-separated list of Shopify OAuth scopes |
| `SHOPIFY_API_VERSION` | No | Shopify API version, defaults to `2025-01` |
| `BIGCOMMERCE_CLIENT_ID` | Yes | BigCommerce OAuth client ID |
| `BIGCOMMERCE_CLIENT_SECRET` | Yes | BigCommerce OAuth client secret; also used to verify signed JWTs on load/uninstall requests |
| `BIGCOMMERCE_CALLBACK_URL` | Yes | Full URL of `GET /bigcommerce/install` — must match the portal exactly |
| `BIGCOMMERCE_SCOPES` | Yes | BigCommerce OAuth scopes to request |
| `BIGCOMMERCE_API_VERSION` | No | BigCommerce API version, defaults to `v2` |
| `BIGCOMMERCE_APP_ID` | Yes | Numeric app ID from the BigCommerce Developer Portal — used to construct the control panel URL |
| `APPS_MANAGER_APPLICATION_ID` | Yes | Your app's ID in the Lantera AppsManager platform |
| `APPS_MANAGER_APPLICATION_SECRET_KEY` | Yes | Used to verify the `X-Application-Secret` header on incoming AppsManager webhooks |
| `APPS_MANAGER_BASE_URL` | No | AppsManager API base URL, defaults to `https://api.appsmanager.com/` |
| `APPS_MANAGER_SCRIPT_URL` | No | URL of the subscription script injected on the BigCommerce load page |

## Routes

All routes are registered automatically by the service provider under the `web` middleware group.

### Shopify

| Method | URI | Description |
|---|---|---|
| `GET` | `/shopify/install` | Initiates OAuth — redirects merchant to Shopify authorization page |
| `GET` | `/shopify/callback` | OAuth callback — exchanges code for access token |
| `GET` | `/shopify/load` | Loads the app inside the Shopify Admin (requires valid session token) |
| `POST` | `/shopify/uninstall` | Receives the app/uninstalled webhook from Shopify |
| `POST` | `/shopify/webhook/apps-manager` | Receives subscription webhooks from Lantera AppsManager |
| `GET` | `/shopify/emulate/{site}` | Dev-only: emulates a merchant session without OAuth (disabled in production) |

### BigCommerce

| Method | URI | Description |
|---|---|---|
| `GET` | `/bigcommerce/install` | OAuth install callback — exchanges code for access token |
| `GET` | `/bigcommerce/load` | Loads the app inside the BigCommerce control panel (requires signed JWT) |
| `GET` | `/bigcommerce/uninstall` | Signed uninstall callback from BigCommerce |
| `POST` | `/bigcommerce/webhook/apps-manager` | Receives subscription webhooks from Lantera AppsManager |
| `GET` | `/bigcommerce/emulate/{site}` | Dev-only: emulates a merchant session without OAuth (disabled in production) |

## Artisan Commands

The package registers two commands for local development and testing. Both are disabled in production and will exit with an error if `APP_ENV=production`.

### `extension:install`

Emulates a full app installation without going through the real OAuth flow. Registers the store with Lantera AppsManager and returns a browser URL you can open to test the app immediately.

```bash
php artisan extension:install
```

Interactive prompts:

1. **Which platform?** — choose `Shopify` or `BigCommerce`
2. **Store URL** — e.g. `myshop.myshopify.com` or `store-abc123.mybigcommerce.com`

The command creates a `Site` record with a generated access token, triggering the `SiteObserver` which calls the AppsManager `/install` endpoint automatically. If a site with the same `store_hash` already exists it will be reused without re-registering.

Output:

```
Emulate URL ............... https://your-app.com/shopify/emulate/1
```

### `extension:sites`

Lists every registered site with its platform, status, and ready-to-open emulate URL.

```bash
php artisan extension:sites
```

Example output:

```
+----+-------------+--------+------------------------------------------+-----------+--------------------------------------------+
| ID | Platform    | Name   | URL                                      | Status    | Emulate URL                                |
+----+-------------+--------+------------------------------------------+-----------+--------------------------------------------+
|  1 | Shopify     | Myshop | https://myshop.myshopify.com             | Installed | https://your-app.com/shopify/emulate/1     |
|  2 | BigCommerce | Store  | https://store-abc123.mybigcommerce.com   | Installed | https://your-app.com/bigcommerce/emulate/2 |
+----+-------------+--------+------------------------------------------+-----------+--------------------------------------------+
```

## Events

The package fires the following events that your application can listen to:

| Event | Platform | Fired when |
|---|---|---|
| `Lantera\ExtensionFramework\Events\Shopify\AppInstalled` | Shopify | OAuth callback completes successfully |
| `Lantera\ExtensionFramework\Events\Shopify\AppUninstalled` | Shopify | Uninstall webhook is received |
| `Lantera\ExtensionFramework\Events\Bigcommerce\AppInstalled` | BigCommerce | OAuth install callback completes |
| `Lantera\ExtensionFramework\Events\Bigcommerce\AppLoaded` | BigCommerce | App is loaded in the control panel |
| `Lantera\ExtensionFramework\Events\Bigcommerce\AppUninstalled` | BigCommerce | Uninstall callback is received |

Register listeners in your application's `EventServiceProvider` to react to installs, uninstalls, and loads.

## Database

The package ships a migration that creates a `sites` table. Run it after installation:

```bash
php artisan migrate
```

Each row represents one merchant installation, uniquely identified by `(platform, store_hash)`. Key columns include `access_token`, `plan`, `is_subscribed`, `uninstalled_at`, and a `settings` JSON column for per-store configuration.

## BigCommerce helpers

### `$site->adminUrl()`

Returns the merchant's BigCommerce control panel URL for your app:

```
https://store-{store_hash}.mybigcommerce.com/manage/app/{app_id}
```

Requires `BIGCOMMERCE_APP_ID` to be set. Useful when you need to redirect the merchant back to the control panel after an external flow (e.g. a Stripe checkout):

```php
use Lantera\ExtensionFramework\Models\Bigcommerce\Site;

\Stripe\Checkout\Session::create([
    'success_url' => $site->adminUrl(),
    'cancel_url'  => $site->adminUrl(),
    // ...
]);
```