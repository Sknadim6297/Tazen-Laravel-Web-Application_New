# Professional UI audit

Audit date: 2026-10-20  
Checkpoint: `checkpoint/pre-professional-ui-rebuild-20261020` (`df2db7be`)  
Scope: Professional portal and onboarding. Public professional discovery, Admin, Customer, and the customer-facing portions of shared views are out of scope.

## Scope and checkpoint

Before UI edits, `git status --short` showed an already-dirty worktree, including changes in authentication, customer/public views, shared booking chat, Vite, feedback components, and an existing deletion of `config/excel.php.bak`. Those changes were preserved; this task did not stage or commit them. A tag was created at the then-current `HEAD` as the pre-change checkpoint. The tag records the committed base; it does not include dirty working-tree edits.

The project already uses Tailwind CSS 4 via `@tailwindcss/vite`, `resources/css/tazen.css`, Poppins, and the Yuktione tokens (`#026FD8`, `#15B1FE`, `#082B58`, `#EFF9FF`). The Professional shell now loads that existing theme and the existing Yuktione feedback components; no new Tailwind configuration or UI framework is introduced.

## Route and view counts

- 50 Blade files are in `resources/views/professional/`.
- 2 additional Professional-specific/shared-use views are `resources/views/shared/booking-chat.blade.php` and `resources/views/components/professional-onboarding.blade.php`.
- 2 associated views are not exclusively Professional-owned: `resources/views/frontend/login/login.blade.php` is the current Customer/Professional login, and `resources/views/components/onboarding-tutorial.blade.php` is a generic tutorial whose dashboard include is commented out.
- `routes/professional.php` contains 66 declarations, including 8 resource declarations: 114 expanded endpoints after resource expansion. Professional-specific declarations in `routes/web.php` add 16 statements, for 82 declaration statements / 130 expanded endpoints in this audit's route scope.
- `/professionals` and `/professionals/details/...` are public discovery routes and are excluded.

## File-by-file inventory

The main portal layout is `professional.layout.layout`. Unless noted, pages inherit its CSS and JavaScript dependencies. Before this work the layout did not load the existing Tailwind theme. It loaded Font Awesome, jQuery, Toastr, SweetAlert2, Select2 and the shared feedback component. Page-specific scripts and CDN styles are called out in the dependency notes below. Files marked **In Progress** have a Tailwind conversion underway; **Pending** means not yet rebuilt and verified.

| Blade file | Route / controller or use | Existing behavior / dependencies to preserve | Initial UI finding | Status |
|---|---|---|---|---|
| `resources/views/professional/index.blade.php` | `professional.dashboard`; route closure | Dashboard booking, revenue, client, appointment and commission figures; setup links | Legacy dashboard classes and Bootstrap alert markup | In Progress |
| `resources/views/professional/layout/layout.blade.php` | Shared portal shell | CSRF/user metadata, view sections, feedback, Select2/jQuery/SweetAlert/Toastr dependencies | Tazen branding, inline CSS/JS, old shell | In Progress |
| `resources/views/professional/sections/header.blade.php` | Shared portal shell | Profile photo, notification aggregation/modal, mark-read action, admin-chat unread polling, logout | Legacy modal/search classes and inline visual styles | In Progress |
| `resources/views/professional/sections/sidebar.blade.php` | Shared portal shell | Existing Professional navigation and logout route | Tazen logo/classes; non-responsive legacy menu | In Progress |
| `resources/views/professional/terms_agreement.blade.php` | Included from shared login | Terms acceptance text/checkbox and existing form association | Legacy visual markup; shared-login dependency | Pending |
| `resources/views/professional/login/forgot-password.blade.php` | `/professional/forgot-password`; `Professional\AuthController` | Password broker form and generic response | Standalone legacy visual styles | In Progress |
| `resources/views/professional/login/login.blade.php` | No current route reference; current login is shared | Preserve if referenced by legacy integration; verify before removal | Potential orphan; do not remove without a reference check | In Progress |
| `resources/views/professional/login/pending.blade.php` | `/professional/pending`; `ProfessionalController` | Application pending status and resubmission navigation | Legacy status page | In Progress |
| `resources/views/professional/login/register.blade.php` | `/professional/register`; `ProfessionalController` | Multipart registration, OTP send/verify/resend, password validation | Tazen branding, Bootstrap-style forms and inline CSS/JS | In Progress |
| `resources/views/professional/login/rejected.blade.php` | `/professional/rejected`; `ProfessionalController` | Rejection status and permitted resubmission behavior | Legacy status page | In Progress |
| `resources/views/professional/login/reset-password.blade.php` | `/professional/reset-password`; `Professional\AuthController` | Password broker token/email/password fields | Standalone legacy visual styles | In Progress |
| `resources/views/professional/profile/add-profile.blade.php` | `professional.profile.*`; `Professional\ProfileController` | Profile fields, upload and CropperJS image crop/upload behavior | Legacy form layout; CropperJS must remain | Pending |
| `resources/views/professional/profile/show-profile.blade.php` | `professional.profile.*`; `Professional\ProfileController` | Profile details and edit actions | Legacy profile presentation | Pending |
| `resources/views/professional/service/create.blade.php` | `professional.service.*`; `Professional\ServiceController` | Service/sub-service selection and create form | Legacy form styling | Pending |
| `resources/views/professional/service/edit.blade.php` | `professional.service.*`; `Professional\ServiceController` | Service/sub-service selection and update form | Legacy form styling | Pending |
| `resources/views/professional/service/index.blade.php` | `professional.service.*`; `Professional\ServiceController` | Service listing and CRUD actions | Legacy table/actions styling | Pending |
| `resources/views/professional/rate/create.blade.php` | `professional.rate.*`; `Professional\RateController` | Rate/session-type selection and create form | Legacy form styling | Pending |
| `resources/views/professional/rate/edit.blade.php` | `professional.rate.*`; `Professional\RateController` | Rate/session-type selection and update form | Legacy form styling | Pending |
| `resources/views/professional/rate/index.blade.php` | `professional.rate.*`; `Professional\RateController` | Rate listing and CRUD actions | Legacy table/actions styling | Pending |
| `resources/views/professional/availability/create.blade.php` | `professional.availability.*`; `Professional\AvailabilityController` | Availability creation and validation | Legacy form styling | Pending |
| `resources/views/professional/availability/edit.blade.php` | `professional.availability.*`; `Professional\AvailabilityController` | Availability update and validation | Legacy form styling | Pending |
| `resources/views/professional/availability/index.blade.php` | `professional.availability.*`; `Professional\AvailabilityController` | Availability listing and CRUD actions | Legacy table/actions styling | Pending |
| `resources/views/professional/booking/index.blade.php` | `professional.booking.*`; `Professional\BookingController` | Booking filters/search/status, details, questionnaires, document uploads | Legacy booking table and modal/UI dependencies | Pending |
| `resources/views/professional/additional-services/create.blade.php` | `professional.additional-services.create`; `AdditionalServiceController` | Booking lookup and additional-service submission | Legacy form styling | Pending |
| `resources/views/professional/additional-services/index.blade.php` | `professional.additional-services.index`; `AdditionalServiceController` | Service-request list and actions; DataTables | Legacy table styling and DataTables dependency | Pending |
| `resources/views/professional/additional-services/invoice-pdf.blade.php` | `professional.additional-services.invoice.pdf`; `AdditionalServiceController` | PDF invoice output | Print/PDF-specific layout; preserve output shape | Pending |
| `resources/views/professional/additional-services/invoice.blade.php` | `professional.additional-services.invoice`; `AdditionalServiceController` | Invoice display/download | Extends missing `professional.layouts.app` layout | Blocked |
| `resources/views/professional/additional-services/show.blade.php` | `professional.additional-services.show`; `AdditionalServiceController` | Negotiation/price, delivery date, completion and notifications | Page-specific scripts and legacy styling | Pending |
| `resources/views/professional/events/create.blade.php` | `professional.events.*`; `ProfessionalEventController` | Event fields and image/detail management | Legacy form styling | Pending |
| `resources/views/professional/events/edit.blade.php` | `professional.events.*`; `ProfessionalEventController` | Event update and image/detail management | Legacy form styling | Pending |
| `resources/views/professional/events/index.blade.php` | `professional.events.*`; `ProfessionalEventController` | Event listing and CRUD actions | Legacy table/actions styling | Pending |
| `resources/views/professional/events/show.blade.php` | `professional.events.*`; `ProfessionalEventController` | Event detail display and available actions | Legacy detail presentation | Pending |
| `resources/views/professional/event-details/create.blade.php` | `professional.event-details.*`; `EventDetailController` | Event-detail creation | Legacy form styling | Pending |
| `resources/views/professional/event-details/edit.blade.php` | `professional.event-details.*`; `EventDetailController` | Event-detail update | Legacy form styling | Pending |
| `resources/views/professional/event-details/index.blade.php` | `professional.event-details.*`; `EventDetailController` | Listing and DataTables | Legacy table styling and DataTables dependency | Pending |
| `resources/views/professional/event-details/show.blade.php` | `professional.event-details.*`; `EventDetailController` | Event-detail view | Legacy detail presentation | Pending |
| `resources/views/professional/event-bookings/index.blade.php` | `professional.event-bookings.index`; `EventBookingController` | Booking filters and status actions | Legacy table/actions styling | Pending |
| `resources/views/professional/event-bookings/show.blade.php` | `professional.event-bookings.show`; `EventBookingController` | Event booking detail and authorized actions | Legacy detail presentation | Pending |
| `resources/views/professional/reviews/index.blade.php` | `professional.reviews.index`; `ReviewController` | Review listing | Legacy listing presentation; duplicate route-name declarations | Pending |
| `resources/views/professional/chat/index.blade.php` | `professional.admin-chat.index`; `Professional\ChatController` | Admin chat messages, sending, unread count and attachment downloads | Legacy chat presentation and AJAX contracts | Pending |
| `resources/views/professional/billing/index.blade.php` | `professional.billing.index`; billing controller | Billing records, filters and invoice links | Legacy table styling; route-name duplication | Pending |
| `resources/views/professional/billing/invoice.blade.php` | Billing invoice view; professional and admin billing controllers | Shared invoice display/print/download | Shared with Admin; do not alter admin behavior | Pending |
| `resources/views/professional/plans/index.blade.php` | `professional.plans.index`; `Professional\PlanController` | Plan catalogue and purchase links | Legacy plan cards | Pending |
| `resources/views/professional/plans/my-plan.blade.php` | `professional.plans.my-plan`; `Professional\PlanController` | Current plan details | Legacy plan presentation | Pending |
| `resources/views/professional/plans/purchase.blade.php` | `professional.plans.purchase`; `Professional\PlanController` | Plan purchase initiation | Payment flow and form contracts | Pending |
| `resources/views/professional/plans/razorpay.blade.php` | `professional.plans.*`; `Professional\PlanController` | Razorpay checkout/callback | Razorpay JavaScript must remain | Pending |
| `resources/views/professional/plans/success.blade.php` | `professional.plans.success`; `Professional\PlanController` | Purchase success/return state | Legacy status page | Pending |
| `resources/views/professional/requested_services/create.blade.php` | `professional.requested_services.*`; `RequestedServiceController` | Additional information submission | Legacy form styling | Pending |
| `resources/views/professional/requested_services/edit.blade.php` | `professional.requested_services.*`; `RequestedServiceController` | Additional information update | Legacy form styling | Pending |
| `resources/views/professional/requested_services/index.blade.php` | `professional.requested_services.*`; `RequestedServiceController` | Information listing and CRUD actions | Legacy listing presentation | Pending |

### Associated views outside the Professional folder

| View | Ownership / route | Dependencies and status |
|---|---|---|
| `resources/views/shared/booking-chat.blade.php` | Shared Customer/Professional booking chat | Uses shared `resources/css/tazen.css`; preserve messages, polling, attachments, booking context and role-specific return navigation. Not changed as part of this Professional-only pass to avoid changing Customer chat. Pending scoped review. |
| `resources/views/components/professional-onboarding.blade.php` | Professional-specific tutorial component; currently not included by dashboard | Preserve current tour steps/help behavior if re-enabled. In Progress. |
| `resources/views/frontend/login/login.blade.php` | Shared Customer/Professional login view | Not modified here; changes could affect Customer authentication UI. |
| `resources/views/components/onboarding-tutorial.blade.php` | Generic tutorial component | Dashboard include is commented out; do not treat as active Professional onboarding. |

## Asset and behavior dependency map

| Dependency | Pages | Decision |
|---|---|---|
| Tailwind CSS 4 / Yuktione theme, Poppins, brand tokens | Professional shell and all views extending it | Reuse `resources/css/tazen.css`; no separate Tailwind setup |
| Yuktione feedback CSS/JS and Blade component | Shared portal layout; server flash messages | Reuse once from the Professional layout |
| Font Awesome | Existing pages, icons and older interactive markup | Keep until page migrations remove all functional references |
| jQuery, Toastr, SweetAlert2, Select2 | Shell and existing page scripts/forms | Retain during migration; remove only after every caller is verified |
| CropperJS | `profile/add-profile.blade.php` | Retain image crop/upload behavior |
| DataTables | `additional-services/index`, `event-details/index` | Retain table behavior until replaced and tested |
| Razorpay checkout script | `plans/razorpay.blade.php` | Retain payment behavior |
| `resources/css/tazen.css` | Shared chat and other site views, plus the new Professional shell | Shared asset; do not delete or globally alter |
| Uploaded profile photos, certificates, documents and event images | Profile, booking and event flows | Preserve all uploaded files and storage paths |

## Audit findings and blockers

- `Professional\AvailabilityController` references `professional.availability.show`, but `resources/views/professional/availability/show.blade.php` is absent.
- `Professional\PlanController` references `professional.plans.failed`, but `resources/views/professional/plans/failed.blade.php` is absent.
- `professional/additional-services/invoice.blade.php` extends `professional.layouts.app`, which was not found; the available shell is `professional.layout.layout`. Resolve only after validating its invoice output requirements.
- Review and billing route names are duplicated between `routes/professional.php` and `routes/web.php`; do not rename routes as part of this frontend pass.
- Admin billing also uses the shared billing invoice view. Preserve its current behavior.
- `components/professional-onboarding` is currently commented out on the dashboard; onboarding completion/reset routes exist separately. Do not silently enable or change its backend workflow.
- The shared booking chat is intentionally left pending for an isolated professional-only visual pass because the same Blade view is used by Customer pages.

## Verification status

The pre-change baseline `npm run build` and Blade view cache compilation passed on 2026-10-20; the compiled view cache was cleared afterward. These are baseline checks, not page-by-page functional or responsive verification. No Professional page is marked **Verified** until route rendering, its interactions and responsive behavior are actually exercised.

Status key: **Pending**, **In Progress**, **Rebuilt**, **Verified**, **Blocked**.
