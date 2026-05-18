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
| Payments | Razorpay *(planned — not integrated)* |
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

### Architecture
- `App\Services\HomePageService` — homepage data aggregation
- `App\Services\ProductQueryService` — catalog filters/sort/pagination
- `App\Services\CheckoutService` — place order + address resolution
- `App\Services\OrderStatusService` — admin status updates + history log
- `App\Services\AddressService` — default address handling
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
| `/checkout` | Select saved address or enter new; delivery type; place order |

**Order address snapshot:** `orders.shipping_address` (JSON) is frozen at checkout so later address edits do not change past orders.

**Order statuses:** pending → confirmed → processing → packed → shipped → out for delivery → delivered (also cancelled, returned).

---

## Not Yet Implemented (Phase 2+)

- Razorpay live payment
- Guest checkout
- Coupon application at checkout (coupons table exists; seeder may duplicate on re-run)
- Invoice PDF
- Return/refund requests
- Review submission + moderation UI
- Role/permissions, activity logs, email templates
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
| GET | `/cart` | `cart.index` | Yes |
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
  Services/
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
6. **WhatsApp** — placeholder `919000000000`; replace in layout, product show, corporate views.

---

## Configuration Checklist

- [ ] `.env` database credentials
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

Use `php artisan storage:link` for uploaded product images (future admin uploads).

---

## License

Proprietary — Shivibes Herbal Skincare
