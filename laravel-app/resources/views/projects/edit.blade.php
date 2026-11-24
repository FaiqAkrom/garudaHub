<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Proyek - Garuda-Hub</title>
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('projects.index') }}" class="text-2xl font-bold text-indigo-600">Garuda<span class="text-gray-900">Hub</span></a>
            <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600">← Kembali ke Detail</a>
        </div>
    </header>

    <main class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Proyek</h1>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Proyek</label>
                    <input type="text" name="title" value="{{ old('title', $project->title) }}" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(['Web', 'Mobile', 'Data', 'Desain', 'Lainnya'] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $project->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Anggaran (Rp)</label>
                        <input type="number" name="budget" value="{{ old('budget', $project->budget) }}" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="open" {{ old('status', $project->status) === 'open' ? 'selected' : '' }}>Open (Buka)</option>
                            <option value="closed" {{ old('status', $project->status) === 'closed' ? 'selected' : '' }}>Closed (Tutup)</option>
                            <option value="awarded" {{ old('status', $project->status) === 'awarded' ? 'selected' : '' }}>Awarded (Selesai)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Waktu Penawaran</label>
                    <input type="datetime-local" name="deadline" value="{{ old('deadline', $project->deadline->format('Y-m-d\TH:i')) }}" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Lengkap Kebutuhan</label>
                    <textarea name="description" rows="5" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $project->description) }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('projects.show', $project) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
