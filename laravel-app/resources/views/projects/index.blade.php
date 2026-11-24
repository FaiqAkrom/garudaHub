<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Garuda-Hub - Lelang Proyek IT</title>
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('projects.index') }}" class="flex items-center space-x-2">
                <span class="text-2xl font-bold text-indigo-600">Garuda<span class="text-gray-900">Hub</span></span>
            </a>
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-sm text-gray-600 hidden sm:inline">
                        {{ Auth::user()->name }} 
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold bg-indigo-100 text-indigo-800 uppercase">{{ Auth::user()->role }}</span>
                    </span>
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Masuk</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Daftar Proyek Tersedia</h1>
                <p class="text-gray-600 text-sm mt-1">Jelajahi dan ajukan penawaran untuk proyek IT terbaik</p>
            </div>
            @if(Auth::check() && Auth::user()->role === 'client')
                <a href="{{ route('projects.create') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm transition">
                    + Pasang Proyek Baru
                </a>
            @endif
        </div>

        @if($projects->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-500 text-base">Belum ada proyek yang dipublikasikan saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition flex flex-col justify-between p-6">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded bg-blue-50 text-blue-700">
                                    {{ $project->category }}
                                </span>
                                @if($project->status === 'open')
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-800">Buka</span>
                                @elseif($project->status === 'awarded')
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">Dimenangkan</span>
                                @else
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-800">Tutup</span>
                                @endif
                            </div>
                            <h2 class="text-lg font-bold text-gray-900 mb-2 line-clamp-1">
                                <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">
                                    {{ $project->title }}
                                </a>
                            </h2>
                            <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                                {{ $project->description }}
                            </p>
                        </div>
                        <div class="pt-4 border-t border-gray-100 mt-2">
                            <div class="flex justify-between items-center text-sm mb-3">
                                <span class="text-gray-500">Anggaran:</span>
                                <span class="font-bold text-gray-900">Rp {{ number_format($project->budget, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs text-gray-500 mb-4">
                                <span>Batas: {{ $project->deadline->format('d M Y') }}</span>
                                <span>{{ $project->bids_count }} Penawaran</span>
                            </div>
                            <a href="{{ route('projects.show', $project) }}" class="block text-center w-full bg-gray-50 hover:bg-indigo-50 hover:text-indigo-600 text-gray-700 font-medium py-2 rounded-lg text-sm border border-gray-200 transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-12 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} Garuda-Hub. Hak cipta dilindungi.
    </footer>
</body>
</html>
