# 📘 Technical Specification — Sistem Pemesanan Online Makanan (Tanpa Pengiriman)

## 1. **Overview Project**

Sistem ini adalah **aplikasi web-based** untuk pemesanan makanan seperti GoFood, GrabFood, dan ShopeeFood namun **tanpa fitur pengiriman**. Proses pemesanan menyerupai sistem **ESB (Electronic Ordering)**, di mana pelanggan dapat melakukan pemesanan melalui web, lalu **admin/kasir** mengelola dan mengonfirmasi pesanan secara manual.

Aplikasi dibangun menggunakan:

* **Laravel 12** (Backend Framework)
* **Tailwind CSS v4** (Frontend Utility CSS)
* **Blade Template** (UI Rendering)
* **Template Dashboard & Login**: sudah tersedia di folder `/public`
* **Web-based** dan **mobile-first design**

Sistem ini mendukung 3 peran pengguna:

1. **Customer** – Melihat menu, membuat pesanan
2. **Admin/Kasir** – Mengelola pesanan, melakukan konfirmasi pembayaran
3. **Kitchen (Opsional/KDS)** – Melihat daftar pesanan yang perlu dimasak

---

## 2. **Tujuan & Masalah yang Diselesaikan**

### Tujuan Utama

* Menyediakan platform sederhana untuk pemesanan makanan di restoran/kafe.
* Mempercepat proses pemesanan tanpa perlu antre.
* Mendukung dine-in, takeaway, dan pick-up.
* Meminimalkan penggunaan kertas (e-menu, pesanan digital).

### Masalah yang Diselesaikan

* Pengunjung tidak perlu mengantre untuk memesan.
* Staff tidak perlu mencatat manual.
* Pesanan dapat dipantau secara real-time.
* Admin dapat mengatur menu dengan mudah.

---

## 3. **User Roles & Permissions**

### **Customer**

* Melihat menu
* Mencari dan memfilter menu
* Membuat keranjang belanja (Cart)
* Checkout pesanan
* Melihat status pesanan

### **Admin/Kasir**

* CRUD menu (kategori, produk)
* Mengelola pesanan masuk
* Mengubah status pesanan
* Melakukan konfirmasi pembayaran secara manual
* Mengelola user (opsional)

### **Kitchen/KDS** (opsional)

* Melihat pesanan dengan status "processing"
* Menandai pesanan "siap"

---

## 4. **Flow Sistem (End-to-End)**

### 4.1 **Flow Customer**

1. Customer membuka halaman web
2. Sistem menampilkan menu berdasarkan kategori
3. Customer memilih item dan menambahkannya ke Cart
4. Customer melakukan checkout
5. Customer memilih:

   * Dine-in (opsional input nomor meja)
   * Takeaway
   * Pick-up
6. Order masuk ke sistem sebagai **Pending**
7. Customer menunggu konfirmasi/siap dari admin atau dapur
8. Customer mengambil pesanan setelah status "Ready"

### 4.2 **Flow Admin/Kasir**

1. Admin login melalui dashboard (template ada di /public)
2. Admin membuka menu **Order Management**
3. Melihat pesanan masuk (status Pending)
4. Admin mengubah status menjadi **Processing**
5. Setelah makanan jadi, admin menandai **Ready**
6. Customer datang mengambil pesanan
7. Admin melakukan **konfirmasi pembayaran manual** → status **Completed**

### 4.3 **Flow Dapur (Kitchen)**

1. Melihat pesanan berstatus Processing
2. Memasak sesuai item
3. Dapur mengubah status menjadi Ready

---

## 5. **Entity Relationship (Database Specification)**

### 5.1 **Tables Summary**

1. `users`
2. `categories`
3. `products`
4. `orders`
5. `order_items`
6. `payments` (optional manual logs)
7. `settings` (opsional)

### 5.2 **Detail Struktur Tabel Utama**

#### **products**

* id
* category_id
* name
* description (nullable)
* price
* photo_url (nullable)
* is_active (boolean)
* created_at, updated_at

#### **orders**

* id
* order_code (string: INV-XXXX)
* customer_name (nullable)
* order_type (enum: dine-in, takeaway, pickup)
* table_number (nullable)
* total_amount
* payment_status (enum: unpaid, paid)
* order_status (enum: pending, processing, ready, completed, canceled)
* created_at, updated_at

#### **order_items**

* id
* order_id
* product_id
* quantity
* price
* subtotal

#### **payments (opsional)**

* id
* order_id
* amount
* payment_method (cash/qris/manual)
* approved_by (admin_id)
* created_at

---

## 6. **Arsitektur Aplikasi**

### 6.1 **Frontend**

* Blade Template
* Tailwind CSS v4
* Mobile-first UI
* Template dashboard & login sudah berada di `/public`, akan diintegrasikan ke Blade layout

### 6.2 **Backend**

* Laravel 12
* MVC pattern
* Route separation:

  * `/routes/web.php` untuk Customer
  * `/routes/admin.php` (opsional) untuk Admin
  * `/routes/api.php` jika perlu JSON API

### 6.3 **Organisasi Controller**

* `App/Http/Controllers/Customer` → MenuController, OrderController
* `App/Http/Controllers/Admin` → DashboardController, ProductController, OrderController
* `App/Http/Controllers/Kitchen` → KitchenController

### 6.4 **Middleware**

* `auth` → Admin/Kasir/Kitchen
* `guest` → Customer (tidak perlu login)

---

## 7. **Fitur Utama Sistem**

### **Customer Facing**

* Menu listing
* Search + filter berdasarkan kategori
* Cart management
* Checkout sederhana
* Halaman tracking status pesanan

### **Admin Side**

* Dashboard statistik
* CRUD kategori dan menu (produk)
* Manajemen pesanan (pending → processing → ready → completed)
* Konfirmasi pembayaran manual
* Cetak struk (opsional)

### **Kitchen Display System (Opsional)**

* Tampilan pesanan yang perlu dimasak
* Status update cepat

### **Opsional Tambahan**

* QR scan untuk menu/meja
* Notifikasi WhatsApp
* Real-time UI update dengan Pusher/Firebase

---

## 8. **UI/UX Guideline**

### Mobile-first Priority

* Layout minimalis, tombol besar
* Navigation bottom bar (opsional)
* Card menu sederhana dengan foto + harga
* Cart slide-up modal (opsional)

### Admin Panel

* Menggunakan template yang sudah ada di `/public`
* Pisahkan layout customer dan admin dengan 2 file Blade:

  * `resources/views/layouts/customer.blade.php`
  * `resources/views/layouts/admin.blade.php`

### Tailwind v4 Rules

* Import 1 baris saja di app.css
* Hindari custom CSS kecuali diperlukan
* Gunakan utility-first

---

## 9. **API / Routing Specification**

### Customer Routes

* `GET /` → Menu list
* `GET /cart` → Cart
* `POST /cart/add` → Add item
* `POST /checkout` → Create order
* `GET /orders/{order_code}` → Tracking

### Admin Routes

* `GET /admin` → Dashboard
* `GET /admin/orders` → List pesanan
* `PUT /admin/orders/{id}/status` → Update status
* `PUT /admin/orders/{id}/pay` → Konfirmasi pembayaran

### Kitchen Routes

* `GET /kitchen/orders` → Pesanan yang perlu dimasak

---

## 10. **Security Considerations**

* Semua route admin harus memakai `auth` dan middleware role
* Validasi form menggunakan **Form Request**
* Sanitasi input customer
* Limit API rate untuk endpoint tertentu
* Cegah manipulasi harga (hitungan total dilakukan di server)

---

## 11. **Deployment Requirements**

### Server Minimum

* PHP 8.2+
* MySQL 8
* Nginx / Apache
* Node.js 18+ (untuk build Tailwind)

### Build Frontend

```
npm install
npm run build
```

### Laravel Config

```
php artisan key:generate
php artisan migrate --seed (opsional)
php artisan storage:link
```

---

## 12. **Future Improvements**

* Integrasi pembayaran otomatis (Midtrans QRIS)
* Push notification (Firebase)
* Multi-branch restaurant
* Membership/Point system
* Voucher & promo

---

## 13. **Kesimpulan**

Dokumen ini mendefinisikan **alur**, **data model**, **arsitektur**, dan **kebutuhan teknis** untuk membangun sistem pemesanan makanan seperti GoFood tetapi **tanpa fitur pengiriman**, dengan workflow manual seperti ESB.

Aplikasi dapat langsung dikembangkan mengikuti struktur Laravel 12 modern dan Tailwind v4.

Jika kamu ingin, aku bisa buatkan:

* **Struktur folder proyek ready-to-use**
* **Migration + Model lengkap**
* **Routing + Controller skeleton**
* **Layout Blade awal (customer & admin)**

Tinggal bilang saja! 🚀
