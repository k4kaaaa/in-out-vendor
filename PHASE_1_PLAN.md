# Phase 1 Implementation Plan: In-Out Vendor Web

## 1. Database Schema
- **Vendors**: `id`, `name`, `address`, `contact`, `timestamps`.
- **Products**: `id`, `name`, `sku`, `current_stock` (default 0), `image`, `timestamps`.
- **Transactions**: `id`, `date`, `type` (enum: in, out), `product_id`, `vendor_id` (nullable), `qty`, `customer_name`, `receiver_name`, `photo_product`, `photo_delivery`, `timestamps`.
- **Stock Movements**: `id`, `product_id`, `transaction_id`, `type`, `qty_before`, `qty_after`, `date`, `timestamps`.

## 2. Models & Relationships
- `Product`: `hasMany(Transaction)`, `hasMany(StockMovement)`.
- `Vendor`: `hasMany(Transaction)`.
- `Transaction`: `belongsTo(Product)`, `belongsTo(Vendor)`, `hasOne(StockMovement)`.
- `StockMovement`: `belongsTo(Product)`, `belongsTo(Transaction)`.

## 3. Authentication
- Setup single admin user.
- Middleware `auth` on all transaction/master routes.

## 4. Product & Vendor Management
- Form mobile-first (Tailwind).
- Upload foto produk.

## 5. Transaction Logic (Core)
- **Barang Masuk**:
  1. Mulai DB Transaction.
  2. Simpan `Transaction`.
  3. Update `Product->current_stock += qty`.
  4. Simpan `StockMovement`.
  5. Commit.
- **Barang Keluar**:
  1. Mulai DB Transaction.
  2. Validasi `Product->current_stock >= qty`. Jika gagal, abort.
  3. Simpan `Transaction`.
  4. Update `Product->current_stock -= qty`.
  5. Simpan `StockMovement`.
  6. Commit.
- Upload foto barang & delivery.

## 6. UI/UX (Mobile-First)
- Sidebar/Navigasi responsive (glassmorphism pada nav saja).
- Tabel responsive (scroll horizontal pada tabel jika sempit).
- Form input besar & touch-friendly.

##  acceptance criteria
- Transaksi Masuk menambah stok.
- Transaksi Keluar mengurangi stok.
- Transaksi Keluar gagal jika stok kurang.
- Stock movement tercatat otomatis.
- Foto terupload dan muncul di detail.
- UI nyaman di HP.

## Risks
- Upload file besar pada koneksi lambat.
- Race condition pada update stok (gunakan `lockForUpdate`).
