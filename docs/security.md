# Catatan Keamanan

## CSRF
- Semua form POST/PUT/PATCH/DELETE memakai `@csrf`.
- Laravel 13 memverifikasi origin (`Sec-Fetch-Site`) lebih dulu, lalu token CSRF sebagai cadangan.
- Uji dengan curl: POST tanpa token, dan POST dengan `Sec-Fetch-Site: cross-site`,
  keduanya ditolak dengan HTTP 419.