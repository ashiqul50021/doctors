# Doccure Laravel Project Audit

## Scope
Codebase scan across `app/`, `Modules/*`, `routes/`, `database/migrations`, `database/seeders`, and registered routes via `php artisan route:list --except-vendor`.

## 1) Duplicate/Dead Code Map

### 1.1 Duplicate domain models (same tables in `app/Models` and module models)
- Doctors domain duplicates:
  - `app/Models/Doctor.php` vs `Modules/Doctors/app/Models/Doctor.php`
  - `app/Models/Appointment.php` vs `Modules/Doctors/app/Models/Appointment.php`
  - `app/Models/Patient.php` vs `Modules/Doctors/app/Models/Patient.php`
  - `app/Models/Review.php` vs `Modules/Doctors/app/Models/Review.php`
  - `app/Models/Schedule.php` vs `Modules/Doctors/app/Models/Schedule.php`
  - `app/Models/Speciality.php` vs `Modules/Doctors/app/Models/Speciality.php`
- Ecommerce domain duplicates:
  - `app/Models/Product.php` vs `Modules/Ecommerce/app/Models/Product.php`
  - `app/Models/ProductCategory.php` vs `Modules/Ecommerce/app/Models/ProductCategory.php`
  - `app/Models/Order.php` vs `Modules/Ecommerce/app/Models/Order.php`
  - `app/Models/OrderItem.php` vs `Modules/Ecommerce/app/Models/OrderItem.php`

Risk: relation drift + inconsistent casts/fillables over time.

### 1.2 Unused root controllers (not in active route map)
No active routes found for:
- `app/Http/Controllers/ProductController.php`
- `app/Http/Controllers/SearchController.php`
- `app/Http/Controllers/DoctorController.php`

Likely legacy after module migration.

### 1.3 Possibly unused root admin CRUD controllers
Active admin product/speciality/doctor/category resources are routed to module controllers (`Modules/*/routes/web.php`).
Root controllers below appear unused by current route table:
- `app/Http/Controllers/Admin/ProductController.php`
- `app/Http/Controllers/Admin/ProductCategoryController.php`
- `app/Http/Controllers/Admin/SpecialityController.php`
- `app/Http/Controllers/Admin/DoctorController.php`

### 1.4 Duplicate view stacks
Both root and module copies exist for many frontend pages:
- Root: `resources/views/frontend/*`
- Module: `Modules/Doctors/resources/views/frontend/*`, `Modules/Ecommerce/resources/views/frontend/*`

Risk: one template updated, another stale.

## 2) Priority Bug List (with fix direction)

### P0 - Access Control/Security
1. Admin module routes missing auth/role middleware:
- `Modules/Doctors/routes/web.php` (`Route::prefix('admin')->name('doctors.admin.')->group(...)`)
- `Modules/Ecommerce/routes/web.php` (`Route::prefix('admin')->name('ecommerce.admin.')->group(...)`)
- `Modules/Courses/routes/web.php` (`Route::prefix('admin')->name('courses.admin.')->group(...)`)

Fix: wrap with `->middleware(['auth','role:admin'])`.

2. Doctor dashboard routes missing auth/role middleware:
- `Modules/Doctors/routes/web.php` doctor dashboard/settings/appointments group

Fix: split booking/public and dashboard/private groups. Apply `->middleware(['auth','role:doctor'])` for dashboard/settings/schedule actions.

3. Sensitive utility routes publicly exposed:
- `Modules/Site/routes/web.php`: `/migrate`, `/migrate-fresh`, `/link`, `/optimize-clear`, `/composer-install`, `/composer-update`

Fix: remove from production routes or guard behind `auth+admin` and `app()->environment('local')`.

### P1 - Functional Breakages
4. Wrong route names used in auth redirects:
- `app/Http/Controllers/AuthController.php` uses `route('doctor.dashboard')`
- Actual registered route is `doctors.dashboard`

Fix: replace with `route('doctors.dashboard')` (and check all doctor route references).

5. Route mismatches in ecommerce views:
- `Modules/Ecommerce/resources/views/frontend/cart.blade.php` uses `route('products')`, `route('product.checkout')`
- Registered names: `ecommerce.products`, `ecommerce.checkout`

Fix: update route helpers in module views.

6. Missing views referenced by Courses controllers:
- `Modules/Courses/app/Http/Controllers/Frontend/CourseController.php` returns:
  - `courses::frontend.my-courses` (missing)
  - `courses::frontend.lesson` (missing)
- `Modules/Courses/app/Http/Controllers/Backend/CourseController.php` returns:
  - `courses::backend.courses.show` (missing)

Fix: add missing blade files or change controllers to existing view names.

7. Ecommerce module controller returns non-existent view name:
- `Modules/Ecommerce/app/Http/Controllers/Frontend/ProductController.php`
  - `return view('ecommerce::frontend.checkout', ...)`
- Existing file is `Modules/Ecommerce/resources/views/frontend/product-checkout.blade.php`

Fix: change to `ecommerce::frontend.product-checkout`.

### P1 - Schema/Code drift
8. Doctor fields referenced in code/views but not in migration schema:
- Referenced: `pricing`, `custom_price`, `clinic_city`
- Seen in:
  - `app/Http/Controllers/BookingController.php`
  - `app/Http/Controllers/SearchController.php`
  - `Modules/Doctors/app/Http/Controllers/Frontend/BookingController.php`
  - `Modules/Doctors/app/Http/Controllers/Frontend/SearchController.php`
  - multiple Doctors module views
- Not present in `database/migrations/2026_01_03_172835_create_doctors_table.php`

Fix: either migrate schema to include these fields or refactor code to use existing fields (`consultation_fee`, district/area/clinic_address).

### P2 - Correctness/maintainability
9. `AdminController@appointments` eager-loads non-existent relation:
- `app/Http/Controllers/AdminController.php` uses `Appointment::with([...,'speciality'])`
- `app/Models/Appointment.php` has no `speciality()` relation.

Fix: remove relation from eager-load or add correct relation path through doctor.

10. Duplicate validation key in settings controller:
- `app/Http/Controllers/Admin/SiteSettingController.php` has `footer_logo` twice in validation array.

Fix: keep one key.

## 3) Security Hardening Checklist

1. Protect all admin route groups with `auth + role:admin`.
2. Protect doctor-private routes with `auth + role:doctor`.
3. Protect patient pages (`/patient-dashboard`, `/patient-profile`, etc.) with `auth + role:patient`.
4. Remove/disable operational routes (`/migrate*`, `/composer-*`, `/optimize-clear`, `/link`) in non-local environments.
5. Add server-side authorization checks in module backend controllers even if routes are protected.
6. Add rate limiting for login and checkout/order endpoints.
7. Add CSRF and validation tests for critical post routes.
8. Add feature tests:
- guest cannot access `/admin/*`
- doctor cannot access admin resources
- patient cannot access doctor dashboard routes
- utility routes unavailable in production

## 4) Recommended Cleanup Sequence

1. Security first: lock route middleware + remove utility endpoints.
2. Fix route-name/view-name mismatches to stop runtime 404/RouteNotFound.
3. Resolve schema/code drift (`pricing/custom_price/clinic_city`).
4. De-duplicate by selecting one canonical model namespace per domain (prefer module models if modular architecture remains).
5. Remove dead root controllers/views after tests pass.

