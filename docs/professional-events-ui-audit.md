# Professional Events UI Audit

## Scope and route protection

The event-management routes are registered in `routes/professional.php` inside the accepted-professional middleware group: `auth:professional` and `professional.status`. The routes verified from the Laravel router are:

| Screen/action | Route name | Method and path | Controller |
|---|---|---|---|
| My Events | `professional.events.index` | GET `/professional/events` | `ProfessionalEventController@index` |
| Create event form | `professional.events.create` | GET `/professional/events/create` | `ProfessionalEventController@create` |
| Create event | `professional.events.store` | POST `/professional/events` | `ProfessionalEventController@store` |
| Event overview | `professional.events.show` | GET `/professional/events/{event}` | `ProfessionalEventController@show` |
| Edit event form | `professional.events.edit` | GET `/professional/events/{event}/edit` | `ProfessionalEventController@edit` |
| Update event | `professional.events.update` | PUT/PATCH `/professional/events/{event}` | `ProfessionalEventController@update` |
| Delete event | `professional.events.destroy` | DELETE `/professional/events/{event}` | `ProfessionalEventController@destroy` |
| Event Details list | `professional.event-details.index` | GET `/professional/event-details` | `EventDetailController@index` |
| Create detail form / save | `professional.event-details.create` / `.store` | GET/POST `/professional/event-details/create` and `/professional/event-details` | `EventDetailController@create` / `@store` |
| Detail overview | `professional.event-details.show` | GET `/professional/event-details/{eventDetail}` | `EventDetailController@show` |
| Edit detail form / save | `professional.event-details.edit` / `.update` | GET/PUT `/professional/event-details/{eventDetail}/edit` and `/professional/event-details/{eventDetail}` | `EventDetailController@edit` / `@update` |
| Delete details | `professional.event-details.destroy` | DELETE `/professional/event-details/{eventDetail}` | `EventDetailController@destroy` |
| Event Bookings list | `professional.event-bookings.index` | GET `/professional/event-bookings` | `EventBookingController@index` |
| Booking detail | `professional.event-bookings.show` | GET `/professional/event-bookings/{eventBooking}` | `EventBookingController@show` |
| Booking update | `professional.event-bookings.update-status` | PUT `/professional/event-bookings/{eventBooking}/status` | `EventBookingController@updateStatus` |

`/professional/event-details` is a management list of completed details plus events that still need details; it is not a parameterized event detail route. The individual detail screen uses `/professional/event-details/{eventDetail}`.

## Discovered Blade screens and dependencies

| Screen | Blade view | Data and behavior | Redesign / verification status |
|---|---|---|---|
| My Events | `resources/views/professional/events/index.blade.php` | Paginated professional-owned `AllEvent` records and total/pending/approved/rejected statistics. Create, view, edit (non-approved only), delete (non-approved only), and event-booking navigation. | Rebuilt; route registration and Blade compilation verified. Full browser and authenticated action flow not tested. |
| Create Event | `resources/views/professional/events/create.blade.php` | Existing fields: image, date, time, type, heading, short description and starting fee. POST to the existing resource route. Image preview retained. | Rebuilt; Blade compilation verified. Upload and submission not exercised. |
| Edit Event | `resources/views/professional/events/edit.blade.php` | Same field names and PUT payload; keeps the current image unless a replacement is selected. Rejected-event notes are displayed. Image preview retained. | Rebuilt; Blade compilation verified. Update/upload not exercised. |
| Event overview | `resources/views/professional/events/show.blade.php` | Event image and fallback, summary, schedule, fee, admin notes, meeting link, public event link for approved events, and existing management links. | Rebuilt; Blade compilation verified. Authenticated record rendering not tested. |
| Event Details list | `resources/views/professional/event-details/index.blade.php` | Professional-owned `EventDetail` entries and events lacking detail records. Search and 10-item client-side pagination replace the old DataTables client dependency; detail create/view/edit/delete actions remain. | Rebuilt; Blade compilation verified. Search/pagination not browser-tested. |
| Create Event Details | `resources/views/professional/event-details/create.blade.php` | Existing event selector, banner/gallery uploads, type, start date, fee, event mode, conditional city and event description. Existing event-based autofill and offline-city requirement retained. | Rebuilt; Blade compilation verified. Autofill and uploads not exercised. |
| Edit Event Details | `resources/views/professional/event-details/edit.blade.php` | Existing event ID, type, date, fee, mode/city, description and replace-all banner/gallery controls retained. Existing uploaded images are shown when present. | Rebuilt; Blade compilation verified. Updates/uploads not exercised. |
| Event Details overview | `resources/views/professional/event-details/show.blade.php` | Event relationship, details text, mode, city, start date/time/fee, banner/gallery images, and existing edit/delete/event links. | Rebuilt; Blade compilation verified. Authenticated rendering not tested. |
| Event Bookings list | `resources/views/professional/event-bookings/index.blade.php` | Server-side pagination and current query filters: customer/event search, event date range, payment status and event type. Desktop table and mobile cards use the same booking fields. Payment update uses the registered PUT route. | Rebuilt; route registration and Blade compilation verified. Filter and payment flows not exercised. |
| Booking detail | `resources/views/professional/event-bookings/show.blade.php` | Professional-owned booking, attendee, event, location, seats, amount, payment/booking status and permitted actions. Shared confirmation dialog is used for existing status updates. | Rebuilt; route registration and Blade compilation verified. Status persistence is blocked by the existing schema/model gap below. |

## Ownership and business logic retained

- `ProfessionalEventController` restricts event lists to the authenticated professional's records created by a professional and checks ownership before showing, editing, updating, or deleting an event. Approved events remain non-editable and non-deletable through the existing controller.
- `EventDetailController` scopes the list to details attached to the professional's events and checks creator type/ID before showing, editing, updating, or deleting details. Event creation still checks that the selected event belongs to the authenticated professional.
- `EventBookingController` scopes bookings through the authenticated professional's event IDs and preserves the existing filter parameters, pagination, and update endpoint.
- No schema, migration, payment, authentication, route, or public/admin UI changes were made. Event and booking query behavior was not changed.

## Theme, shared assets, and page-specific dependencies

- All screens extend `professional.layout.layout`, which supplies the shared Professional sidebar/header, Tailwind CSS 4 theme from `resources/css/tazen.css`, Poppins font, panel JavaScript, and centralized `x-yuktione.feedback` toast/dialog component.
- The layout also loads shared jQuery, Font Awesome, Toastr, SweetAlert, and Select2 CDN assets. They are layout-level dependencies used beyond these pages and were not removed or modified.
- No dedicated event-page CSS or JavaScript files were found. The previous page-specific visual code was inline in the Blade views.
- Removed the Event Details list's page-level DataTables CSS/JS CDN import and recreated its existing search/page-size-10 pagination behavior with local page markup and native JavaScript.
- Replaced native `alert()`/`confirm()` and Bootstrap dismissible-alert markup on these screens with inline validation/flash handling from the shared feedback component and `YuktioneDialog`.
- Event create/edit image previews, event-detail selector autofill, offline city visibility/required behavior, server-side booking filters, booking actions, and pagination are retained or rebuilt.
- No physical CSS/JS file or image/uploaded file was deleted. Event images and stored detail media paths remain referenced through the existing public storage disk.

## Related models and data

- Event cards use `App\Models\AllEvent` fields: `card_image`, `date`, `time`, `mini_heading`, `heading`, `short_description`, `starting_fees`, `status`, `admin_notes`, `meet_link`, `created_at` and `approved_at`. The controller does not provide booking counts, so none are fabricated.
- Event detail records use `App\Models\EventDetail` fields: `event_id`, `banner_image`, `event_type`, `event_details`, `starting_date`, `time`, `starting_fees`, `event_gallery`, `city`, and `event_mode`.
- Bookings use `App\Models\EventBooking` and its `user`/`event` relationships: event name/date/location/type, persons, phone, base/total price, payment status and creation date. No unsupported booking reference or payment fields are invented.
- Missing event and banner images render a branded placeholder; existing images are checked on the public storage disk before being rendered.

## Known blocker discovered during audit

The existing booking-status transition endpoint accepts `status` values and the detail screen retains its confirmation actions, but the checked-in `event_bookings` creation migration has no `status` column and `EventBooking::$fillable` does not include `status`. The controller's mass-assignment update therefore cannot persist those transitions under the checked-in schema/model. The UI task did not alter database schema or booking business logic; status-transition behavior cannot be reported as verified. The payment-status update field is present in the migration and model allowlist.

## Validation record

| Check | Result |
|---|---|
| `npm run build` | Passed. |
| `php artisan view:clear` followed by `php artisan view:cache` | Passed for the full Blade view set. The compiled view cache was cleared after compilation. |
| `php -l app/Http/Controllers/Professional/EventDetailController.php` | Passed. |
| Laravel router bootstrap inspection of event, details, booking and public event route names | Passed; expected paths and methods were registered. |
| `git diff --check` | Passed; Git reports only its configured CRLF-to-LF normalization warnings for the newly edited Blade files. |
| `php artisan test` | 8 tests passed; 2 unrelated `SubServiceBookingTest` tests failed during migration setup because the `header_feature_services.service_id` foreign key could not be formed. No event-specific feature test exists in `tests/`. |
| Browser rendering at 360, 390, 768, 1024, 1366 and 1440 px; console and authenticated flows | Not tested. The local browser's `http://localhost/professional/events` returned Apache 404, and no authenticated professional browser session was available. |
| Event creation/editing, uploads/deletion, search, pagination, booking filters/actions and authorization runtime checks | Not exercised in a logged-in session. |

### Test database safety note

The test failure names the MySQL database `tazen_db`. The project `.env` declares `tazen_database`, while the command-line Laravel configuration resolved to `tazen_db`; PHPUnit also enables `RefreshDatabase` in the failing suite. After the failed run, read-only inspection found `tazen_db` with 9 tables and only 7 current migrations marked as run, while the `.env` database `tazen_database` still had 91 tables. No further database-writing test or migration command was run. The state and recoverability of `tazen_db` should be confirmed before any database operation; the checked-in workspace and standard XAMPP backup folder contained no SQL dump for it.

## Second-audit status

All ten discovered page views listed above have been rebuilt with the existing theme and layout. Their templates compile and the relevant route names were verified. Runtime/browser interactions remain **not tested**, and the booking-status persistence gap remains **blocked** pending a separately approved data-layer correction. No page-specific CSS/JS files or uploaded images were removed.
