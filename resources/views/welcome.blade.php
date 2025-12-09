<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Hero Section -->
    <div class="gradient-bg min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl w-full">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="flex justify-center mb-6">
                    <div class="bg-white p-4 rounded-full shadow-lg">
                        <svg class="w-16 h-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-4">
                    Sistem Absensi Sekolah
                </h1>
                <p class="text-xl sm:text-2xl text-purple-100 mb-2">
                    Kelola Kehadiran dengan Mudah & Realtime
                </p>
                <p class="text-lg text-purple-200">
                    Scan QR Code • Dashboard Realtime • Laporan Otomatis
                </p>
            </div>

            <!-- Login Cards -->
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Admin/Guru Login Card -->
                <div class="bg-white rounded-2xl shadow-2xl p-8 card-hover">
                    <div class="text-center">
                        <div class="bg-amber-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-3">
                            Admin & Guru
                        </h2>
                        <p class="text-gray-600 mb-6">
                            Kelola data sekolah, input absensi, dan lihat laporan lengkap
                        </p>
                        <ul class="text-left text-sm text-gray-600 mb-8 space-y-2">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Manajemen Kelas & Murid
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Input Absensi & QR Code
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Laporan & Statistik
                            </li>
                        </ul>
                        <a href="/admin" class="block w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-4 px-6 rounded-xl transition duration-300 shadow-lg hover:shadow-xl">
                            Login Admin/Guru
                        </a>
                    </div>
                </div>

                <!-- Murid Login Card -->
                <div class="bg-white rounded-2xl shadow-2xl p-8 card-hover">
                    <div class="text-center">
                        <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-3">
                            Portal Murid
                        </h2>
                        <p class="text-gray-600 mb-6">
                            Scan QR untuk absen, ajukan izin, dan lihat riwayat kehadiran
                        </p>
                        <ul class="text-left text-sm text-gray-600 mb-8 space-y-2">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Scan QR Code Absensi
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Ajukan Izin/Sakit
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Riwayat Kehadiran
                            </li>
                        </ul>
                        <a href="/student" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition duration-300 shadow-lg hover:shadow-xl">
                            Login Murid
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 text-white">
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 rounded-lg p-6 backdrop-blur-sm">
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-semibold text-lg mb-2">Realtime</h3>
                        <p class="text-sm text-purple-100">Update otomatis tanpa refresh</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 rounded-lg p-6 backdrop-blur-sm">
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="font-semibold text-lg mb-2">Mobile Friendly</h3>
                        <p class="text-sm text-purple-100">Akses dari HP kapan saja</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="bg-white bg-opacity-20 rounded-lg p-6 backdrop-blur-sm">
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <h3 class="font-semibold text-lg mb-2">Aman</h3>
                        <p class="text-sm text-purple-100">Data terenkripsi & terlindungi</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-12 text-center text-white text-sm">
                <p class="opacity-75">© 2024 Sistem Absensi Sekolah. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
