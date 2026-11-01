# Bank CRM — Agent Guide

Laravel backend for a **Bangla QR merchant acquiring / CRM platform**, operated by SSLWIRELESS for **SSL Wireless**. This is a **PCI-DSS scoped payment system** — every change here should be made with Bangladesh Bank / PCI-DSS compliance in mind (see [Security & Compliance](#security--compliance) below).

## Tech Stack

- **Backend**: Laravel 13, PHP ^8.3 (`composer.json` is the source of truth at `^13.0`)
- **Auth**: Sanctum (mobile/API) + session (web admin panel) + two custom token guards (see [Guards](#guards--route-surfaces))
- **Permissions**: Spatie Laravel Permission (`app/Enums/PermissionEnum.php`, ~90 cases)
- **Database**: MySQL — schema includes hand-written stored procedures/functions (see [Domain Model](#domain-model))
- **Frontend (real UI)**: Server-rendered Blade + **OneUI v5.7** admin theme + jQuery (AJAX table/modal pattern). **Not** an SPA.
    - `vite.config.js` / `tailwind.config.js` / `resources/js` in `package.json` are Breeze/skeleton leftovers. `resources/css/` doesn't exist on disk at all. `public/build/` has a compiled Vite manifest (Vite _has_ been run at some point) but `layouts/app.blade.php` doesn't use `@vite()` — it hardcodes `asset('assets/...')` paths to the static OneUI theme. Treat Vite/Tailwind as vestigial; don't build new UI assuming it's live.
    - Pages load a separately-compiled `public/js/app.js` module directly (`import { ShowFn, CreateDataFn, UpdateDataFn, SubmitFn, DeleteDataFn, ResetFormErrorFn } from "{{ asset('js/app.js') }}"`), not via Vite.
- **PDF**: mPDF, Bangla-font support for QR sticker printing (`config/bangla-qr.php`)
- **QR generation**: `simplesoftwareio/simple-qrcode`
- **Push**: Hand-rolled Firebase Cloud Messaging v1 API — **no Firebase SDK package**, JWT service-account auth built by hand (`app/Services/Firebase/*`)
- **Excel import/export**: maatwebsite/excel
- **Testing**: Pest v4 is a dependency, but `tests/` is plain PHPUnit-style Breeze scaffolding — see [Testing](#testing)

## Guards & Route Surfaces

`config/auth.php` defines three guards:

| Guard        | Provider                               | Driver             | Actually used how                                                                                  |
| ------------ | -------------------------------------- | ------------------ | -------------------------------------------------------------------------------------------------- |
| `web`        | `App\Models\User`                      | session            | Bank staff admin panel                                                                             |
| `merchant`   | `App\Models\MerchantAccount`           | token              | Defined, but merchant-app routes actually authenticate via `auth:sanctum`, not this guard directly |
| `bankClient` | `App\Models\BankClient` (hashed token) | token, `hash=true` | Applied directly as `auth:bankClient` on third-party bank/NPSB webhook routes                      |

Route files (`routes/`):

- `web.php` — admin panel: dashboard, users, roles, leads, merchants, stores, merchant-users, merchant-app-roles, commissions, store-terminals, banks/branches, divisions/districts/thana/areas, targets (user/branch), store configuration, QR network gateway, global settings, import-export, reports (transactions, settlement, settlement off-us/on-us, target-vs-achievement, ssl-commission, commission, user-wise-lead-onboarding), password/profile, merchant-app banners, log viewer. Heavy use of `Route::resource` + custom `POST {module}/list` AJAX-table endpoints.
- `auth.php` — Breeze-style session auth; register/forgot-password/reset-password routes are **commented out** — login only, accounts are admin-provisioned.
- `api.php` — `v1` prefix, **CRM mobile app**: heartbeat, OTP-based login/verify, app-version, then `auth:sanctum + crm.app.security.check + user.security.audit`: change-password, dashboard, leads (list/save/show, gated per `PermissionEnum::APP_*`), merchants, stores (+ deploy-qr), commissions, pans, npsb-networks, generate bangla-qr, cbs/account.
- `common.php` — shared: OTP request/verify, banks/branches/divisions/districts/thana/areas, mcc/merchant/store/user lookups, static resources (genders/relations/business-types/settlement-statuses), OCR NID, then authenticated FCM-token update, location fetch, notifications.
- `merchant.php` — `v1/merchant` prefix, **merchant mobile app**: app-version, self-signup, PIN login/verify, forgot-pin, then `auth:sanctum + merchant.security.check + user.security.audit`: logout, change-pin, dashboard, balance, transactions (+summary), terminal assign, generate bangla-qr.
- `bank-tp.php` — `v1/tp-client` prefix, **third-party bank/NPSB webhook**: `auth:bankClient + verify.active.client + check.incoming.ip`, with NPSB/OnUs merchant-notification endpoints additionally gated by `verify.client.signature` (per-request HMAC/signature check on inbound bank transaction notifications).
- `console.php` — the nightly settlement schedule (see [Domain Model](#domain-model)).

All registered in `bootstrap/app.php`: `web`/`api`/`commands` via the default `withRouting()`, then `common.php`/`merchant.php`/`bank-tp.php` are manually registered as extra route groups with distinct name prefixes (`api.`, `merchant.`, `bank-tp.`). Custom middleware aliases: `request.logger`, `locale`, `force.change.password`, `check.password.expiry`, `check.incoming.ip`, `verify.active.client`, `verify.client.signature`, Spatie's `role`/`permission`/`role_or_permission`, `user.security.audit`, `merchant.security.check`, `crm.app.security.check`. A global exception renderer maps exceptions on any `api/*` request to `ApiResponseHelper`, logging every branch via `LogHelper::logEntry`.

## Domain Model

- **Identity / Org / Geography**: `User`, `Role`, `BankClient`, `Bank`, `Branch`, `Division`, `District`, `Thana`, `Area`
- **Merchant onboarding pipeline**: `Lead` (+ `LeadAudit`, `LeadReviewLog`), `Merchant` (+ `MerchantAudit`, `MerchantDocument`), `MerchantAccount` (merchant-app auth)
- **Store / Terminal**: `Store` (+ `StoreAudit`), `StoreDocument`, `StoreTerminal`, `StoreConfiguration` (+ audit), `StoreCommission` (+ audit), `StoreRequest`, `StoreSettlementRecord`, `StDailyTrnxBalance`
- **Payments / Transactions / Networks**: `Transaction`, `Pan`, `NpsbNetwork`, `NpsbInstitution`, `NpsbRequestResponse`, `CbsRequestResponse` (Core Banking System call logs), `SettlementAccount`
- **Commercials / Targets**: `Commission`, `Target`, `BranchTarget`, `Achievement`
- **Platform / Ops**: `Mcc`, `GlobalSetting`, `ImportExportBatch`, `MerchantAppBanner`, `Notification`, `Otp`, `UserDevice`, `UserSecurityAudit`

Several migrations create raw **MySQL stored functions/procedures** — check these before assuming settlement/PAN/transaction-ID logic is PHP-only:

- `ufn_check_bit_gen_function`
- `usp_npsb_acquiring_pan_gen_procedure`
- `usp_generate_transaction_id` (deterministic transaction-ID generator with check digit)
- `usp_st_daily_trnx_balance`
- `usp_st_trnx_daily_settlement`

62 migrations total; the newest (2026-06 to 2026-07) cluster around settlement, store configuration/commission audits, notifications, CBS request/response logging, and lead review logs — i.e. the Lead review workflow and settlement audit trail are the most actively developed areas right now.

### Core workflow: Lead → Merchant → Store

Lead status machine (`LeadStatusEnum`): `DRAFT → SEND_FOR_VERIFICATION → SEND_FOR_REVIEW → APPROVED` (or `REJECTED` / `NOT_INTERESTED` / `INTERESTED`). See the `lead-create-edit` memory for full form/JS architecture. Key controller logic (`app/Http/Controllers/LeadController.php`):

- `store()`/`update()` → shared private `save()` → `LeadService::assignLead()` decides create-vs-update path, `LeadService::uploadDocuments()` handles merchant/store document uploads, `MerchantService::achievement()` tracks `TargetKeyEnum::LEAD_ONBOARDING`.
- Review pipeline: `assignMe()` (reviewer self-assigns) → `review()` (multi-section remarks via `LeadReviewSectionEnum`, cycle-numbered `LeadReviewLog` entries) → `approve()` (`MerchantService::onboardMerchant()` converts Lead → Merchant).
- Every transition dispatches `SendPushNotificationJob::dispatch(..., firebaseApp: 'crm')`.
- `CbsApiService::getAccountInfo()` looks up core-banking accounts during review (Janata Bank CBS integration).
- `checkLeadEditable()`: a lead is editable only when status is not `APPROVED`/`SEND_FOR_VERIFICATION` **and** `assigned_to == Auth::id()`.
- Field officers are scoped to their own leads; division/area/branch scoping is applied at the query level throughout (see `UserController::userList()` for the same pattern applied to user management).

**Settlement pipeline** (`routes/console.php`): `process-merchant-settlement` runs daily at 01:00 and, on success, chains into `merchant-auto-settlement` (`app/Console/Commands/ProcessMerchantSettlement.php`, `MerchantAutoSettlement.php`, `RecalculateSettlementCommand.php`). This is the critical nightly batch job for a payment gateway — changes here need extra care and testing. Other maintenance commands (`ResetAllMerchantPin`, `PinProcessCheck`, `GenerateBankClient`, `CleanOldExportFiles`) are not scheduled — presumably run manually or via external cron.

## Conventions

### Web panel (Blade + jQuery)

- Controllers: thin, return Blade views. Views live at `resources/views/{module}/index|table|filter.blade.php`, `modals/`, `script.blade.php`.
- Layout: `<x-app-layout>` with `title`, `styles`, `scripts` slots.
- List pattern: `POST {module}/list` route returns a table partial loaded into `.data-table` via AJAX. Filters use `->when($request->filled('field'), fn($q) => ...)`.
- Two CRUD variants — **Modal-based** (Users, Roles, Commissions: create/edit in a shared modal, show via AJAX) vs **Page-based** (Stores, Merchants, Leads: tabbed create/edit/show pages, `fieldTabMapping` for error-tab switching). See the `web-panel` skill for full templates.
- Responses: `webResponse($code, $message, $data, $errors)` (`app/helpers.php`).
- Role guards: non-Super-Admins cannot edit Super Admin/Admin accounts (see `UserController::edit()`/`update()`).

### API (Sanctum + JSON)

- Controllers: `app/Http/Controllers/Api/V1/**` (with `Common`, `Merchant`, `ThirdParty` sub-namespaces), plus `Report/` and `Auth/`.
- Resources: `app/Http/Resources/{Module}ListResource` (lightweight) / `{Module}ViewResource` (full + relations).
- Every method starts with `LogHelper::landingLog($request, __CLASS__ . ' - ' . __FUNCTION__)` — this already redacts `['pin','password','card_no','incoming_ip','fcm_token']` from the logged payload; extend that list whenever a new sensitive field is added anywhere in the request lifecycle.
- Responses: `ApiResponseHelper::success()/validationError()/notFound()/error()` etc. — never `response()->json()` directly. Every response is also logged via `LogHelper::logEntry(LOG_INFO, ...)`.
- Pagination: `CommonEnum::PER_PAGE->value()` (10), overridable via `$request->per_page`.
- Permission checks in API routes use Laravel/Spatie's `permission:` middleware directly, including OR-combined checks: `->middleware('permission:' . A->value . '|' . B->value)`.
- See the `api` skill for full templates.

### Permissions

- `app/Enums/PermissionEnum.php`, pattern `module.action` (e.g. `user.list`), ~90 cases spanning web-panel CRUD, CRM-app (`app.lead.*`, `app.merchant.*`, ...), and merchant-app (`top_up`, `pay_bill`, ...) permission domains. Each case has a `label()` only — no `color()`/`badge()` (those are on status enums, not this one).
- Blade view / page response → `checkAuthPermission(PermissionEnum::CASE)` (aborts 401).
- JSON/AJAX response → `if (!userHasPermission(PermissionEnum::CASE)) { return webResponse(...); }`.
- Blade templates: `@can(App\Enums\PermissionEnum::CASE)`.

### Enums, files, forms

- PHP 8.1 backed enums under `app/Enums/` (27 files) — status enums (`StatusEnum`, `LeadStatusEnum`, `MerchantStatusEnum`, `TransactionStatusEnum`, `SettlementStatusEnum`, etc.) expose `label()`, `color()`, `badge()` (badge returns a hardcoded Bootstrap pill), cast in models via `'status' => StatusEnum::class`. `RoleEnum` is string-backed (9 hardcoded roles). `CommonEnum` is a pure-case enum used only for `PER_PAGE->value()`.
- File upload: `FileHelper::uploadFile('folder', $file)`; delete before replace with `FileHelper::deleteFile($model->column)`. Disk: `public`. Merchant/store document folder mapping lives in `FileHelper::getMerchantDocumentPath()` / `getStoreDocumentPath()`.
- Form Requests: `app/Http/Requests/{Module}/StoreRequest.php` + `UpdateRequest.php`; `UpdateRequest` excludes the current row from unique checks (`'unique:table,col,' . $this->{model}->id`).

### Dark mode (web panel CSS)

- Toggle class `.dark-mode` on `#page-container`, persisted via `remember-theme`.
- Elements rendered outside `#page-container` (flatpickr, select2) key off `body.dark-mode` via a MutationObserver.
- Custom overrides: `public/assets/css/custom.css`.

### Push notifications

No Firebase SDK — hand-rolled against the FCM v1 HTTP API (`app/Services/Firebase/`):

- `FirebaseClient` — takes an `$app` param (`'crm'` or `'merchant'`), loads a service-account JSON from `storage_path('app/firebase/firebase-service-account-{crm|merchant}.json')` (two separate Firebase projects, one per mobile app), builds its own RS256 JWT (`openssl_sign`) for the OAuth2 service-account flow, caches the access token 58 minutes, then POSTs to `https://fcm.googleapis.com/v1/projects/{projectId}/messages:send`. Supports single send and async multicast fan-out via Guzzle promises.
- `FirebaseMessage` — immutable fluent builder (`withTitle/withBody/withData/withPriority/withWebPushNotification`, etc.), validates `data` payload keys against FCM reserved words, already has web-push support (`Urgency` header) — no backend changes needed to extend push to browsers, just token storage/dispatch on the frontend side.
- `SendPushNotificationJob` — queued dispatch, records a `Notification` model (PENDING → SENT/FAILED). Called throughout `LeadController` with `firebaseApp: 'crm'`.
- `WebPushController` (web) handles browser FCM token registration (`update-web-fcm` route); `scripts.firebase`/`scripts.notifications` Blade partials wire this into the admin panel.

### PIN / password security helpers (`app/helpers.php`)

- `hashPassword()`/`verifyPassword()` for user accounts.
- `hashPinHmac()`/`verifyPinHmac()` — HMAC-SHA256 salted merchant PIN hash, salts from `config('app.hmac_salt1'/'hmac_salt2')`.
- `validatePin()` — 5-digit banking rules (no repeated digits, no ascending/descending sequences).
- `encryptPin()`/`decryptPin()` — AES-256-CBC using `config('app.pin_salt')`.
- Note: `globalSetting()` is currently **defined twice, verbatim**, in `app/helpers.php` (harmless — guarded by `function_exists` — but worth deduplicating next time that file is touched).

## Security & Compliance

This is a PCI-DSS certified payment gateway — treat every change as compliance-sensitive:

- **Never** hardcode real credentials, tokens, card numbers, or merchant secrets in code, docs, or examples. Use `[PLACEHOLDER]`-style values. Only reference secrets via `env()` with no real fallback value.
- ⚠️ **Known issue**: `config/cbs_api.php` (Core Banking System integration — `base_url`, `basic_auth`, `username`, `password`) and `config/ocr_nid_config.php` (`token`, `domain_url`) currently hardcode real-looking credentials/tokens as `env()` default fallbacks. `.env.example` itself also ships literal-looking (not placeholder) values for `CBS_API_BASE_URL`/`CBS_API_BASIC_AUTH`/`CBS_API_USERNAME`/`CBS_API_PASSWORD` and a fixed test mobile number (`FIXED_OTP_MOBILE_NO`). All of these are git-tracked. **Priority fix**: strip default values from the two config files (leave `env('KEY')` with no second argument), replace the `.env.example` values with `[PLACEHOLDER]`, and rotate any credential that may have been exposed via git history — regardless of whether they're UAT or production values, shipping literal-looking secrets in a committed template is a PCI-DSS/data-privacy process gap.
- `FIXED_OTP_MOBILE_NO` (OTP bypass for test/dev, in `config/otp_config.php`) must never be reachable in production.
- Security-relevant env flags map directly to BB/PCI controls — treat changes to these as compliance decisions, not just config tweaks: password history/expiry (`config/password.php`), `check.incoming.ip`/`verify.active.client`/`verify.client.signature` middleware on the `bankClient` guard, merchant login-attempt limits (`config/merchant_config.php`).
- Inbound third-party bank/NPSB traffic is defended in depth via `verify.client.signature` + `check.incoming.ip` + `verify.active.client` on the `bankClient` guard — don't weaken this without a clear reason.
- Flag any new data flow that touches NID, card/account data, or merchant PII for privacy review before merging.
- `LogHelper::landingLog()` already redacts `pin`/`password`/`card_no`/`incoming_ip`/`fcm_token` from logged request payloads — extend this list whenever new sensitive fields are introduced anywhere in the request lifecycle, and double-check `LogHelper::logEntry()` calls (e.g. plaintext temp-password logging in `UserController` when SMS/email isn't configured) don't leak credentials into log files that aren't access-controlled to the same standard.

## Testing

Pest v4 is a composer dependency, but `tests/` contains only Breeze-scaffolded PHPUnit-style classes (`tests/Feature/Auth/*`, `ExampleTest`, `ProfileTest`, `tests/Unit/ExampleTest`) — **no tests exist for the core domain** (leads, merchants, stores, transactions, settlement, NPSB webhooks, CBS integration, OTP, PIN hashing, commissions, permissions). When adding non-trivial business logic here, prefer adding a Feature test alongside it since this area has the least existing safety net.

## Related docs

- `.github/copilot-instructions.md` — condensed tech-stack/conventions reference read by GitHub Copilot (kept roughly in sync with this file, though currently out of date on the Laravel version — update it alongside this file when conventions change).
- `.claude/skills/web-panel/SKILL.md` — full scaffolding templates for new web panel CRUD modules.
- `.claude/skills/api/SKILL.md` — full scaffolding templates for new API v1 modules.
