@extends('dashboardStaff.layouts.app')

@section('title', 'Add Data Services')

@section('content')

<div class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Add New Data</h2>
        <a href="{{ route('service.index') }}">
            <button class="flex items-center gap-2 px-4 py-2 border border-green-500 text-green-600 rounded-md hover:bg-green-50">
                Back to Dashboard
            </button>
        </a>
    </div>

    <form id="form-id" method="POST" action="{{ route('service.store') }}" enctype="multipart/form-data"
    class="bg-white p-6 rounded-xl shadow-md space-y-8">
        @csrf
        <!-- Input Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Service Name -->
            <div>
                <label for="service_name" class="block text-sm font-medium text-gray-700 mb-1">Service Name <span class="text-red-500">*</span></label>
                <input type="text" id="service_name" name="service_name" value="{{ old('service_name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Carbon Tracker" />
                @error('service_name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Duration -->
            <div>
                <label for="service_duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (Days) <span class="text-red-500">*</span></label>
                <input type="text" id="service_duration" name="service_duration" value="{{ old('service_duration') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="30 Days" />
                @error('service_duration')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div>
                <label for="service_price" class="block text-sm font-medium text-gray-700 mb-1">Price (IDR) <span class="text-red-500">*</span></label>
                <input type="number" id="service_price" name="service_price" value="{{ old('service_price') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="500000 (IDR)" />
                @error('service_price')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description with Tag Input -->
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label for="service_description_input" class="block text-sm font-medium text-gray-700 mb-1">
                    Service Features / Description <span class="text-red-500">*</span>
                </label>

                <!-- Tag Container -->
                <div id="tag-container"
                    class="flex flex-wrap items-center gap-2 p-2 border border-gray-300 rounded-md shadow-sm min-h-[48px] focus-within:ring-2 focus-within:ring-green-500 focus-within:border-green-500 transition-all">
                    <input type="text" id="service_description_input"
                        class="flex-1 bg-transparent border-none outline-none text-sm focus:ring-0"
                        placeholder="Press Enter to add feature..." />
                </div>

                <!-- Hidden Input -->
                <input type="hidden" name="service_description" id="service_description_hidden" />

                @error('service_description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Success Notification -->
        @if (session('success'))
        <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('perjalananKaryawanPerusahaan.index') }}">
                <button type="button"
                    class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    Cancel
                </button>
            </a>
            <button type="submit"
                class="px-4 py-2 bg-[#39AA80] text-white text-sm rounded-md hover:bg-[#2F7B63] shadow-sm">
                Add Service
            </button>
        </div>
    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('service_description_input');
        const container = document.getElementById('tag-container');
        const hiddenInput = document.getElementById('service_description_hidden');
        let tags = [];

        function updateHiddenInput() {
            hiddenInput.value = JSON.stringify(tags);
        }

        function createTagElement(tagText) {
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center bg-green-100 text-green-800 text-sm font-medium px-2 py-1 rounded-full';
            tag.textContent = tagText;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'ml-1 text-red-500 hover:text-red-700 text-sm';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = () => {
                tags = tags.filter(t => t !== tagText);
                container.removeChild(tag);
                updateHiddenInput();
            };

            tag.appendChild(removeBtn);
            return tag;
        }

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && input.value.trim() !== '') {
                e.preventDefault();
                const tagText = input.value.trim();
                if (!tags.includes(tagText)) {
                    tags.push(tagText);
                    container.insertBefore(createTagElement(tagText), input);
                    updateHiddenInput();
                }
                input.value = '';
            }
        });
    });
</script>


@endsection
