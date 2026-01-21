# SOFTWARE REQUIREMENTS SPECIFICATION (SRS)

## SISTEM INFORMASI PRESENSI KARYAWAN BERBASIS WEBSITE

---

**Versi Dokumen:** 1.0  
**Tanggal:** 20 Januari 2026  
**Status:** Draft

---

# BAB 1: PENDAHULUAN

## 1.1 Tujuan Penulisan Dokumen

Dokumen Software Requirements Specification (SRS) ini dibuat untuk mendeskripsikan spesifikasi sistem dari Sistem Informasi Presensi Karyawan berbasis Website. Sistem ini dirancang sebagai solusi digital untuk membantu perusahaan dalam mengelola dan memantau kehadiran karyawan secara efisien, akurat, dan real-time dengan memanfaatkan teknologi QR Code serta validasi lokasi berbasis GPS melalui browser.

Dokumen ini bertujuan menjadi panduan utama bagi seluruh pihak yang terlibat dalam pengembangan, termasuk analis sistem, pengembang perangkat lunak, penguji (quality assurance), serta desainer antarmuka, agar implementasi sistem dapat dilakukan secara konsisten sesuai dengan kebutuhan pengguna yang telah diidentifikasi.

Selain itu, dokumen ini juga menguraikan batasan sistem, ruang lingkup, serta alur interaksi yang diharapkan antara tiga role pengguna utama yaitu Admin, Resepsionis, dan Karyawan dalam satu platform website terpadu. Dokumen ini mencakup spesifikasi untuk modul-modul utama seperti manajemen karyawan, pencatatan kehadiran (check-in dan check-out) melalui scan QR Code, pengelolaan keterlambatan dengan sistem bertingkat, serta pelaporan kehadiran yang dapat diekspor dalam format Excel.

Dengan adanya dokumen SRS ini, proses pengembangan Sistem Informasi Presensi Karyawan diharapkan dapat berjalan lebih terarah, terstruktur, serta mendukung evaluasi dan pemeliharaan sistem di masa depan. Dokumen ini juga dapat dijadikan sebagai acuan untuk pengembangan fitur-fitur lanjutan sesuai dengan kebutuhan bisnis yang berkembang.

## 1.2 Audien yang Dituju dan Pembaca yang Disarankan

Dokumen Software Requirements Specification (SRS) ini ditujukan untuk berbagai pihak yang terlibat dalam pengembangan maupun pengguna Sistem Informasi Presensi Karyawan. Beberapa pembaca yang disarankan meliputi:

**Pengembang (Developer)**
Menggunakan dokumen ini sebagai acuan utama dalam membangun fitur website menggunakan framework Laravel, serta memastikan sistem berjalan sesuai dengan kebutuhan yang telah didefinisikan.

**Manajer Proyek (Project Manager)**
Membantu dalam mengatur perencanaan, ruang lingkup, serta memantau kemajuan pengembangan sistem presensi dari tahap awal hingga implementasi.

**Penguji (Tester/QA)**
Menjadikan dokumen ini sebagai dasar dalam menyusun skenario uji dan mengecek apakah fitur-fitur seperti scan QR Code, validasi lokasi, pencatatan kehadiran, dan pelaporan sudah sesuai dengan spesifikasi.

**Desainer UI/UX**
Memahami alur penggunaan dan kebutuhan sistem sehingga antarmuka website untuk Admin, Resepsionis, dan Karyawan dapat dibuat lebih ramah pengguna dan mudah dipahami.

**Pembimbing/Dosen**
Menggunakan dokumen ini sebagai bahan evaluasi terkait kelayakan konsep, arsitektur sistem, dan kualitas implementasi dalam konteks Kerja Praktik.

**Administrator Perusahaan (Admin)**
Mendapatkan gambaran mengenai fitur-fitur yang tersedia untuk mengelola data karyawan, memantau kehadiran, mengatur pengaturan perusahaan, dan menghasilkan laporan absensi dalam format Excel.

**Resepsionis**
Memahami fungsi sistem dalam membantu proses generate dan menampilkan QR Code untuk discan karyawan, serta memantau kehadiran harian secara real-time di lokasi kantor.

**Pengguna Akhir (Karyawan)**
Mengetahui cara kerja website untuk melakukan presensi harian dengan scan QR Code, melihat riwayat kehadiran pribadi melalui kalender, dan mengelola profil akun.

Dengan adanya daftar audiens ini, diharapkan setiap pihak dapat menggunakan dokumen SRS ini sesuai kebutuhan dan perannya masing-masing dalam proses pengembangan maupun penggunaan Sistem Informasi Presensi Karyawan.

## 1.3 Batasan Produk

Website Sistem Informasi Presensi Karyawan ini dibatasi pada fungsi utama berupa pencatatan kehadiran karyawan secara digital melalui mekanisme scan QR Code yang divalidasi dengan lokasi GPS. Sistem ini tidak berfungsi sebagai pelacak lokasi karyawan secara real-time, melainkan hanya memverifikasi bahwa karyawan berada dalam radius yang ditentukan pada saat melakukan absensi masuk (check-in) maupun absensi pulang (check-out).

Sistem ini dirancang khusus untuk kebutuhan internal PT. Kasau Sinar Samudera dengan pengaturan yang dapat dikonfigurasi seperti jam kerja standar, radius lokasi kantor, serta perhitungan potongan keterlambatan. Fitur pencatatan keterlambatan menggunakan sistem bertingkat (Terlambat 1, 2, dan 3) yang masing-masing memiliki konsekuensi potongan berbeda sesuai kebijakan perusahaan.

Sebagai nilai tambahan, sistem ini dilengkapi dengan modul pelaporan yang mampu menghasilkan rekap absensi bulanan dalam format Excel. Laporan tersebut mencakup informasi lengkap seperti total hari kerja, keterlambatan, ketidakhadiran (Alpha, Sakit, Izin, Cuti), serta perhitungan potongan gaji secara otomatis berdasarkan parameter yang telah dikonfigurasi oleh administrator.

Tujuan dari produk ini adalah menyediakan sarana yang sederhana, praktis, dan ramah pengguna untuk mendukung proses pencatatan kehadiran karyawan secara efisien dan akurat. Manfaat yang diharapkan mencakup penghapusan sistem absensi manual berbasis kertas, peningkatan akurasi data kehadiran, kemudahan dalam pembuatan laporan bulanan, serta transparansi informasi kehadiran bagi karyawan maupun manajemen.

Secara strategis, Sistem Informasi Presensi Karyawan ini diarahkan sebagai solusi digital terintegrasi yang dapat diakses melalui browser tanpa perlu instalasi aplikasi tambahan, sehingga lebih praktis dan mudah digunakan oleh seluruh karyawan. Di masa depan, sistem ini berpotensi dikembangkan untuk integrasi dengan sistem penggajian (payroll), notifikasi otomatis, serta fitur-fitur pendukung lainnya sesuai dengan kebutuhan operasional perusahaan yang berkembang.

---

# BAB 2: DESKRIPSI KESELURUHAN

## 2.1 Deskripsi Produk

Website Sistem Informasi Presensi Karyawan adalah sebuah platform berbasis web yang dirancang untuk membantu perusahaan dalam mengelola dan memantau kehadiran karyawan secara digital. Sistem ini memungkinkan karyawan untuk melakukan absensi masuk (check-in) dan absensi pulang (check-out) dengan cara memindai QR Code yang ditampilkan oleh resepsionis, dimana proses tersebut divalidasi berdasarkan lokasi GPS untuk memastikan karyawan berada dalam radius kantor yang telah ditentukan.

Sistem ini menyediakan tiga jenis akses pengguna dengan hak akses yang berbeda. Administrator memiliki akses penuh untuk mengelola data karyawan, melihat laporan absensi, mengatur parameter perusahaan seperti jam kerja dan radius lokasi, serta mengekspor rekap kehadiran ke dalam format Excel. Resepsionis bertugas untuk menampilkan QR Code di lokasi kantor, memantau kehadiran karyawan secara real-time pada hari berjalan, serta menambahkan catatan pada data absensi jika diperlukan. Sementara itu, karyawan dapat mengakses halaman untuk memindai QR Code, melihat riwayat kehadiran pribadi melalui tampilan kalender, serta mengelola profil akunnya.

Fitur utama dari sistem ini adalah pencatatan keterlambatan bertingkat yang terbagi menjadi tiga kategori yaitu Terlambat 1, Terlambat 2, dan Terlambat 3, dimana masing-masing kategori memiliki rentang waktu dan konsekuensi potongan yang berbeda sesuai dengan kebijakan perusahaan. Selain itu, sistem juga mampu mencatat status kehadiran lainnya seperti Hadir, Sakit, Izin, Cuti, Alpha, dan Dinas Luar Kota.

Sebagai pendukung pengambilan keputusan, sistem dilengkapi dengan modul pelaporan yang dapat menghasilkan rekap absensi bulanan dalam format Excel. Laporan tersebut mencakup rangkuman lengkap berupa total hari kerja, jumlah keterlambatan per kategori, jumlah ketidakhadiran, serta perhitungan potongan gaji secara otomatis berdasarkan parameter yang telah dikonfigurasi.

Melalui kombinasi antara mekanisme absensi berbasis QR Code, validasi lokasi GPS, pencatatan keterlambatan bertingkat, serta pelaporan yang komprehensif, Sistem Informasi Presensi Karyawan diharapkan dapat memberikan solusi yang efektif, efisien, dan transparan dalam pengelolaan kehadiran karyawan di lingkungan PT. Kasau Sinar Samudera.

## 2.2 Fungsi Produk

Sistem Informasi Presensi Karyawan menyediakan beberapa fungsi utama, seperti:

**Pencatatan Kehadiran (Check-in dan Check-out)**
Memungkinkan karyawan melakukan absensi masuk dan pulang dengan memindai QR Code melalui browser, yang divalidasi berdasarkan lokasi GPS untuk memastikan keberadaan di area kantor.

**Manajemen QR Code**
Menyediakan fitur bagi resepsionis untuk menampilkan QR Code harian yang dapat dipindai oleh karyawan, serta memantau daftar kehadiran secara real-time pada hari berjalan.

**Pencatatan Keterlambatan Bertingkat**
Mencatat keterlambatan karyawan dalam tiga kategori (Terlambat 1, 2, dan 3) berdasarkan rentang waktu yang telah dikonfigurasi, lengkap dengan perhitungan potongan masing-masing.

**Manajemen Data Karyawan**
Memungkinkan administrator untuk menambah, mengubah, dan menghapus data karyawan termasuk informasi profil, role, dan gaji pokok.

**Pencatatan Status Kehadiran**
Mendukung pencatatan berbagai status kehadiran seperti Hadir, Sakit, Izin, Cuti, Alpha, dan Dinas Luar Kota, baik secara otomatis maupun manual oleh admin/resepsionis.

**Riwayat Kehadiran**
Menyajikan riwayat absensi karyawan dalam bentuk tampilan kalender yang menampilkan status kehadiran setiap harinya.

**Pengaturan Perusahaan**
Memungkinkan administrator mengonfigurasi parameter seperti jam masuk, jam pulang, radius lokasi kantor, serta persentase potongan untuk setiap kategori keterlambatan.

**Pelaporan dan Export Excel**
Menghasilkan rekap absensi bulanan dalam format Excel yang mencakup total hari kerja, jumlah keterlambatan, ketidakhadiran, serta perhitungan potongan gaji secara otomatis.

**Manajemen Profil**
Memungkinkan setiap pengguna untuk melihat dan memperbarui informasi profil serta mengubah password akun.

## 2.3 Penggolongan Karakter

Sistem Informasi Presensi Karyawan ditujukan untuk berbagai jenis pengguna yang memiliki kebutuhan berbeda. Identifikasi karakteristik pengguna ini penting agar sistem dapat dirancang sesuai kemampuan, hak akses, serta tujuan penggunaannya. Secara umum, pengguna website Sistem Informasi Presensi Karyawan dapat digolongkan ke dalam beberapa kategori sebagaimana ditunjukkan pada tabel berikut.

| Kategori Pengguna | Tugas                                                                                                                                                                                                                            | Hak Akses ke Aplikasi                                                                                   | Kemampuan yang Harus Dimiliki                                                                                                                                          |
| ----------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Administrator** | Mengelola seluruh data karyawan, memantau kehadiran semua karyawan, mengatur parameter perusahaan (jam kerja, radius lokasi, potongan keterlambatan), serta menghasilkan dan mengekspor laporan absensi bulanan                  | Akses penuh: Create, Read, Update, Delete data karyawan, pengaturan perusahaan, dan laporan absensi     | Mampu menggunakan browser, memahami manajemen data, mampu mengoperasikan fitur export Excel, serta memahami konsep pengaturan sistem                                   |
| **Resepsionis**   | Menampilkan QR Code harian untuk absensi karyawan, memantau kehadiran karyawan secara real-time pada hari berjalan, menambahkan catatan pada data absensi, serta melakukan absensi tanpa QR Code menggunakan validasi lokasi GPS | Read dan Update data absensi, Create catatan pada data kehadiran, serta akses untuk menampilkan QR Code | Mampu menggunakan browser, memahami cara menampilkan QR Code di layar, mampu memantau daftar kehadiran, serta mampu menambahkan catatan manual                         |
| **Karyawan**      | Melakukan absensi masuk (check-in) dan pulang (check-out) dengan memindai QR Code, melihat riwayat kehadiran pribadi melalui tampilan kalender, serta mengelola profil akun                                                      | Read data kehadiran pribadi, Create data absensi (check-in/out), Update profil pribadi                  | Mampu menggunakan browser, mampu mengakses kamera perangkat untuk scan QR Code, memahami cara memberikan izin akses lokasi GPS, serta mampu membaca kalender kehadiran |

## 2.4 Lingkungan Operasi

Website Sistem Informasi Presensi Karyawan akan beroperasi pada lingkungan berikut:

**Platform**

Aplikasi berbasis web yang dapat diakses melalui browser modern dengan dukungan fitur kamera dan GPS/geolokasi. Tidak memerlukan instalasi khusus di perangkat pengguna, cukup mengakses URL sistem melalui browser. Sistem dapat diakses baik melalui jaringan lokal (intranet) maupun internet sesuai konfigurasi server.

**Perangkat Keras**

Minimal perangkat dengan RAM 4 GB dan prosesor dual-core untuk pengalaman pengguna yang optimal. Mendukung berbagai jenis perangkat seperti PC desktop, laptop, smartphone, dan tablet. Perangkat harus dilengkapi dengan kamera untuk fitur scan QR Code dan GPS/sensor lokasi untuk validasi kehadiran berbasis radius.

**Sistem Operasi**

Kompatibel dengan sistem operasi yang mendukung browser modern, seperti Windows 10 ke atas, macOS 10.14 (Mojave) ke atas, Linux Ubuntu 20.04 ke atas, Android 9.0 (Pie) ke atas, dan iOS 13 ke atas. Sistem dapat diakses dari berbagai platform tanpa memerlukan penyesuaian khusus.

**Perangkat Lunak Pendukung**

Browser modern seperti Google Chrome versi 90+, Mozilla Firefox versi 88+, Microsoft Edge versi 90+, atau Safari versi 14+ yang mendukung HTML5, CSS3, JavaScript ES6+, Geolocation API, dan Media Devices API untuk akses kamera.

Framework backend Laravel versi 11.x dengan PHP versi 8.2 atau lebih tinggi sebagai server-side processing. Database MySQL versi 8.0 atau MariaDB versi 10.5 ke atas untuk penyimpanan data.

Teknologi frontend menggunakan Tailwind CSS untuk styling, Alpine.js untuk interaktivitas, dan Vite sebagai build tool. Library SimpleSoftwareIO/simple-qrcode untuk generate QR Code, Maatwebsite Laravel Excel untuk export laporan dalam format Excel, serta Spatie Laravel Permission untuk manajemen role dan permission.

Server web Apache atau Nginx dengan support PHP-FPM untuk menjalankan aplikasi Laravel. Sistem juga dapat dijalankan menggunakan built-in PHP server untuk keperluan development.

Geolocation API browser untuk mendapatkan koordinat GPS pengguna, serta Media Devices API untuk mengakses kamera perangkat dalam proses scan QR Code. Koneksi internet atau jaringan lokal yang stabil diperlukan untuk komunikasi antara client dan server.

## 2.5 Batasan Desain dan Implementasi

Dalam pengembangan Sistem Informasi Presensi Karyawan, terdapat beberapa batasan desain dan implementasi yang perlu diperhatikan, seperti:

**Keterbatasan Hardware**

Website harus tetap dapat berjalan pada perangkat dengan spesifikasi minimal seperti RAM 4GB dan prosesor dual-core untuk menjangkau berbagai jenis perangkat yang digunakan karyawan. Optimalisasi penggunaan memori dan CPU pada browser diperlukan agar tidak membebani perangkat, terutama pada fitur scan QR Code yang membutuhkan akses kamera secara real-time. Perangkat pengguna wajib memiliki GPS atau sensor lokasi yang aktif untuk fitur validasi kehadiran berbasis radius.

**Keterbatasan Teknologi**

Sistem dibangun menggunakan framework Laravel versi 11.x dengan PHP 8.2 sebagai backend, yang berarti memerlukan server dengan dukungan PHP versi tersebut atau lebih tinggi. Database yang digunakan dibatasi pada MySQL 8.0 atau MariaDB 10.5 ke atas untuk memastikan kompatibilitas dengan fitur-fitur Laravel modern.

Teknologi frontend menggunakan Tailwind CSS untuk styling dan Alpine.js untuk interaktivitas, dengan Vite sebagai build tool. Hal ini memerlukan proses build saat deployment untuk mengoptimalkan aset CSS dan JavaScript.

Fitur QR Code menggunakan library SimpleSoftwareIO/simple-qrcode yang menghasilkan QR Code dalam format SVG atau PNG. Export laporan Excel bergantung pada library Maatwebsite Laravel Excel dengan batasan format output yaitu XLSX.

Sistem validasi lokasi bergantung pada Geolocation API browser yang memerlukan koneksi HTTPS dan izin akses lokasi dari pengguna. Akurasi GPS tergantung pada kualitas sensor perangkat dan kondisi lingkungan.

**Kebijakan Perusahaan dan Regulasi**

Sistem harus mematuhi kebijakan perusahaan PT. Kasau Sinar Samudera terkait jam kerja, radius kantor, dan perhitungan potongan keterlambatan. Setiap perubahan kebijakan memerlukan update konfigurasi di database melalui halaman pengaturan admin.

Data kehadiran karyawan termasuk lokasi GPS bersifat sensitif dan harus diperlakukan sesuai prinsip perlindungan data pribadi. Akses ke data hanya diberikan kepada pihak yang berwenang sesuai role masing-masing.

**Bahasa dan Aksesibilitas**

Bahasa utama yang digunakan dalam sistem adalah Bahasa Indonesia untuk semua antarmuka, pesan error, dan dokumentasi. Dukungan bahasa Inggris atau bahasa lainnya dapat ditambahkan pada tahap pengembangan selanjutnya jika diperlukan.

Desain antarmuka harus responsif dan mudah digunakan di berbagai ukuran layar, dari desktop hingga smartphone, mengingat karyawan akan mengakses sistem dari perangkat yang beragam.

**Protokol Komunikasi**

Semua komunikasi antara client dan server harus menggunakan protokol HTTPS untuk menjamin keamanan data, terutama saat mengirimkan informasi sensitif seperti koordinat lokasi dan data kehadiran. Penggunaan HTTP (non-secure) hanya diperbolehkan untuk keperluan development lokal.

API endpoint yang diakses oleh sistem harus mengikuti standar RESTful API dengan response format JSON. Authentication menggunakan Laravel Sanctum dengan token-based authentication untuk mengamankan setiap request.

**Pertimbangan Keamanan**

Data lokasi GPS karyawan hanya dicatat pada saat melakukan absensi dan tidak dilacak secara real-time di luar waktu tersebut. Data koordinat hanya digunakan untuk validasi radius dan disimpan sebagai bagian dari record absensi.

Sistem menggunakan role-based access control (RBAC) melalui Spatie Laravel Permission untuk memastikan setiap pengguna hanya dapat mengakses fitur sesuai dengan hak aksesnya. Password pengguna disimpan dalam bentuk hash menggunakan bcrypt.

Session management menggunakan Laravel session dengan CSRF protection untuk mencegah serangan Cross-Site Request Forgery. Validasi input dilakukan di sisi server untuk mencegah SQL injection dan XSS attacks.

**Standar Pemrograman**

Pengembangan backend mengikuti standar penulisan kode PHP PSR-1, PSR-2, dan PSR-12 sesuai best practices Laravel. Struktur kode harus mengikuti pola MVC (Model-View-Controller) untuk memudahkan pemeliharaan dan pengembangan lebih lanjut.

Pengembangan frontend mengikuti standar penulisan JavaScript modern (ES6+) dengan pendekatan modular dan reusable components. Kode CSS menggunakan utility-first approach dari Tailwind CSS untuk konsistensi styling.

Penggunaan Version Control Git wajib diterapkan dengan repository di GitHub atau platform sejenis sebagai standar kolaborasi tim. Commit message harus deskriptif dan mengikuti conventional commits format.

Database migration harus digunakan untuk setiap perubahan struktur database, dan seeder digunakan untuk data awal atau demo. Dokumentasi kode menggunakan docblock PHP standar untuk memudahkan pemahaman fungsi dan method.

## 2.6 Dokumentasi Pengguna

Untuk mendukung penggunaan Sistem Informasi Presensi Karyawan, dokumentasi pengguna akan disediakan dalam bentuk panduan penggunaan sistem yang komprehensif. Dokumentasi ini bertujuan untuk membantu pengguna memahami alur kerja dan fitur-fitur utama website sesuai dengan role masing-masing.

Dokumentasi pengguna yang disediakan melalui panduan penggunaan website dalam bentuk dokumen tertulis yang menjelaskan langkah-langkah penggunaan sistem secara detail. Penjelasan fitur utama mencakup cara melakukan absensi dengan scan QR Code, validasi lokasi GPS, pengelolaan data karyawan, pemantauan kehadiran real-time, dan cara menghasilkan laporan absensi bulanan dalam format Excel.

Panduan khusus untuk Administrator mencakup penjelasan mengenai pengelolaan data karyawan (tambah, edit, hapus), pengaturan parameter perusahaan seperti jam kerja, radius lokasi kantor, konfigurasi potongan keterlambatan bertingkat, serta cara mengekspor dan membaca laporan rekap absensi. Panduan ini juga menjelaskan cara mengubah status kehadiran secara manual dan menghapus data absensi berdasarkan bulan tertentu.

Panduan khusus untuk Resepsionis mencakup langkah-langkah menampilkan QR Code harian di layar, cara memantau daftar kehadiran karyawan secara real-time pada hari berjalan, serta cara menambahkan catatan atau keterangan pada data absensi karyawan. Panduan ini juga menjelaskan cara melakukan absensi tanpa scan QR Code menggunakan validasi lokasi GPS saja.

Panduan khusus untuk Karyawan mencakup cara mengakses halaman scan QR Code melalui browser, memberikan izin akses kamera dan lokasi GPS, melakukan scan QR Code untuk check-in dan check-out, melihat riwayat kehadiran pribadi melalui tampilan kalender, serta cara mengelola profil akun dan mengubah password.

Petunjuk teknis mencakup penjelasan mengenai cara memberikan izin akses kamera pada berbagai jenis browser (Chrome, Firefox, Safari, Edge), cara memberikan izin akses lokasi GPS pada perangkat desktop dan mobile, troubleshooting umum jika QR Code tidak terbaca atau lokasi GPS tidak terdeteksi, serta rekomendasi browser dan versi untuk pengalaman terbaik.

Panduan singkat mengenai interpretasi status kehadiran seperti Hadir, Terlambat (Level 1, 2, 3), Sakit, Izin, Cuti, Alpha, dan Dinas Luar Kota. Penjelasan mengenai cara kerja sistem validasi radius lokasi dan bagaimana sistem menghitung keterlambatan berdasarkan jam masuk yang dikonfigurasi.

Petunjuk umum navigasi antarmuka agar pengguna dapat menggunakan sistem secara efektif, termasuk penjelasan menu sidebar, cara menggunakan fitur pencarian dan filter pada halaman laporan, serta cara membaca informasi pada dashboard. Dokumentasi ini juga mencakup FAQ (Frequently Asked Questions) untuk menjawab pertanyaan umum yang sering muncul.

---

# BAB 3: KEBUTUHAN ANTARMUKA EKSTERNAL

## 3.1 User Interfaces

Antarmuka pengguna merupakan komponen utama yang menghubungkan pengguna dengan Sistem Informasi Presensi Karyawan. Desain antarmuka harus sederhana, intuitif, serta mendukung alur pengguna utama sesuai dengan role masing-masing, yaitu melakukan absensi, memantau kehadiran, mengelola data karyawan, dan menghasilkan laporan. Semua halaman harus responsif agar dapat diakses dengan nyaman melalui perangkat desktop maupun mobile.

Rincian antarmuka utama pada sistem dikelompokkan berdasarkan role pengguna sebagai berikut:

### 3.1.1 Antarmuka Umum (Semua Role)

**Halaman Login**

Formulir login dengan field username atau email dan password. Terdapat ikon toggle untuk menampilkan atau menyembunyikan password. Tombol login dengan validasi input di sisi client dan server. Pesan error ditampilkan jika kredensial salah atau field kosong. Desain halaman sederhana dan profesional dengan logo perusahaan.

**Halaman Profil Pengguna**

Menampilkan informasi profil: foto profil, nama, email, role. Form untuk update foto profil dengan preview sebelum upload. Form untuk update nama dan email. Form terpisah untuk ubah password dengan field: password lama, password baru, konfirmasi password baru. Tombol simpan untuk setiap section. Validasi input dan konfirmasi sebelum menyimpan perubahan.

**Sidebar Navigasi**

Menu navigasi konsisten di semua halaman dengan icon yang jelas. Menu disesuaikan berdasarkan role pengguna. Terdapat menu: Beranda, Laporan Absensi, Data Karyawan (khusus Admin), Pengaturan Perusahaan (khusus Admin), dan Profil. Untuk Resepsionis dan Karyawan terdapat menu: Beranda, Absensi (Scan QR atau Generate QR), Riwayat Kehadiran, dan Profil.

### 3.1.2 Antarmuka Administrator

**Halaman Beranda (Dashboard Admin)**

Menampilkan statistik kehadiran ringkasan hari ini seperti total karyawan hadir, terlambat, dan tidak hadir. Grafik atau chart untuk visualisasi tren kehadiran mingguan atau bulanan. Widget kalender yang menampilkan status kehadiran personal admin. Akses cepat ke menu-menu utama seperti Kelola Karyawan dan Lihat Laporan.

**Halaman Manajemen Karyawan**

Tabel daftar karyawan dengan kolom: foto profil, nama, email, role, status akun. Fitur pencarian berdasarkan nama atau email. Tombol tambah karyawan baru yang membuka modal atau halaman form. Tombol edit dan hapus pada setiap baris karyawan dengan icon yang jelas. Modal atau form untuk input data karyawan mencakup: nama lengkap, email, password (untuk user baru), role (Administrator/Resepsionis/Karyawan), gaji pokok, dan upload foto profil. Validasi input dengan pesan error yang jelas untuk field yang tidak valid. Konfirmasi dialog sebelum menghapus data karyawan untuk mencegah penghapusan tidak sengaja.

**Halaman Laporan Absensi**

Filter berdasarkan rentang tanggal dengan date picker yang user-friendly. Fitur pencarian berdasarkan nama karyawan untuk mempercepat pencarian data. Tombol export laporan ke Excel dengan icon download yang jelas. Tabel laporan dengan kolom: tanggal, nama karyawan, waktu masuk, waktu pulang, status keterlambatan, dan keterangan. Badge berwarna untuk membedakan status: Hadir (hijau), Terlambat Level 1-3 (kuning/orange/merah), Setengah Hari (orange), Sakit (biru), Izin (ungu), Cuti (cyan), Alpha (merah), Dinas Luar Kota (abu-abu). Pagination untuk navigasi data yang banyak. Tombol edit untuk mengubah status kehadiran manual dan menambahkan catatan khusus.

**Halaman Pengaturan Perusahaan**

Formulir untuk mengatur parameter perusahaan dengan field: nama perusahaan, alamat lengkap, email kontak, latitude dan longitude kantor dengan bantuan map picker interaktif, radius lokasi kantor dalam kilometer. Konfigurasi jam kerja: jam masuk standar dan jam pulang standar. Konfigurasi threshold keterlambatan bertingkat: Terlambat 1 (waktu awal), Terlambat 2 (waktu menengah), Terlambat 3 (waktu batas), dan Setengah Hari (threshold terakhir). Konfigurasi persentase potongan gaji untuk setiap level keterlambatan. Tombol simpan perubahan dengan konfirmasi dialog. Validasi input untuk memastikan format waktu valid, angka positif, dan koordinat GPS dalam range yang benar.

**Halaman Riwayat Kehadiran Personal**

Tampilan kalender bulanan yang menampilkan status kehadiran administrator sendiri setiap hari. Setiap tanggal pada kalender diberi warna sesuai status kehadiran. Klik pada tanggal tertentu akan menampilkan detail lengkap: waktu masuk, waktu pulang, status keterlambatan, dan keterangan jika ada. Navigasi untuk berpindah ke bulan sebelumnya dan bulan berikutnya. Legenda warna status kehadiran ditampilkan di bagian bawah atau samping kalender untuk referensi.

### 3.1.3 Antarmuka Resepsionis

**Halaman Beranda (Dashboard Resepsionis)**

Tampilan sederhana dengan fokus pada fungsi utama resepsionis. Tombol akses cepat besar untuk "Tampilkan QR Code" yang langsung membuka halaman generate QR Code. Widget preview daftar kehadiran hari ini dengan informasi singkat jumlah karyawan yang sudah check-in. Kalender personal untuk melihat riwayat kehadiran resepsionis sendiri. Informasi jam kerja dan status absensi resepsionis hari ini.

**Halaman Generate QR Code dan Monitoring**

Menampilkan QR Code harian dalam ukuran besar yang mudah dipindai dari jarak jauh, cocok untuk ditampilkan di monitor resepsionis. Informasi token QR Code dan tanggal berlaku ditampilkan di bawah QR Code. Daftar real-time karyawan yang sudah melakukan absensi hari ini dengan kolom: nomor urut, foto profil, nama lengkap, waktu masuk, status keterlambatan (tepat waktu, terlambat 1/2/3, setengah hari), dan kolom catatan. Tombol kecil pada setiap baris untuk menambahkan atau mengedit catatan pada record absensi. Tombol refresh manual untuk memperbarui daftar kehadiran. Fitur auto-refresh otomatis setiap beberapa detik untuk monitoring real-time tanpa perlu refresh manual. Indikator jumlah total karyawan yang sudah absen vs total karyawan.

**Halaman Laporan Absensi**

Filter berdasarkan rentang tanggal untuk melihat data absensi periode tertentu. Fitur pencarian berdasarkan nama karyawan. Tabel laporan dengan kolom: tanggal, nama karyawan, waktu masuk, waktu pulang, status, dan keterangan. Badge berwarna untuk status kehadiran yang mudah dibedakan. Tombol untuk menambahkan catatan pada data absensi karyawan. Tidak memiliki akses hapus atau export Excel (khusus admin).

**Halaman Riwayat Kehadiran Personal**

Tampilan kalender bulanan untuk melihat riwayat kehadiran resepsionis sendiri. Setiap tanggal diberi warna sesuai status kehadiran. Detail kehadiran muncul saat klik tanggal tertentu. Navigasi bulan dan legenda warna status.

### 3.1.4 Antarmuka Karyawan

**Halaman Beranda (Dashboard Karyawan)**

Tampilan sederhana dan user-friendly untuk karyawan. Informasi jam kerja hari ini dan jadwal kerja. Status absensi hari ini: sudah check-in atau belum, waktu check-in jika sudah. Tombol akses cepat besar "Scan QR Code" untuk langsung membuka halaman absensi. Tombol "Check Out" jika sudah melakukan check-in. Widget kalender kecil yang menampilkan riwayat kehadiran bulan ini. Informasi singkat: total kehadiran bulan ini, total keterlambatan, dan status terkini.

**Halaman Scan QR Code**

Interface kamera full-screen dengan preview video real-time untuk scan QR Code. Area frame berbentuk kotak di tengah layar untuk memposisikan QR Code dengan tepat saat scan. Tombol besar "Izinkan Akses Kamera" jika browser belum memberikan permission. Indikator status GPS yang menampilkan apakah GPS aktif atau tidak aktif. Informasi jarak real-time dari lokasi kantor jika GPS aktif. Loading indicator saat memproses scan QR Code. Pesan notifikasi pop-up atau alert untuk hasil scan: sukses check-in, sukses check-out, atau error dengan keterangan jelas. Tombol alternatif "Check Out" yang muncul jika karyawan sudah check-in dan ingin langsung check-out tanpa scan QR lagi. Validasi otomatis untuk QR Code (token dan tanggal valid) dan lokasi GPS (dalam radius kantor).

**Halaman Riwayat Kehadiran**

Tampilan kalender bulanan yang menampilkan status kehadiran karyawan setiap hari dalam format visual yang menarik. Setiap tanggal pada kalender diberi warna berbeda sesuai status: hijau untuk tepat waktu, kuning untuk terlambat, orange untuk setengah hari, merah untuk alpha/cuti/sakit/izin, biru untuk izin khusus. Klik pada tanggal tertentu akan menampilkan modal atau panel detail yang berisi: tanggal lengkap, waktu check-in, waktu check-out, status keterlambatan detail (jika terlambat, level berapa), keterangan atau catatan jika ada. Navigasi arrow untuk berpindah ke bulan sebelumnya dan bulan berikutnya. Legenda warna status kehadiran ditampilkan di bagian bawah kalender dengan keterangan jelas untuk setiap warna.

### 3.1.5 Standar Antarmuka Keseluruhan

**Konsistensi Tombol dan Warna**

Tombol aksi utama seperti Simpan, Hapus, Export, Scan menggunakan warna yang kontras dan konsisten di seluruh aplikasi untuk memudahkan identifikasi. Tombol primer (aksi utama) menggunakan warna biru atau hijau. Tombol destructive (hapus, logout) menggunakan warna merah. Tombol sekunder (batal, kembali) menggunakan warna abu-abu atau outline.

**Notifikasi dan Pesan Error**

Setiap error ditampilkan dengan notifikasi atau toast alert singkat dengan durasi beberapa detik. Contoh pesan error: "QR Code tidak valid atau sudah kadaluarsa", "Anda berada di luar radius kantor", "Data tidak boleh kosong", "Email sudah terdaftar". Notifikasi sukses ditampilkan setelah aksi berhasil dilakukan dengan warna hijau dan icon checkmark. Contoh pesan sukses: "Absen Masuk Berhasil", "Data berhasil disimpan", "Laporan berhasil diexport".

**Desain Responsif**

Desain mengikuti prinsip responsive layout menggunakan Tailwind CSS agar dapat diakses dengan baik dari desktop hingga smartphone. Pada layar besar (desktop), sidebar menu ditampilkan secara permanen. Pada layar kecil (mobile), sidebar menu dapat ditampilkan/disembunyikan dengan tombol hamburger menu. Tabel pada mobile ditampilkan dengan scroll horizontal atau diubah menjadi card view untuk kemudahan membaca.

**Loading dan Konfirmasi**

Loading indicator (spinner atau skeleton) ditampilkan saat proses yang membutuhkan waktu seperti upload foto, generate laporan Excel, atau fetch data dari server. Konfirmasi dialog muncul untuk aksi krusial yang tidak bisa di-undo seperti hapus data karyawan, hapus absensi bulanan, atau logout dari sistem. Dialog konfirmasi memiliki tombol "Ya, Lanjutkan" dan "Batal" dengan warna yang jelas membedakan keduanya.

**Aksesibilitas**

Icon selalu disertai dengan label text untuk clarity. Field input memiliki label yang jelas dan placeholder yang deskriptif. Tab navigation dapat dilakukan dengan keyboard untuk aksesibilitas. Focus state pada elemen interaktif ditampilkan dengan jelas saat navigasi dengan keyboard.

## 3.2 Hardware Interfaces

Sistem Informasi Presensi Karyawan beroperasi melalui peramban web sehingga tidak membutuhkan perangkat keras khusus selain perangkat standar yang dapat menjalankan browser modern dengan dukungan kamera dan GPS. Adapun karakteristik antarmuka perangkat keras yang didukung adalah sebagai berikut.

**Perangkat yang Didukung**

Laptop, komputer desktop, tablet, dan smartphone dengan spesifikasi minimal RAM 4 GB dan prosesor dual-core untuk memastikan performa optimal saat menjalankan fitur scan QR Code yang membutuhkan akses kamera real-time. Perangkat mobile direkomendasikan memiliki minimal Android 9.0 (Pie) atau iOS 13 ke atas untuk kompatibilitas penuh dengan fitur GPS dan kamera.

**Perangkat Input**

Semua interaksi pengguna dilakukan melalui perangkat input standar seperti keyboard, mouse, dan touchscreen untuk navigasi aplikasi. Khusus untuk fitur scan QR Code, perangkat wajib dilengkapi dengan kamera yang mampu mengambil gambar dengan resolusi minimal 720p untuk memastikan QR Code dapat terbaca dengan jelas. Kamera harus mendukung autofocus untuk hasil scan yang lebih akurat.

**Sensor Lokasi (GPS)**

Perangkat harus dilengkapi dengan GPS atau sensor lokasi yang aktif dan akurat untuk fitur validasi kehadiran berbasis radius kantor. Pada perangkat mobile, GPS built-in sudah mencukupi. Pada perangkat desktop atau laptop yang tidak memiliki GPS internal, sistem akan menggunakan geolocation berbasis IP atau WiFi positioning dengan tingkat akurasi yang lebih rendah. Akurasi GPS minimal 10 meter diperlukan untuk validasi radius yang tepat.

**Koneksi Internet**

Perangkat harus terhubung ke internet dengan koneksi yang stabil untuk mengakses sistem secara real-time, mengirim data absensi ke server, serta mengunduh QR Code harian. Bandwidth minimal yang disarankan adalah 2 Mbps untuk pengalaman pengguna yang lancar, terutama saat mengakses fitur kamera dan upload foto profil. Koneksi dapat menggunakan WiFi, mobile data (3G/4G/5G), atau jaringan kabel.

**Protokol Komunikasi**

Sistem memanfaatkan protokol HTTPS untuk semua pertukaran data antara client dan server guna menjamin keamanan data selama proses transmisi, terutama untuk data sensitif seperti koordinat GPS, informasi kehadiran, dan kredensial login. WebSocket atau long polling dapat digunakan untuk fitur real-time monitoring kehadiran pada halaman resepsionis. Komunikasi dengan browser API seperti Geolocation API dan Media Devices API menggunakan protokol standar browser yang aman.

**Karakteristik Display**

Sistem dirancang responsif sehingga tampilan dan fungsi dapat berjalan dengan baik pada berbagai ukuran layar mulai dari smartphone dengan layar 5 inci (resolusi minimal 720 x 1280 pixels) hingga monitor desktop dengan layar 24 inci atau lebih besar (resolusi hingga 1920 x 1080 pixels atau 4K). Layout halaman menyesuaikan secara otomatis menggunakan breakpoint Tailwind CSS untuk memastikan elemen tetap terbaca dan dapat diakses dengan mudah di semua ukuran layar.

**Izin Akses Perangkat**

Browser harus mendapatkan izin eksplisit dari pengguna untuk mengakses kamera (Camera API) dan lokasi GPS (Geolocation API) sesuai dengan kebijakan keamanan browser modern. Sistem akan menampilkan prompt izin saat pertama kali pengguna mengakses fitur scan QR Code atau saat melakukan absensi. Jika izin ditolak, sistem akan menampilkan pesan error yang menjelaskan cara memberikan izin akses melalui pengaturan browser.

**Kompatibilitas Browser**

Sistem mendukung browser modern seperti Google Chrome 90+, Mozilla Firefox 88+, Microsoft Edge 90+, dan Safari 14+ yang memiliki dukungan penuh terhadap HTML5, CSS3, JavaScript ES6+, Media Devices API untuk akses kamera, dan Geolocation API untuk akses GPS. Browser versi lama yang tidak mendukung fitur-fitur tersebut tidak akan dapat menjalankan sistem dengan optimal.
