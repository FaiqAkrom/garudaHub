<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $project->title }} - Garuda-Hub</title>
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('projects.index') }}" class="text-2xl font-bold text-indigo-600">Garuda<span class="text-gray-900">Hub</span></a>
            <div class="flex items-center space-x-4">
                <a href="{{ route('projects.index') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600">← Semua Proyek</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Project Detail Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
                <div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-blue-50 text-blue-700 uppercase">
                        {{ $project->category }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ $project->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">Dipasang oleh <span class="font-medium text-gray-800">{{ $project->client->name }}</span> • {{ $project->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    @if($project->status === 'open')
                        <span class="text-sm font-semibold px-3 py-1 rounded-full bg-green-100 text-green-800">Status: Buka</span>
                    @elseif($project->status === 'awarded')
                        <span class="text-sm font-semibold px-3 py-1 rounded-full bg-indigo-100 text-indigo-800">Status: Dimenangkan</span>
                    @else
                        <span class="text-sm font-semibold px-3 py-1 rounded-full bg-gray-100 text-gray-800">Status: Tutup</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-4 my-4 border-y border-gray-100 bg-gray-50 rounded-lg px-4">
                <div>
                    <span class="text-xs text-gray-500 block">Anggaran Maksimal</span>
                    <span class="text-lg font-bold text-gray-900">Rp {{ number_format($project->budget, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Batas Waktu Penawaran</span>
                    <span class="text-lg font-bold text-gray-900">{{ $project->deadline->format('d F Y, H:i') }}</span>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-base font-semibold text-gray-900 mb-2">Deskripsi Proyek</h2>
                <div class="text-gray-700 leading-relaxed whitespace-pre-line text-sm bg-white">
                    {{ $project->description }}
                </div>
            </div>

            @if(Auth::check() && Auth::id() === $project->client_id)
                <div class="mt-6 pt-4 border-t border-gray-100 flex gap-3">
                    <a href="{{ route('projects.edit', $project) }}" class="text-sm px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-medium transition">Edit Proyek</a>
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Yakin ingin menghapus proyek ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-medium transition">Hapus Proyek</button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Bidding Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Daftar Penawaran ({{ $project->bids->count() }})</h2>

            @auth
                @if(Auth::user()->role === 'developer' && $project->status === 'open')
                    @php
                        $userBid = $project->bids->firstWhere('developer_id', Auth::id());
                    @endphp

                    @if(!$userBid)
                        <div class="mb-8 p-6 bg-indigo-50 border border-indigo-100 rounded-xl">
                            <h3 class="text-base font-semibold text-indigo-900 mb-3">Ajukan Penawaran Anda</h3>
                            <form method="POST" action="{{ route('bids.store', $project) }}" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Harga Tawaran (Rp)</label>
                                        <input type="number" name="price" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 4500000" max="{{ $project->budget }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Durasi Pengerjaan (Hari)</label>
                                        <input type="number" name="duration_days" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 14">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Proposal & Pendekatan</label>
                                    <textarea name="proposal" rows="3" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jelaskan pengalaman dan bagaimana Anda menyelesaikan proyek ini..."></textarea>
                                </div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg text-sm shadow transition">
                                    Kirim Penawaran
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-900 flex justify-between items-center">
                            <span>Anda sudah mengajukan tawaran sebesar <strong>Rp {{ number_format($userBid->price, 0, ',', '.') }}</strong> ({{ $userBid->duration_days }} hari).</span>
                            <form method="POST" action="{{ route('bids.destroy', $userBid) }}" onsubmit="return confirm('Batalkan penawaran ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline">Batalkan</button>
                            </form>
                        </div>
                    @endif
                @endif
            @else
                <p class="text-sm text-gray-500 mb-6 bg-gray-50 p-4 rounded-lg">
                    Silakan <a href="{{ route('login') }}" class="text-indigo-600 font-semibold underline">masuk</a> sebagai developer untuk mengajukan penawaran.
                </p>
            @endauth

            <!-- Bids List -->
            @if($project->bids->isEmpty())
                <p class="text-gray-500 text-sm text-center py-6">Belum ada penawaran untuk proyek ini.</p>
            @else
                <div class="space-y-4">
                    @foreach($project->bids as $bid)
                        <div class="border border-gray-200 rounded-lg p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 {{ $bid->status === 'won' ? 'bg-green-50 border-green-300' : 'bg-white' }}">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-bold text-gray-900">{{ $bid->developer->name }}</span>
                                    @if($bid->status === 'won')
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-green-200 text-green-900">Pemenang</span>
                                    @elseif($bid->status === 'rejected')
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">Ditolak</span>
                                    @else
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-700 mt-2">{{ $bid->proposal }}</p>
                                <div class="flex gap-4 text-xs text-gray-500 mt-3">
                                    <span>Tawaran: <strong class="text-gray-900">Rp {{ number_format($bid->price, 0, ',', '.') }}</strong></span>
                                    <span>Durasi: <strong class="text-gray-900">{{ $bid->duration_days }} hari</strong></span>
                                    <span>{{ $bid->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            @if(Auth::check() && Auth::id() === $project->client_id && $project->status === 'open')
                                <form method="POST" action="{{ route('bids.accept', $bid) }}" onsubmit="return confirm('Pilih {{ $bid->developer->name }} sebagai pemenang? Proyek akan ditutup.');">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg text-sm shadow transition">
                                        Pilih Pemenang
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</body>
</html>
