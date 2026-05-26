# Shivibes — Premium Herbal E-Commerce

Laravel ecommerce for herbal skincare, personal gifting, corporate gifting, and festival hampers. Built for a small Indian startup scaling from WhatsApp to a professional online store.

> **For developers:** Read [`.cursor/rules/project-handoff.mdc`](.cursor/rules/project-handoff.mdc) (Cursor AI memory) for implementation status, conventions, and where work was left off. Update that file when you ship meaningful changes.

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11+ (PHP 8.2+) |
| Database | MySQL |
| Frontend | Blade, Tailwind CSS 3, Alpine.js 3 |
| Build | Vite 7 |
| Auth | Laravel Breeze |
| Payments | Razorpay (AJAX checkout + webhooks; optional COD via env) |
| Delivery | India Post (normal), Shiprocket (express) |

**Design reference:** [moha.co.in](https://www.moha.co.in/) — premium Indian gifting / lifestyle aesthetic.

---

## What Is Implemented (Phase 1 — May 2026)

### Storefront UI/UX
- Premium design system (`brand` + `gold` colors, DM Sans + Cormorant Garamond)
- Sticky header, announcement bar, mega menu, footer with newsletter
- Homepage with 15+ sections (hero, categories, trending, bestsellers, gifting hub, shop-by, about, FAQ, testimonials, Instagram grid)
- Product listing: search, category/type/price/stock filters, sorting, pagination
- Product detail: image gallery, variants display, sale badges, reviews (read), related + recently viewed, WhatsApp + share
- Toast notifications, breadcrumbs, empty states, skeleton loaders in cart drawer
- Session cart + **slide-out cart drawer** (authenticated users, Alpine + `/cart/summary` JSON)
- Search autocomplete (`GET /search/suggest?q=`)

### Features
- **Wishlist** — toggle per product, list page (`auth` required)
- **Corporate inquiries** — GST, quantity, branding, callback flags; admin status updates
- **Newsletter** — `newsletter_subscribers` table
- **Recently viewed** — session-based on product pages
- **Customer addresses** — CRUD at `/account/addresses`, default address, used at checkout
- **My Orders** — `/account/orders` list + detail with status timeline and tracking
- **Checkout** — select saved address or add new (optional save); address snapshot stored on order

### Admin
- Dashboard: orders today, pending orders, monthly revenue, low stock, customers, pending inquiries, recent orders, top products
- Full CRUD: products, categories, banners, testimonials, FAQs, users
- **Order management** — filter/search, detail page, update status/payment, courier + tracking, internal notes, status history

### Email system (May 2026)
- Centralized `App\Services\MailService` — all outbound mail with try/catch + logging
- Branded responsive HTML templates under `resources/views/mail/html/`
- **OTP email verification** for customers (registration + login if unverified)
- Order confirmation + status update emails (admin status change triggers customer mail)
- Corporate inquiry: admin alert (all `ADMIN_NOTIFICATION_EMAILS`) + customer acknowledgement
- Queue-ready mailables (`MAIL_USE_QUEUE=true` + `php artisan queue:work`)

### Architecture
- `App\Services\HomePageService` — homepage data aggregation
- `App\Services\ProductQueryService` — catalog filters/sort/pagination
- `App\Services\CheckoutService` — place order + address resolution
- `App\Services\OrderStatusService` — admin status updates + history log + status emails
- `App\Services\AddressService` — default address handling
- `App\Services\MailService` — transactional + admin notification mail
- `App\Services\OtpVerificationService` — OTP generation, rate limits, verification
- Blade components under `resources/views/components/store/`
- Homepage partials under `resources/views/store/home/sections/`
- `AppServiceProvider` shares `$navCategories` with `layouts.app` (guarded if `categories` table missing)

### Preserved (do not break)
- Laravel Breeze auth (login/register/profile)
- Session cart structure (`cart` session key, slug-indexed items)
- Checkout flow (creates Customer + Order + OrderItems; links `user_id` + address snapshot)
- Existing admin routes (extended, not removed)
- All original product/order/customer tables

---

## Customer Account & Orders Flow

| URL | Purpose |
|-----|---------|
| `/account/addresses` | List / add / edit / delete addresses; set default |
| `/account/orders` | Order history |
| `/account/orders/{id}` | Order detail, items, address snapshot, tracking, timeline |
| `/checkout` | Address + delivery + Razorpay (AJAX, no page reload) or optional COD |
| `POST /checkout/verify` | Server-side Razorpay signature verification after payment |
| `POST /webhooks/razorpay` | Razorpay webhooks (payment.captured, payment.failed, refund.processed) |

**Order address snapshot:** `orders.shipping_address` (JSON) is frozen at checkout so later address edits do not change past orders.

**Order statuses:** pending → confirmed → processing → packed → shipped → out for delivery → delivered (also cancelled, returned).

---

## Razorpay setup

Add to `.env` (Test keys from [Razorpay Dashboard](https://dashboard.razorpay.com) → API Keys → **Test mode**):

```env
RAZORPAY_KEY=rzp_test_xxxx
RAZORPAY_SECRET=your_test_secret
RAZORPAY_WEBHOOK_SECRET=whsec_xxxx
RAZORPAY_MODE=test
CHECKOUT_COD_ENABLED=false
```

**Test payment:** use card `4111 1111 1111 1111`, any future expiry, any CVV, any OTP.

**Webhook (local):** expose your app with [ngrok](https://ngrok.com) and in Razorpay Dashboard → Webhooks add URL `https://YOUR-NGROK/webhooks/razorpay` with events `payment.captured`, `payment.failed`, `refund.processed`. Copy the webhook secret into `RAZORPAY_WEBHOOK_SECRET`.

**Production:** switch Dashboard to Live mode, use live keys, set `RAZORPAY_MODE=live`, register production webhook URL.

Run `composer install` on the server (includes `razorpay/razorpay`).

---

## Not Yet Implemented (Phase 2+)

- Guest checkout
- Coupon application at checkout (coupons table exists; seeder may duplicate on re-run)
- Invoice PDF
- Return/refund requests
- Review submission + moderation UI
- Role/permissions, activity logs
- Shipping calculator / delivery estimate API
- Product quick-view modal
- Save for later (separate from wishlist)
- Full CMS / dynamic homepage section management in admin

---

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+
- XAMPP users: use `c:\xampp\php\php.exe` if `php` is not on PATH

---

## Installation (XAMPP / Windows)

```powershell
cd c:\xampp\htdocs\shivibes

composer install
copy .env.example .env
c:\xampp\php\php.exe artisan key:generate
```

Configure `.env`:

```env
DB_DATABASE=shivibes
DB_USERNAME=root
DB_PASSWORD=
```

```powershell
c:\xampp\php\php.exe artisan migrate --force
c:\xampp\php\php.exe artisan db:seed --class=CategorySeeder --force
c:\xampp\php\php.exe artisan db:seed --class=ProductSeeder --force
c:\xampp\php\php.exe artisan db:seed --class=StoreContentSeeder --force
```

> Full `db:seed` may fail on `OrderSeeder` if coupon `GLOW100` already exists — safe to skip or use `updateOrCreate` in that seeder.

```powershell
npm install
npm run build
# dev: npm run dev

c:\xampp\php\php.exe artisan serve
```

Open `http://127.0.0.1:8000`

---

## Mail & SMTP setup

Add to `.env` (see `.env.example`):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@shivibes.com
MAIL_FROM_NAME="Shivibes"
MAIL_SUPPORT_ADDRESS=support@shivibes.com
ADMIN_NOTIFICATION_EMAILS=support@shivibes.com,owner@shivibes.com
MAIL_USE_QUEUE=false
```

**Local development:** use `MAIL_MAILER=log` to write emails to `storage/logs/laravel.log`, or Mailtrap for SMTP testing.

**OTP verification:** new customers must verify email via 6-digit code at `/verify-email/otp` before cart, checkout, wishlist, or account orders. Admins are exempt.

### Queue recommendation (production)

```powershell
# .env
MAIL_USE_QUEUE=true
QUEUE_CONNECTION=database   # or redis

c:\xampp\php\php.exe artisan queue:table
c:\xampp\php\php.exe artisan migrate
c:\xampp\php\php.exe artisan queue:work
```

Mail failures are logged and never block checkout, login, or registration.

### Adding new mail types

1. Create `app/Mail/YourMail.php` (implement `ShouldQueue`)
2. Add Blade view under `resources/views/mail/html/`
3. Expose a method on `MailService` that calls `$this->send(...)`

---

## Routes

| Method | URI | Name | Auth |
|--------|-----|------|------|
| GET | `/` | `home` | No |
| GET | `/products` | `products.index` | No |
| GET | `/products/{slug}` | `products.show` | No |
| GET | `/corporate` | `corporate.create` | No |
| POST | `/corporate` | `corporate.store` | No |
| POST | `/newsletter` | `newsletter.store` | No |
| GET | `/search/suggest` | `search.suggest` | No |
| GET | `/verify-email/otp` | `verification.otp` | Yes (if unverified) |
| GET | `/cart` | `cart.index` | Yes + verified email |
| GET | `/cart/summary` | `cart.summary` | Yes (JSON) |
| POST | `/cart/add/{slug}` | `cart.add` | Yes |
| POST | `/cart/remove/{slug}` | `cart.remove` | Yes |
| GET | `/wishlist` | `wishlist.index` | Yes |
| POST | `/wishlist/toggle/{slug}` | `wishlist.toggle` | Yes |
| GET | `/checkout` | `checkout.index` | Yes |
| POST | `/checkout` | `checkout.store` | Yes |
| GET | `/admin` | `admin.dashboard` | Yes |
| GET | `/admin/products` | `admin.products.index` | Yes |
| GET | `/admin/orders` | `admin.orders.index` | Yes |
| GET | `/admin/inquiries` | `admin.inquiries.index` | Yes |
| PATCH | `/admin/inquiries/{inquiry}/status` | `admin.inquiries.update_status` | Yes |

Auth routes: `routes/auth.php` (Breeze).

---

## Database

### New tables
- `categories`, `category_product`
- `product_images`, `product_variants`
- `reviews`, `wishlists`
- `corporate_inquiries`
- `banners`, `testimonials`, `faqs`, `newsletter_subscribers`

### Extended columns
**`products`:** `sku`, `compare_at_price`, `product_type` (`personal|corporate|festival|combo`), `tags` (JSON), `is_featured`, `is_bestseller`, `is_trending`, `moq`, `meta_title`, `meta_description`

**`orders`:** `tracking_number`, `coupon_code`, `shipped_at`, `delivered_at`

**`corporate_inquiries`:** `gst_number`, `quantity`, `needs_branding`, `callback_requested`, `inquiry_type`

### Migration note
Early migrations `2026_05_15_113300_add_ecommerce_fields_to_products_table` and similar may have run **empty**. Fix migrations add missing columns safely:
- `2026_05_15_140900_fix_product_ecommerce_columns.php`
- `2026_05_15_141000_fix_reviews_and_wishlists_tables.php`

If columns are missing after migrate, run `php artisan migrate` again (fix migrations are idempotent).

### Seeders
| Seeder | Purpose |
|--------|---------|
| `CategorySeeder` | 6 categories |
| `ProductSeeder` | 7 sample products with flags + category links |
| `StoreContentSeeder` | Banners, testimonials, FAQs |
| `CustomerSeeder`, `OrderSeeder` | Legacy sample orders |

---

## Project Structure

```
app/
  Http/Controllers/
    StorefrontController.php      # home, products, product show
    CartController.php            # cart + JSON summary for drawer
    CheckoutController.php        # checkout (unchanged flow)
    WishlistController.php
    SearchController.php
    NewsletterController.php
    CorporateInquiryController.php
    Admin/                        # dashboard, products, orders, inquiries
  Models/                         # Product, Category, Banner, etc.
  Mail/                           # Mailable classes (queue-ready)
  Services/
    MailService.php
    OtpVerificationService.php
    HomePageService.php
    ProductQueryService.php
resources/
  css/app.css                     # design tokens + component classes
  js/app.js                       # Alpine: toast, searchBox, cartDrawer
  views/
    layouts/app.blade.php         # main storefront shell
    components/store/             # product-card, breadcrumb, toast, ...
    store/home/sections/          # homepage sections
    store/products/               # index, show
    store/partials/cart-drawer.blade.php
    admin/                        # admin layout + dashboard
database/seeders/
  CategorySeeder.php, ProductSeeder.php, StoreContentSeeder.php
.cursor/rules/
  maincontext.mdc                 # product/business context
  project-handoff.mdc             # dev handoff / Cursor memory (UPDATE THIS)
```

---

## Key Conventions

1. **Incremental changes** — do not rewrite working cart/checkout/auth.
2. **Blade components** — use `<x-store.*>` for storefront UI.
3. **Business logic** — prefer `app/Services/` over fat controllers.
4. **Cart session format:**
   ```php
   session('cart')[slug] = ['name', 'price', 'quantity', 'image'?];
   ```
5. **Product images** — use `$product->primaryImage()` helper.
6. **WhatsApp** — placeholder `+91 6392086152`; replace in layout, product show, corporate views.

---

## Configuration Checklist

- [ ] `.env` database credentials
- [ ] `.env` SMTP / `ADMIN_NOTIFICATION_EMAILS`
- [ ] `npm run build` after CSS/JS changes
- [ ] Replace WhatsApp number in views
- [ ] Register/login to test cart, wishlist, checkout
- [ ] Update `project-handoff.mdc` when completing a phase

---

## Deployment

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci && npm run build
```

**Uploaded images (admin products, banners, categories)**

- URLs are always `/storage/{folder}/...` (e.g. `/storage/products/xyz.jpg`).
- **Local / standard Laravel:** run `php artisan storage:link` (symlink `public/storage` → `storage/app/public`).
- **Production with `public_html` docroot (cPanel):** uploads must live under `public_html/storage/`. In `.env`, set a path **inside** PHP `open_basedir` (usually `/home/shivibes/...` — **not** `/domains/shivibes.com/...`):

```env
PUBLIC_STORAGE_ROOT=/home/shivibes/domains/shivibes.com/public_html/storage
```

Confirm the folder in cPanel File Manager, then `php artisan config:clear`.

If Laravel and `public_html` are siblings under the same allowed path, you can use `USE_PUBLIC_HTML_STORAGE=true` instead of `PUBLIC_STORAGE_ROOT`.

After deploying, **copy** files from `storage/app/public/products/` to `public_html/storage/products/` for uploads that already went to the wrong folder.

---

## License

Proprietary — Shivibes Herbal Skincare
