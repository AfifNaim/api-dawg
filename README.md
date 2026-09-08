# API DAWG

**API DAWG** (Documentation API Workspace Generator) adalah aplikasi dokumentasi API internal berbasis Laravel yang digunakan untuk mencatat, mengelompokkan, dan menguji endpoint API secara langsung dari browser.

Tujuannya sederhana: satu tempat untuk mendokumentasikan alur API (mis. alur *extend reservation* D'Paragon) lengkap dengan **playground** untuk mencoba request/response tanpa alat tambahan.

---

## Fitur Utama

- **Multi-project** — setiap project mewakili satu modul/domain API (mis. "Extend Reservation").
- **Grouping API** — API di dalam project dikelompokkan per *group* (contoh: "Reservation Extend Daily") dan ditampilkan di sidebar kiri.
- **Viewer 3 kolom**:
  - **Kiri** — daftar API ber-group + **search/filter** cepat.
  - **Tengah** — detail endpoint (path/query params, request body) + **Code Sample** (auto-generate cURL) + **Response catalog** (status code) + tombol **Execute** + output response.
  - **Kanan** — **Schema response bertipe** (tabel Field / Tipe / Status wajib-opsional) untuk sukses & gagal, lengkap dengan raw JSON yang bisa dilipat.
- **Proxy Execute** — tombol Execute meneruskan request ke target asli lewat proxy internal, sehingga endpoint yang hanya reachable dari sisi server (mis. `api.dparagon2.blog` via Valet lokal) tetap bisa dicoba dari Swagger UI / viewer tanpa error *"Load failed"* atau *"Fetch error"*.
- **Auth otomatis** — jika project punya token (Bearer), token disertakan otomatis saat Execute.
- **OpenAPI spec** — setiap project mengekspos spec OpenAPI 3 (`/p/{id}/openapi`) yang bisa dipakai Swagger UI atau tool lain.

---

## Tech Stack

- **Laravel 12** (PHP 8.2)
- **MySQL** (via `.env`)
- **Tailwind CSS** (CDN) untuk UI
- **GuzzleHttp** untuk proxy forwarding
- **Laravel Valet** (lokal) untuk serve `*.blog` domain

---

## Struktur Data

| Tabel | Penjelasan |
|-------|-----------|
| `projects` | Project/modul API. Field penting: `base_url` (tampilan), `token` (Bearer auth), `proxy_target` (target asli untuk Execute). |
| `groups` | Pengelompokkan API di dalam project. |
| `apis` | Satu endpoint. Field: `method`, `endpoint`, `request_json`, `success_response`, `error_response`, `headers`, dll. |

Relasi: `Project → hasMany → Group → hasMany → Api`, serta `Api` bisa *ungrouped* (`group_id = null`).

---

## Instalasi & Menjalankan

```bash
# 1. Install dependency
composer install

# 2. Setup env
cp .env.example .env
php artisan key:generate

# 3. Migrasi database
php artisan migrate --force

# 4. Jalankan dev server (atau via Valet)
php artisan serve
# atau
valet link api-docs.blog
```

Buka `http://127.0.0.1:8000` (atau `http://api-docs.blog`).

---

## Cara Penggunaan

1. Buka halaman utama → daftar project.
2. Klik project → masuk ke viewer 3 kolom.
3. Pilih API di sidebar kiri (ber-group).
4. Isi parameter / request body di kolom tengah, lalu klik **Execute**.
5. Response muncul di kolom tengah; contoh sukses/gagal ada di kolom kanan.

### Mengelola project & API
- `/manage` — CRUD project, group, dan API.
- Setiap API bisa diisi `request_json` (contoh body), `success_response`, dan `error_response` untuk menampilkan contoh di kolom kanan.

---

## Penjelasan Proxy (Penting)

Beberapa endpoint tidak bisa diakses langsung dari browser karena:
- `base_url` project hanya untuk **tampilan** (mis. `api.dparagon.dev-afif.tech`), tapi
- kode aslinya ada di host lain yang hanya reachable dari server dokumentasi (mis. `api.dparagon2.blog` via Valet lokal).

Solusinya: isi field **`proxy_target`** di project dengan host asli (contoh `https://api.dparagon2.blog`). Saat Execute, request dikirim ke:

```
/p/{project}/proxy/{path}?{query}
```

lalu api-docs meneruskannya (method, header, body, query) ke `proxy_target` dan mengembalikan response mentah. `base_url` tetap ditampilkan apa adanya.

> Keamanan: `proxy_target` diambil dari **database project**, bukan dari input user, sehingga mencegah SSRF via header.

---

## Endpoint Internal

| Route | Fungsi |
|-------|--------|
| `GET /` | Daftar project |
| `GET /p/{project}` | Viewer 3 kolom dokumentasi |
| `GET /p/{project}/openapi` | Spec OpenAPI 3 project |
| `ANY /p/{project}/proxy/{path?}` | Proxy Execute ke `proxy_target` |
| `/manage` | Halaman kelola project/group/API |
| `/manage/projects/{project}/environments*` | CRUD Environment per project |

---

## Environments (Konvensi)

Setiap project bisa punya beberapa **environment** (mis. Development / Production), masing-masing menyimpan:
- `base_url` (tampilan server)
- `token` (Bearer JWT)
- `proxy_target` (host asli untuk Execute, opsional)

**Pengaturan ada di dalam project**: di `/manage`, buka card project → klik tombol **Environment** → panel inline berisi daftar env yang bisa diedit langsung (base_url, token, proxy_target, default) atau dibuat/hapus. (Juga ada halaman terpisah `/manage/projects/{id}/environments` untuk CRUD lengkap.)

Di viewer, pilih environment dari dropdown → `base_url`, `token`, dan `proxy_target` berubah **live** (code sample & Execute ikut berubah). Ini menggantikan pengaturan base_url/token statis di level project.

> Field `proxy_target`/`token` di tabel `projects` masih dipakai sebagai fallback bila project belum punya environment.

---

## Catatan Pengembangan

- File view utama viewer: `resources/views/projects/viewer.blade.php`
- Generator spec: `app/Http/Controllers/OpenApiController.php` (`spec()` & `proxy()`)
- Model: `app/Models/{Project,Group,Api}.php` (Api punya `pathParams()` & `queryParams()`)
- Field `proxy_target` ditambahkan lewat migration `..._add_proxy_target_to_projects_table.php`
- **Typed schema** di kolom kanan dihasilkan dari `success_response`/`error_response` (JSON) via fungsi `inferType()` di JS — bukan sekadar raw JSON.
- **Code sample** (cURL) di-generate otomatis dari method + param + body + token project.

---

## License

MIT.
