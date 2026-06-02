<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen Makan - Selamat Datang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-6 relative">

    <!-- Tombol Login Admin (Pojok Kanan Atas) -->
    <div class="absolute top-6 right-6">
        <a href="/admin"
            class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl shadow-sm hover:bg-gray-50 hover:text-blue-600 transition-all font-medium text-sm">
            <i class="fa-solid fa-lock text-xs"></i>
            Login Admin
        </a>
    </div>

    <div class="max-w-4xl w-full">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Sistem Absensi Makan</h1>
            <p class="text-gray-600">Silakan pilih kategori kehadiran Anda hari ini</p>
        </div>

        <!-- Pilihan Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Karyawan -->
            <a href="{{ route('absen.karyawan') }}"
                class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-8 text-center border-b-4 border-blue-500 hover:-translate-y-2">
                <div
                    class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-user-tie text-3xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Karyawan</h2>
                <p class="text-gray-500 text-sm">Absensi rutin menggunakan NIK Karyawan</p>
            </a>

            <!-- Magang -->
            <a href="/absen/magang"
                class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-8 text-center border-b-4 border-emerald-500 hover:-translate-y-2">
                <div
                    class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-user-graduate text-3xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Magang / PKL</h2>
                <p class="text-gray-500 text-sm">Absensi khusus untuk siswa/mahasiswa magang / PKL</p>
            </a>

            <!-- Tamu -->
            <a href="/absen/tamu"
                class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-8 text-center border-b-4 border-amber-500 hover:-translate-y-2">
                <div
                    class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-id-badge text-3xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Tamu</h2>
                <p class="text-gray-500 text-sm">Registrasi makan untuk tamu kunjungan</p>
            </a>

        </div>

        <!-- Footer -->
        <div class="mt-16 text-center text-gray-400 text-xs">
            &copy; 2026 PT Banpu Indo - Meal Attendance System
        </div>
    </div>

</body>

</html>