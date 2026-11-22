<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }} - <span class="capitalize text-indigo-600">{{ Auth::user()->role }}</span>
            </h2>
            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                ← Lihat Semua Proyek
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Role Summary Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Selamat datang, {{ Auth::user()->name }}!</h3>
                        <p class="text-sm text-gray-500 mt-1">Anda masuk sebagai <strong class="uppercase text-indigo-600">{{ Auth::user()->role }}</strong> ({{ Auth::user()->email }}).</p>
                    </div>
                    @if(Auth::user()->role === 'client')
                        <a href="{{ route('projects.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            + Pasang Proyek Baru
                        </a>
                    @endif
                </div>
            </div>

            <!-- Client Section: My Projects -->
            @if(Auth::user()->role === 'client' || Auth::user()->role === 'admin')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        {{ Auth::user()->role === 'client' ? 'Daftar Proyek Saya' : 'Semua Proyek Sistem (Admin)' }}
                    </h3>

                    @if($projects->isEmpty())
                        <p class="text-sm text-gray-500 py-4 text-center">Belum ada proyek yang Anda buat.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-700">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                                    <tr>
                                        <th class="py-3 px-4">Judul Proyek</th>
                                        <th class="py-3 px-4">Kategori</th>
                                        <th class="py-3 px-4">Anggaran</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4">Penawaran</th>
                                        <th class="py-3 px-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($projects as $proj)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4 font-semibold text-gray-900">{{ $proj->title }}</td>
                                            <td class="py-3 px-4">{{ $proj->category }}</td>
                                            <td class="py-3 px-4">Rp {{ number_format($proj->budget, 0, ',', '.') }}</td>
                                            <td class="py-3 px-4">
                                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $proj->status === 'open' ? 'bg-green-100 text-green-800' : ($proj->status === 'awarded' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($proj->status) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">{{ $proj->bids_count ?? $proj->bids->count() }} Tawaran</td>
                                            <td class="py-3 px-4 text-right space-x-2">
                                                <a href="{{ route('projects.show', $proj) }}" class="text-indigo-600 hover:underline">Detail</a>
                                                @if(Auth::id() === $proj->client_id)
                                                    <a href="{{ route('projects.edit', $proj) }}" class="text-gray-600 hover:underline">Edit</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Developer Section: My Bids -->
            @if(Auth::user()->role === 'developer')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Penawaran Saya</h3>

                    @if($bids->isEmpty())
                        <p class="text-sm text-gray-500 py-4 text-center">Anda belum mengajukan penawaran pada proyek manapun.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-700">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                                    <tr>
                                        <th class="py-3 px-4">Proyek</th>
                                        <th class="py-3 px-4">Harga Tawaran</th>
                                        <th class="py-3 px-4">Durasi</th>
                                        <th class="py-3 px-4">Status Tawaran</th>
                                        <th class="py-3 px-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($bids as $b)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4 font-semibold text-gray-900">{{ $b->project->title }}</td>
                                            <td class="py-3 px-4">Rp {{ number_format($b->price, 0, ',', '.') }}</td>
                                            <td class="py-3 px-4">{{ $b->duration_days }} Hari</td>
                                            <td class="py-3 px-4">
                                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $b->status === 'won' ? 'bg-green-100 text-green-800' : ($b->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($b->status) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <a href="{{ route('projects.show', $b->project_id) }}" class="text-indigo-600 hover:underline">Lihat Proyek</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
