Baik, dafong! Berikut **Tech Spec lengkap + alur sistem** untuk menambahkan **fitur Paket Produk (Product Bundles / Menu Packages)** ke dalam aplikasi pemesanan makanan kamu.

Dokumen ini dirancang agar **clean, scalable, dan sesuai best practice Laravel 12**.

---

# 📘 **TECH SPEC – Fitur Paket Produk (Product Packages / Bundles)**

Untuk aplikasi pemesanan online makanan (GoFood-like, ESB-like, tanpa kurir)

---

# 1. **Goal Fitur**

Menambahkan kemampuan agar restoran bisa membuat **produk paket**, misalnya:

* Paket Hemat (Kue Balok + Kopi)
* Paket Spesial (3 varian kue campur)
* Paket Sharing (isi 6 atau 12 pcs)
* Paket Custom (opsional)

Tujuan: **menawarkan harga bundling**, meningkatkan penjualan, dan mempermudah pelanggan memilih menu yang lebih menarik.

---

# 2. **Jenis Paket yang Didukung**

### **2.1 Paket Static / Fixed**

Sudah ditentukan oleh admin:

* Isi paket tetap
* Harga paket tetap
  Contoh:
  *Paket Combo A = Kue Balok Keju 1 + Kue Balok Coklat 1 + Minuman*

### **2.2 Paket Dynamic / Optional (opsional)**

Customer memilih isi paket dari pilihan tertentu.
*(Jika mau implementasi di belakang, bisa ditambahkan nanti.)*

---

# 3. **Perubahan Database**

## 3.1 Tabel Baru

### **3.1.1 `product_packages`**

Representasi paket sebagai sebuah produk khusus.

| Field       | Tipe    | Keterangan         |
| ----------- | ------- | ------------------ |
| id          | bigint  | primary key        |
| name        | string  | nama paket         |
| description | text    | opsional           |
| price       | integer | harga paket        |
| photo_url   | string  | gambar paket       |
| is_active   | boolean | apakah paket aktif |

### **3.1.2 `product_package_items`**

Relasi antara paket & produk yang ada di dalam paket.

| Field      | Tipe                              | Keterangan             |
| ---------- | --------------------------------- | ---------------------- |
| id         | bigint                            | primary key            |
| package_id | foreign key → product_packages.id |                        |
| product_id | foreign key → products.id         |                        |
| qty        | integer                           | jumlah item per produk |

---

# 4. **Model Laravel**

```
ProductPackage (hasMany ProductPackageItem)
ProductPackageItem (belongsTo Product, belongsTo ProductPackage)
Product (existing)
```

Contoh relasi di model:

### **ProductPackage.php**

```php
public function items()
{
    return $this->hasMany(ProductPackageItem::class, 'package_id');
}
```

### **ProductPackageItem.php**

```php
public function product()
{
    return $this->belongsTo(Product::class);
}
```

---

# 5. **Flow Sistem (End-to-End)**

## **5.1 Flow Admin Mengelola Paket Produk**

1. Admin masuk dashboard
2. Admin memilih menu **"Product Packages"**
3. Admin klik **Tambah Paket**
4. Admin input:

   * Nama paket
   * Harga paket
   * Deskripsi
   * Foto paket
5. Admin memilih produk-produk yang masuk ke dalam paket

   * Pilih produk
   * Tentukan qty masing-masing
6. Admin menyimpan paket
7. Paket muncul di halaman customer sebagai **produk paket**

---

## **5.2 Flow Customer Melihat & Memesan Paket**

1. Customer membuka web menu
2. Sistem menampilkan **produk reguler + produk paket**
3. Customer klik paket
4. Customer melihat isi paket (list produk penyusun)
5. Customer klik **Tambah ke Keranjang** (atau langsung checkout)
6. Sistem menambahkan paket sebagai satu item di order
7. Ketika order dibuat, sistem akan menyimpan:

   * `order_item` = 1 row
   * detail isi paket disimpan dalam field JSON atau table terpisah (opsional)

---

## **5.3 Flow di Bagian Dapur (KDS)**

1. Pesanan paket muncul sebagai item “Paket Spesial 1”
2. Staff klik detail pesanan
3. Sistem menampilkan isi paket:

   * Kue Balok Keju x1
   * Kue Balok Coklat x1
   * Minuman x1
4. Staff menyiapkan sesuai komponen
5. Status diubah menjadi **Ready**

---

## **5.4 Flow Pembayaran**

* Pembayaran tetap manual (cash/QRIS tanpa gateway)
* Harga dihitung mengikuti harga paket

---

# 6. **Alur Order untuk Paket**

### **Proses per item order:**

**Saat customer memesan:**

```
order_items:
  - item_type: product_package
  - reference_id: package_id
  - price: price_of_package
  - qty: 1 (atau lebih)
  - components: JSON (isi paket)
```

**Contoh JSON components:**

```json
[
  {"product_id": 1, "name": "Kue Balok Keju", "qty": 1},
  {"product_id": 3, "name": "Kue Balok Coklat", "qty": 1}
]
```

Ini membuat dapur mudah membaca isi paket.

---

# 7. **UI/UX Requirement**

### Customer Side:

* Kartu paket **sama seperti kartu produk biasa**
* Label kecil: **"Paket" / "Bundle"**
* Preview isi paket saat detail dibuka
* Harga paket lebih besar dari harga produk (highlight)

### Admin Side:

* UI “Tambah Produk ke Paket”
* Drag-drop urutan item paket (opsional)
* Counter qty produk dalam paket

### KDS:

* Menampilkan isi paket secara jelas
* Tidak perlu menampilkan harga unit

---

# 8. **Routing Specification**

### Admin

```
GET /admin/packages
GET /admin/packages/create
POST /admin/packages
GET /admin/packages/{id}/edit
PUT /admin/packages/{id}
DELETE /admin/packages/{id}
```

### Customer

```
GET /packages
GET /packages/{id}
```

---

# 9. **Controller Setup**

### Admin\ProductPackageController

* index
* create
* store
* edit
* update
* destroy

### Customer\ProductPackageController

* index (optional)
* show

---

# 10. **Security**

* Admin-only access untuk CRUD paket
* Pastikan saat checkout server menghitung ulang harga paket
* Validasi qty produk dalam paket

---

# 11. **Future Upgrade**

* Custom package builder (pilih isi paket)
* Promo otomatis untuk bundling
* Multiple price tiers per paket
* Tracking stok otomatis (berkurang sesuai isi paket)

---