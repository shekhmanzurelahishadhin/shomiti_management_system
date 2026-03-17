# নবদিগন্ত সমবায় সমিতি
## Nabadiganta Somobai Somiti — Management System

A complete, production-ready Cooperative Society Management System built with **Laravel 12**, Blade, Spatie RBAC, and Bootstrap 5 — fully aligned with the official **গঠনতন্ত্র ও নীতিমালা** (Constitution).

---

## ✅ Features

- **Role-Based Access Control** (Super Admin, Admin, Treasurer, Member)
- **Full Member Registration Form** — matches official নিবন্ধন ফর্ম exactly
  - Personal info: name, father/mother/spouse name, DOB, gender, marital status
  - Present & permanent address fields
  - Nominee details
  - Photo upload
  - Share purchase (max 2 per member × ৳1,000)
  - Entry fee ৳100 (non-refundable)
  - Referral member tracking
- **Printable Registration Card** — PDF replica of official form
- **Smart Billing** — auto-generate monthly bills (5th–15th window per গঠনতন্ত্র ধারা ৭)
- **Late Fine Engine** — auto ৳50/month after 15th (ধারা ৭.১)
- **Auto-Suspend** — members suspended after 3 consecutive missed months (ধারা ৭.১.২)
- **Payment Collection** — Cash, Bank, bKash, Nagad
- **PDF Receipts** — printable payment receipts
- **Committee (Somity Group)** — Draw system
- **Expense Management** — by category
- **Reports** — Monthly, Defaulters, Annual, PDF export
- **Dashboard** — charts, quick actions
- **Activity / Audit Log** — full trail
- **System Settings** — entry fee, share value, max members, due dates

---

## 🚀 Quick Setup

### Requirements
- PHP 8.2+
- MySQL 8.0+
- Composer 2+

### Installation

```bash
# 1. Extract and enter folder
cd nabadiganta

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Create MySQL database
mysql -u root -p -e "CREATE DATABASE nabadiganta_somiti CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Edit .env — set DB credentials
#    DB_DATABASE=nabadiganta_somiti
#    DB_USERNAME=root
#    DB_PASSWORD=yourpassword

# 7. Run migrations and seed demo data
php artisan migrate --seed

# 8. Create storage symlink (for photo uploads)
php artisan storage:link

# 9. Start server
php artisan serve
```

Open **http://localhost:8000**

---

## 🔑 Demo Login Credentials

| Role        | Email                              | Password   |
|-------------|-----------------------------------|------------|
| Super Admin | superadmin@nabadiganta.com         | `password` |
| Admin       | admin@nabadiganta.com              | `password` |
| Treasurer   | treasurer@nabadiganta.com          | `password` |
| Member      | member@nabadiganta.com             | `password` |

---

## ⚙️ Automated Scheduler

Add to server crontab:

```
* * * * * cd /path/to/nabadiganta && php artisan schedule:run >> /dev/null 2>&1
```

**Auto tasks:**
- `bills:generate-monthly` — 1st of every month at 00:05
- `bills:apply-fines` — daily at 00:10 (applies ৳50 fine + auto-suspends after 3 months)

**Manual run:**
```bash
php artisan bills:generate-monthly --month=6 --year=2025
php artisan bills:apply-fines
```

---

## 📋 Constitution Rules Implemented

| Rule (গঠনতন্ত্র) | Implemented |
|-----------------|-------------|
| ভর্তি ফি ৳100 (ধারা ৬.১) | ✅ |
| প্রতি শেয়ার ৳1,000, সর্বোচ্চ ২টি (ধারা ৬.২) | ✅ |
| জমার সময়সীমা ৫–১৫ তারিখ (ধারা ৭.২) | ✅ |
| বিলম্ব জরিমানা ৳50/মাস (ধারা ৭.১.১) | ✅ |
| টানা ৩ মাস না দিলে স্থগিত (ধারা ৭.১.২) | ✅ |
| সর্বোচ্চ সদস্য ৩০ জন (ধারা ৫.২) | ✅ |
| নমিনি তথ্য সংরক্ষণ (ধারা ১০) | ✅ |
| ডিজিটাল রেকর্ড (ধারা ১৫.২) | ✅ |

---

## 📁 Key Files

```
app/
  Models/Member.php              ← 30+ fields including nominee, address, shares
  Http/Controllers/
    MemberController.php         ← Full CRUD + photo upload + PDF
    BillController.php           ← Generate + fine + waive
    PaymentController.php        ← Record + receipt PDF
  Console/Commands/
    GenerateMonthlyBills.php     ← Scheduler
    ApplyLateFines.php           ← Fine + auto-suspend

resources/views/
  members/
    create.blade.php             ← Full নিবন্ধন ফর্ম (all fields)
    edit.blade.php               ← Full edit form
    show.blade.php               ← Profile + bills + payments + PDF button
    registration_pdf.blade.php   ← Official form replica (PDF)
  settings/index.blade.php       ← All configurable rules
```

---

## 🛡️ Roles & Permissions

| Permission        | Super Admin | Admin | Treasurer | Member |
|-------------------|:-----------:|:-----:|:---------:|:------:|
| manage users      | ✅ | ❌ | ❌ | ❌ |
| manage members    | ✅ | ✅ | ❌ | ❌ |
| manage committees | ✅ | ✅ | ❌ | ❌ |
| generate bills    | ✅ | ✅ | ✅ | ❌ |
| collect payments  | ✅ | ✅ | ✅ | ❌ |
| manage expenses   | ✅ | ✅ | ✅ | ❌ |
| view reports      | ✅ | ✅ | ✅ | ✅ |
| manage settings   | ✅ | ❌ | ❌ | ❌ |

---

## 🌐 Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 |
| Frontend | Blade + Bootstrap 5.3 |
| Charts | Chart.js 4 |
| Auth | Laravel Breeze |
| RBAC | Spatie Laravel Permission 6 |
| PDF | barryvdh/laravel-dompdf 3 |
| Database | MySQL 8 |
