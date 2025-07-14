@extends('dashboardStaff.layouts.app')

@section('title', 'Update Information')

@section('content')

<div class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Update Data</h2>
        <a href="{{ route('informasi.index') }}">
            <button class="flex items-center gap-2 px-4 py-2 border border-green-500 text-green-600 rounded-md hover:bg-green-50">
                Back to Dashboard
            </button>
        </a>
    </div>

    <form id="form-id" method="POST" action="{{ route('informasi.update', ['id' => $oldData->id]) }}" enctype="multipart/form-data"
        class="bg-white p-6 rounded-xl shadow-md space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Title Input -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div>
                <label for="information_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Information Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="information_name" name="information_name" value="{{ old('information_name', $oldData->judul_informasi) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Renewable Energy Policy Update" />
                @error('information_name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Content & Image Upload - Balanced Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Content -->
            <div class="flex flex-col">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                    Content <span class="text-red-500">*</span>
                </label>
                <textarea id="content" name="content" required
                    class="w-full flex-1 h-80 px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none text-sm"
                    placeholder="Write the detailed content here...">{{ old('content', $oldData->isi_informasi) }}</textarea>
                @error('content')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Image -->
            <div x-data="{ isDragging: false, imageUrl: null, fileName: '' }" class="flex flex-col">
                <label for="gambar_informasi" class="block text-sm font-medium text-gray-700 mb-1">
                    Upload Image <span class="text-red-500">*</span>
                </label>

                <div @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="isDragging = false"
                    :class="{ 'border-green-500 ring-2 ring-green-200': isDragging }"
                    class="w-full flex-1 h-80 px-4 py-6 border-2 border-dashed border-gray-300 rounded-md shadow-sm bg-white text-center flex flex-col items-center justify-center gap-2 transition relative overflow-hidden">

                    <!-- Current Image Preview (when no new file selected) -->
                    <template x-if="!fileName">
                        <div class="flex flex-col items-center justify-center gap-2 h-full">
                            <div class="flex-1 flex items-center justify-center max-h-48">
                                <img src="{{ asset('informasi_images/'.$oldData->gambar_informasi) }}" 
                                     alt="Current Image" 
                                     class="max-w-full max-h-full object-contain rounded-md border border-gray-300" />
                            </div>
                            <div class="flex-shrink-0">
                                <p class="text-sm text-gray-600 mb-2">Current image - Drag & drop to replace or click to select new</p>
                                <button type="button"
                                    @click="$refs.fileInput.click()"
                                    class="px-4 py-2 bg-[#39AA80] text-white rounded-md hover:bg-[#2F7B63] transition text-sm shadow-sm">
                                    Choose New File
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- New File Selected -->
                    <template x-if="fileName">
                        <div class="flex flex-col items-center justify-center gap-2 h-full">
                            <div class="flex-1 flex items-center justify-center">
                                <p class="text-sm text-green-600 font-medium">Selected file: <span x-text="fileName"></span></p>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="button"
                                    @click="$refs.fileInput.click()"
                                    class="px-4 py-2 bg-[#39AA80] text-white rounded-md hover:bg-[#2F7B63] transition text-sm shadow-sm">
                                    Change File
                                </button>
                            </div>
                        </div>
                    </template>

                    <input type="file" accept="image/*" id="gambar_informasi" name="gambar_informasi"
                        class="hidden"
                        @change="
                            let file = $event.target.files[0];
                            if (file) {
                                imageUrl = URL.createObjectURL(file);
                                fileName = file.name;
                            } else {
                                imageUrl = null;
                                fileName = '';
                            }
                        "
                        x-ref="fileInput">
                </div>

                @error('gambar_informasi')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('informasi.index') }}">
                <button type="button"
                    class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 shadow-sm">
                    Cancel
                </button>
            </a>
            <button type="submit" form="form-id"
                class="px-4 py-2 bg-[#39AA80] text-white text-sm rounded-md hover:bg-[#2F7B63] shadow-sm">
                Update Information
            </button>
        </div>
    </form>

</div>

@endsection