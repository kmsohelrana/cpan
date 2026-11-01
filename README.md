# CPAN — Standalone Administrator Panel & Control Center

A modern, streamlined **Laravel 13** administrative control panel ("cpan") designed as a reusable, modular foundation for web applications.

---

## 🌟 Included Modules (অন্তর্ভুক্ত ফিচারসমূহ)

1. **Dashboard**
   - 4 Dynamic Stat Cards: System Users, Roles & Permissions, Global Settings, Infrastructure Health.
   - Real-time environment and server details (PHP 8.3, Laravel 13, DB driver, Cache, Queue).
   - Quick navigation shortcuts.

2. **User Management (`/users`)**
   - Complete CRUD (Create, Read, Update, Delete).
   - Role assignment & synchronization.
   - Profile picture / avatar upload & preview.
   - Status management (Active / Inactive).
   - Secure random password reset with notification dispatch.

3. **Roles & Permissions (`/roles`)**
   - Role creation, editing, and granular permission assignment powered by Spatie Laravel Permission.

4. **Global Settings (`/global-settings`)**
   - General toggles (System SMS, Web Push, System Email).
   - Template configuration for SMS, Push Notifications, and Emails.

5. **Infrastructure & System Health Monitoring (`/health-monitoring`)**
   - Real-time status cards & metrics for: Application, Server, Database, Storage, Queue, Redis, Mail.
   - Outbound connectivity live diagnostics (SMS Gateway, Mail SMTP, Firebase FCM, OAuth, Internet).
   - API traffic analytics (Hourly traffic & endpoint breakdown).
   - Active user sessions monitor.
   - Directory size distribution (vendor, storage, node_modules).
   - Scheduled automated snapshot collection (`health:collect-metrics`).

6. **System Logs Viewer (`/logs`)**
   - Real-time Laravel log viewer for debugging and exception monitoring.

7. **Authentication & Profile**
   - Secure login / logout with rate limiting.
   - Profile details and avatar updates.
   - Password change with banking-grade security enforcement (length, mixed case, non-sequential).

---

## 📋 System Requirements

- **PHP**: `^8.3` (Laravel 13 requires PHP 8.3+)
- **PHP Extensions**: `ext-gd`, `ext-zip`, `ext-mbstring`, `ext-curl`, `ext-fileinfo`, `ext-pdo_mysql`, `ext-bcmath`
- **Database**: MySQL 8.0+ (or MariaDB / SQLite for testing)
- **Composer**: 2.7+
- **Node.js & npm** (optional, UI assets pre-compiled)

---

## 🚀 Setup & Installation Guide (ধাপে ধাপে সম্পূর্ণ ইন্সটলেশন প্রসেস)

যেকোনো নতুন পিসিতে এই প্রজেক্টটি ক্লোন করে সম্পূর্ণ সচল করার জন্য নিচের ধাপগুলো ক্রমানুসারে অনুসরণ করুন:

### ধাপ ১: প্রাক-শর্ত যাচাই (Prerequisites)
- **PHP**: `^8.3` (Laravel 13-এর জন্য আবশ্যক)
- **Composer**: 2.x
- **MySQL Database**: ভার্সন 8.0+
- **Git**
- **Oracle Instant Client (64-bit)**: (যদি ওরাকল ডাটাবেজের সাথে কানেক্ট করতে চান)

### ধাপ ২: ওরাকল ড্রাইভার ও PHP এক্সটেনশন সেটআপ (Oracle & PHP Setup)
1. **Oracle Instant Client PATH সেট করুন**:
   - ওরাকলের অফিসিয়াল সাইট থেকে **Oracle Instant Client Basic (64-bit)** (19c বা 21c/23ai) ডাউনলোড করে একটি ফোল্ডারে আনজিপ করুন (যেমন: `C:\instantclient_19_23` বা পিসিতে ওরাকল ডাটাবেজ ইনস্টল থাকলে তার `bin` ফোল্ডার)।
   - উক্ত ফোল্ডারের পাথটি Windows-এর **System Environment Variables -> System PATH**-এ যোগ করুন।
   - PowerShell-এ ভেরিফাই করুন:
     ```powershell
     Get-Command oci.dll
     ```
2. **PHP-তে OCI8 এনাবল করুন**:
   - Laragon বা আপনার PHP ডিরেক্টরির `php.ini` ফাইল ওপেন করুন (যেমন: `C:\laragon\bin\php\php-8.3.x\php.ini`) এবং নিচের লাইন দুটি আনকমেন্ট (সেমিকোলন `;` বাদ দিন) করুন:
     ```ini
     extension=oci8_19
     extension=pdo_oci
     ```
   - টার্মিনালে এক্সটেনশন সক্রিয় হয়েছে কিনা ভেরিফাই করুন:
     ```powershell
     php -m | Select-String "oci"
     # আউটপুটে 'oci8' এবং 'PDO_OCI' দেখতে পাবেন
     ```

### ধাপ ৩: রিপোজিটরি ক্লোন ও ব্রাঞ্চ চেকআউট
```powershell
git clone <repository-url>
cd crm
git checkout cpan
```

### ধাপ ৪: কম্পোজার ডিপেন্ডেন্সি ইনস্টল (Composer Install)
```powershell
composer install
```
> 💡 *নোট: কোনো কারণে যদি ঐ পিসিতে তখনো Oracle Client সেটআপ না হয়ে থাকে, তবে এরর এড়াতে এই কমান্ডটি দিতে পারেন:*
> ```powershell
> composer install --ignore-platform-req=ext-oci8
> ```

### ধাপ ৫: এনভায়রনমেন্ট ফাইল সেটআপ
```powershell
copy .env.example .env
php artisan key:generate
```

### ধাপ ৬: ডাটাবেজ কনফিগারেশন (`.env`)
আপনার `.env` ফাইলে MySQL এবং Oracle ডাটাবেজের তথ্য দিন:
1. **MySQL কনফিগারেশন (মেইন অ্যাপ্লিকেশনের জন্য)**:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crm
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(MySQL-এ `crm` নামে ডাটাবেজ তৈরি করে রাখুন)*

2. **Oracle Database কনফিগারেশন (প্রাইমারি ও সেকেন্ডারি ওরাকল)**:
   ```env
   # Oracle 1 (Primary)
   ORACLE_DB_HOST=127.0.0.1
   ORACLE_DB_PORT=1521
   ORACLE_DB_SERVICE_NAME=FREEPDB1
   ORACLE_DB_USERNAME=my_oracle_user
   ORACLE_DB_PASSWORD=my_oracle_password

   # Oracle 2 (Secondary - অপশনাল)
   ORACLE2_DB_HOST=192.168.1.50
   ORACLE2_DB_PORT=1521
   ORACLE2_DB_SERVICE_NAME=ORCL
   ORACLE2_DB_USERNAME=cbs_user
   ORACLE2_DB_PASSWORD=cbs_password
   ```

### ধাপ ৭: ডাটাবেজ মাইগ্রেশন ও সিডিং (Database Migration & Seeder)
```powershell
php artisan migrate:fresh --seed
```

### ধাপ ৮: ওরাকল কানেকশন টেস্ট করা (Test Oracle Connection)
```powershell
php artisan oracle:test         # প্রাইমারি ওরাকল ডাটাবেজ টেস্ট করবে
php artisan oracle:test oracle2 # সেকেন্ডারি ওরাকল ডাটাবেজ টেস্ট করবে
```
*সফল হলে ল্যাটেন্সি (ms), কারেন্ট ওরাকল ইউজার ও সার্ভার ভার্সন কনসোলে দেখতে পাবেন।*

### ধাপ ৯: টেস্ট স্যুট যাচাই (Run Tests)
```powershell
php artisan test
```
*সকল ফিচার টেস্ট (33 passed) সফলভাবে পাস করবে।*

### ধাপ ১০: অ্যাপলিকেশন রান ও লগইন
```powershell
php artisan serve
```
ব্রাউজারে ভিজিট করুন: **[http://localhost:8000/login](http://localhost:8000/login)**

#### ডিফল্ট লগইন ক্রেডেনশিয়াল (Default Credentials):
| Role | Email | Password |
|---|---|---|
| **Super Admin** | `superadmin@sslwireless.com` | `12345678` |
| **System Admin** | `admin@sslwireless.com` | `12345678` |

---

## 🛢️ Multiple Oracle Database Connection Guide (অন্য পিসিতে ওরাকল সেটআপ গাইড)

এই প্রজেক্টটি একসাথে **একাধিক Oracle Database** (`oracle`, `oracle2`, `oracle3` ইত্যাদি)-এর সাথে কানেক্ট হতে পারে। যেকোনো নতুন বা অন্য পিসিতে ওরাকল কানেকশন সেটআপের সম্পূর্ণ গাইড নিচে দেওয়া হলো:

### ১. প্রাক-শর্ত (Prerequisites on Windows / Linux)

#### ক. Oracle Instant Client সেটআপ (Windows):
1. ওরাকলের অফিসিয়াল সাইট থেকে **Oracle Instant Client Basic (64-bit)** (ভার্সন 19c বা 21c/23ai) ডাউনলোড করুন (অথবা পিসিতে ওরাকল ডাটাবেজ থাকলে তার `bin` ডিরেক্টরি)।
2. আনজিপ করে একটি ফোল্ডারে রাখুন, যেমন: `C:\instantclient_19_23`।
3. ফোল্ডারটির পাথ Windows-এর **System Environment Variables -> System PATH**-এ যুক্ত করুন।
4. টার্মিনাল খুলে নিশ্চিত হন `oci.dll` পাওয়া যাচ্ছে:
   ```powershell
   Get-Command oci.dll
   ```

#### খ. PHP-তে OCI8 এক্সটেনশন সক্রিয় করা:
Laragon বা আপনার PHP ইনস্টলেশনের `php.ini` ফাইল ওপেন করুন (যেমন: `C:\laragon\bin\php\php-8.3.x\php.ini`) এবং নিচের লাইন দুটি আনকমেন্ট (সেমিকোলন `;` বাদ দিন) করুন:
```ini
extension=oci8_19    ; অথবা extension=oci8_12c (আপনার Instant Client অনুযায়ী)
extension=pdo_oci
```
টার্মিনালে এক্সটেনশন সক্রিয় হয়েছে কিনা ভেরিফাই করুন:
```powershell
php -m | Select-String "oci"
# আউটপুটে 'oci8' এবং 'PDO_OCI' দেখতে পাবেন
```

> 💡 **নোট**: যদি কোনো পিসিতে সাময়িকভাবে PHP oci8 এক্সটেনশন এনাবল করার আগে `composer install` দিতে হয়, তবে `--ignore-platform-req=ext-oci8` ফ্ল্যাগ ব্যবহার করুন:
> ```powershell
> composer install --ignore-platform-req=ext-oci8
> ```

---

### ২. এনভায়রনমেন্ট কনফিগারেশন (`.env`)

`.env` ফাইলে প্রতিটি ওরাকল ডাটাবেজের জন্য আলাদা আলাদা কনফিগারেশন সেট করুন:

```env
# ==========================================
# Oracle Primary Database (Connection: 'oracle')
# ==========================================
ORACLE_DB_HOST=127.0.0.1
ORACLE_DB_PORT=1521
ORACLE_DB_SERVICE_NAME=FREEPDB1   # অথবা SID দিতে পারেন ORACLE_DB_DATABASE এ
ORACLE_DB_DATABASE=
ORACLE_DB_USERNAME=my_oracle_user
ORACLE_DB_PASSWORD=my_oracle_password
ORACLE_DB_CHARSET=AL32UTF8
ORACLE_DB_SCHEMA=                 # অপশনাল (নির্দিষ্ট স্কিমা সেট করতে)
ORACLE_DB_TNS=                    # অপশনাল (সরাসরি TNS স্ট্রিং ব্যবহার করতে চাইলে)

# ==========================================
# Oracle Secondary Database (Connection: 'oracle2')
# ==========================================
ORACLE2_DB_HOST=192.168.1.50
ORACLE2_DB_PORT=1521
ORACLE2_DB_SERVICE_NAME=ORCL
ORACLE2_DB_DATABASE=
ORACLE2_DB_USERNAME=cbs_user
ORACLE2_DB_PASSWORD=cbs_password
ORACLE2_DB_CHARSET=AL32UTF8
ORACLE2_DB_SCHEMA=
ORACLE2_DB_TNS=

# ==========================================
# Oracle Tertiary Database (Connection: 'oracle3')
# ==========================================
ORACLE3_DB_HOST=192.168.1.60
ORACLE3_DB_PORT=1521
ORACLE3_DB_SERVICE_NAME=NPSBDB
ORACLE3_DB_DATABASE=
ORACLE3_DB_USERNAME=npsb_user
ORACLE3_DB_PASSWORD=npsb_password
ORACLE3_DB_CHARSET=AL32UTF8
ORACLE3_DB_SCHEMA=
ORACLE3_DB_TNS=
```

---

### ৩. কানেকশন টেস্ট করা (Test Oracle Connectivity)

প্রজেক্টে বিল্ট-ইন টেস্ট কমান্ড যুক্ত আছে যা দিয়ে এক ক্লিকে ওরাকল কানেকশন, ল্যাটেন্সি ও ক্রেডেনশিয়াল পরীক্ষা করা যায়:

```powershell
# প্রাইমারি ওরাকল কানেকশন টেস্ট করতে:
php artisan oracle:test

# সেকেন্ডারি ওরাকল কানেকশন টেস্ট করতে:
php artisan oracle:test oracle2

# টারশিয়ারি ওরাকল কানেকশন টেস্ট করতে:
php artisan oracle:test oracle3
```

সফল হলে কমান্ড লাইনে ল্যাটেন্সি (ms), কারেন্ট ইউজার ও সার্ভার ভার্সন দেখাবে। কোনো সমস্যা থাকলে সুনির্দিষ্ট ট্রাবলশুটিং গাইড দেখাবে।

---

### ৪. কোডে যেভাবে ব্যবহার করবেন (Code Usage Examples)

#### ১) Query Builder দিয়ে:
```php
use Illuminate\Support\Facades\DB;

// ওরাকল ১ থেকে কুয়েরি
$users = DB::connection('oracle')->table('CUSTOMERS')->where('STATUS', 1)->get();

// ওরাকল ২ থেকে কুয়েরি
$accounts = DB::connection('oracle2')->table('ACCOUNTS')->first();

// ওরাকলে র-কুয়েরি (Raw SQL)
$result = DB::connection('oracle2')->select("SELECT SYSDATE FROM DUAL");
```

#### ২) Eloquent Model দিয়ে:
ওরাকল টেবিলের জন্য `App\Models\OracleModel` এক্সটেন্ড করে মডেল তৈরি করুন:
```php
namespace App\Models;

class Account extends OracleModel
{
    // যে ওরাকল ডাটাবেজ ব্যবহার করবেন ('oracle', 'oracle2', 'oracle3'):
    protected $connection = 'oracle2';

    protected $table = 'ACCOUNTS';
    protected $primaryKey = 'ACCOUNT_ID';
    protected $sequence = 'ACCOUNT_ID_SEQ'; // অপশনাল, যদি Sequence থাকে
}

// সাধারণ Eloquent-এর মতোই ব্যবহার করুন:
$account = Account::find(1001);
```

#### ৩) একাধিক ডাটাবেজের মধ্যে ট্রানজেকশন (Cross-DB Transactions):
```php
DB::connection('oracle')->transaction(function () {
    // Operations on Database 1
});

DB::connection('oracle2')->transaction(function () {
    // Operations on Database 2
});
```

---

### ৫. সাধারণ সমস্যা ও সমাধান (Troubleshooting)

| সমস্যা / এরর | কারণ | সমাধান |
|---|---|---|
| `Call to undefined function oci_connect()` | PHP OCI8 এক্সটেনশন বন্ধ আছে | `php.ini` ফাইলে `extension=oci8_19` আনকমেন্ট করুন এবং Laragon / Web Server রিস্টার্ট দিন। |
| `ORA-12541: TNS:no listener` | ওরাকল লিসেনার বন্ধ বা পোর্ট ভুল | ওরাকল সার্ভারে লিসেনার চালু আছে কিনা চেক করুন (`lsnrctl status`) এবং পোর্ট `1521` নিশ্চিত করুন। |
| `ORA-12514: TNS:listener does not currently know of service` | সার্ভিস নেম ভুল | `.env` ফাইলে `ORACLE_DB_SERVICE_NAME` সঠিক সার্ভিস নেম দিন (যেমন: `FREEPDB1`, `ORCL`, `XE`)। |
| `ORA-01017: invalid username/password` | ইউজারনেম বা পাসওয়ার্ড ভুল | `.env` ফাইলে ওরাকল ইউজারনেম ও পাসওয়ার্ড চেক করুন। |
| `ORA-00923: FROM keyword not found` | ওরাকলে `SELECT 1` সরাসরি সাপোর্ট করে না | ওরাকলে সর্বদা `SELECT 1 FROM DUAL` ব্যবহার করুন। |

---

## 🧪 Testing
```powershell
php artisan test
```
সকল ফিচার ও ইউনিট টেস্ট (Auth, Health Monitoring, Profile, System) পাস করবে।
