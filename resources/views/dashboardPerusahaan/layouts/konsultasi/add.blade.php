@extends('dashboardPerusahaan.layouts.app')

@section('title', 'Consultations')

@section('content')

<div 
    class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6"
    x-data="{ 
        showModal: false,
        selectedRow: {},
        selectedId: null,
        confirmDelete: false,
        pdfModal: false,
        showDiscussionModal: false,
        discussionName: '',
        discussionMessage: '',
        confirmSendConsultation: false
    }"

>
    @if (session('success'))
    <div class="mb-4 p-4 rounded-md border border-green-300 bg-green-50 text-green-800 shadow-sm flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Consultations</h2>
        <div class="relative group">
            <button 
                @click="selectedId ? showDiscussionModal = true : null" 
                :disabled="!selectedId"
                class="flex items-center gap-2 px-4 py-2 bg-[#39AA80] text-white rounded-md border border-[#39AA80]
                    disabled:bg-gray-300 disabled:border-gray-300 disabled:cursor-not-allowed"
            >
                Open Discussion
            </button>

            <!-- Tooltip jika belum memilih -->
            <div 
                x-show="!selectedId"
                class="absolute left-1/2 -translate-x-1/2 mt-2 text-xs text-white bg-[#39AA80] rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
            >
                Select a data first
            </div>
        </div>

    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Select</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Analysis Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Analysis Date</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(row, index) in filteredData" :key="index">
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700 text-center">
                            <input 
                                type="radio"
                                name="selected"
                                :value="row.id"
                                x-model="selectedId"
                                class="form-radio h-4 w-4 text-green-600 border-gray-300"
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="row.no"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.nama_analisis"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.tanggal_analisis"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <div class="flex justify-center gap-2">
                                <button 
                                    @click="selectedRow = row; showModal = true; confirmDelete = false"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded"
                                >
                                    Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{ $data->links('vendor.pagination.custom') }}

    <!-- Modal Detail - Adjusted Size -->
    <div 
        x-show="showModal && !pdfModal" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
        style="display: none;"
    >
        <div 
            @click.away="showModal = false; confirmDelete = false"
            class="bg-white w-full max-w-2xl rounded-2xl shadow-xl relative"
        >
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800">Analysis Detail</h3>
                <button @click="showModal = false; confirmDelete = false" class="text-gray-500 hover:text-gray-700 text-xl">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 space-y-6">
                <!-- Analysis Information -->
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-500 mb-1 text-sm">Analysis Name</p>
                        <p class="font-semibold text-gray-900 text-base" x-text="selectedRow.nama_analisis"></p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1 text-sm">Analysis Date</p>
                        <p class="font-semibold text-gray-900 text-base" x-text="selectedRow.tanggal_analisis"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-between items-center p-6 border-t bg-gray-50 rounded-b-2xl">
                <!-- PDF Actions -->
                <div class="flex gap-2">
                    <button 
                        @click="pdfModal = true; pdfViewMode = 'view'"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View PDF
                    </button>
                    
                    <a 
                        :href="'/analysis/download/' + selectedRow.file_pdf "
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </a>
                </div>

                <!-- Delete Button Group -->
                <div class="flex gap-2 items-center">
                    <!-- Confirm Prompt -->
                    <template x-if="confirmDelete">
                        <form :action="'{{ url('/dashboard/perusahaan/perjalanan') }}/' + selectedRow.id" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('DELETE')

                            <span class="text-sm text-red-500">Are you sure?</span>

                            <button 
                                type="submit"
                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-md"
                            >
                                Yes, Delete
                            </button>

                            <button 
                                type="button"
                                @click="confirmDelete = false"
                                class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-md"
                            >
                                Cancel
                            </button>
                        </form>
                    </template>

                    <!-- Trigger Delete -->
                    <template x-if="!confirmDelete">
                        <button 
                            @click="confirmDelete = true"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg"
                        >
                            Delete
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced PDF Viewer Modal - Inside the main Alpine.js component -->
    <div 
        x-show="pdfModal" 
        x-transition 
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-70"
        style="display: none;"
    >
        <div 
            class="bg-white w-full max-w-6xl h-[95vh] rounded-2xl shadow-2xl relative overflow-hidden flex flex-col"
            @click.away="pdfModal = false"
        >
            <!-- Enhanced Header -->
            <div class="flex justify-between items-center p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Analysis PDF Document</h2>
                        <p class="text-sm text-gray-600" x-text="selectedRow.nama_analisis"></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- PDF Controls -->
                    <div class="flex items-center gap-2 bg-white rounded-lg p-1 shadow-sm">
                        <button 
                            class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                            title="Zoom In"
                            @click="document.querySelector('#pdfFrame').style.transform = 'scale(1.1)'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                        </button>
                        <button 
                            class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                            title="Zoom Out"
                            @click="document.querySelector('#pdfFrame').style.transform = 'scale(0.9)'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                            </svg>
                        </button>
                        <div class="w-px h-6 bg-gray-300"></div>
                        <a 
                            :href="'/analysis/download/' + selectedRow.file_pdf + "
                            class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors"
                            title="Download PDF"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </a>
                    </div>
                    
                    <button 
                        @click="pdfModal = false" 
                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors"
                        title="Close"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- PDF Content with Loading State -->
            <div class="flex-1 relative overflow-hidden bg-gray-100">
                <!-- Loading Indicator -->
                <div class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10" id="pdfLoader">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Loading PDF document...</p>
                    </div>
                </div>

                <!-- PDF Iframe dengan toolbar tersembunyi -->
                <iframe 
                    id="pdfFrame"
                    :src="'/analysis/' + selectedRow.file_pdf + '#toolbar=0&navpanes=0&scrollbar=0&statusbar=0&messages=0&view=FitH'" 
                    type="application/pdf"
                    class="w-full h-full border-none transition-transform duration-300"
                    onload="document.getElementById('pdfLoader').style.display='none'"
                ></iframe>
            </div>

            <!-- Footer with Document Info -->
            <div class="p-4 bg-gray-50 border-t flex justify-between items-center text-sm text-gray-600">
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0a1 1 0 00-1 1v7a1 1 0 001 1h6a1 1 0 001-1V8a1 1 0 00-1-1" />
                        </svg>
                        Document Type: PDF
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Generated: <span x-text="selectedRow.tanggal_analisis"></span>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        @click="pdfModal = false"
                        class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-md"
                    >
                        Back to Detail
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Modal Open Discussion -->
    <div 
        x-show="showDiscussionModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
        style="display: none;"
    >
        <div 
            @click.away="showDiscussionModal = false; discussionName = ''; discussionMessage = ''; confirmSendConsultation = false"
            class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6"
        >
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-gray-800">New Consultation</h2>
                </div>
                <button 
                    @click="showDiscussionModal = false; discussionName = ''; discussionMessage = ''; confirmSendConsultation = false" 
                    class="text-gray-500 hover:text-gray-700 text-xl"
                >
                    &times;
                </button>
            </div>

            <!-- Form untuk Kirim Konsultasi -->
            <form method="POST" action="{{ route('konsultasi.upload') }}">
                @csrf
                <input type="hidden" name="selected_id" :value="selectedId">

                <!-- Form Inputs -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="discussionName">
                            Consultation Name
                        </label>
                        <input 
                            id="discussionName"
                            type="text" 
                            maxlength="35"
                            name="discussion_name"
                            x-model="discussionName" 
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-[#39AA80] focus:border-[#39AA80] focus:outline-none transition-colors duration-200"
                            placeholder="Enter consultation name..."
                            required
                        />
                        <div class="text-right text-xs text-gray-500 mt-1" x-text="discussionName.length + '/35'"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="discussionMessage">
                            Consultation Message
                        </label>
                        <textarea 
                            id="discussionMessage"
                            name="discussion_message"
                            x-model="discussionMessage" 
                            rows="4" 
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-[#39AA80] focus:border-[#39AA80] focus:outline-none transition-colors duration-200"
                            placeholder="Type your message here..."
                            required
                        ></textarea>
                    </div>
                </div>

                <!-- Confirmation Prompt -->
                <template x-if="confirmSendConsultation">
                    <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="text-sm font-medium text-amber-800">Are you sure you want to send this consultation request?</span>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button 
                                type="button"
                                @click="confirmSendConsultation = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-[#39AA80] text-white rounded-lg hover:bg-emerald-600 transition-colors duration-200"
                            >
                                Confirm
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Action Buttons -->
                <template x-if="!confirmSendConsultation">
                    <div class="flex justify-end gap-3 mt-6">
                        <button 
                            type="button"
                            @click="showDiscussionModal = false; discussionName = ''; discussionMessage = ''; confirmSendConsultation = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        >
                            Cancel
                        </button>

                        <button 
                            type="button"
                            :disabled="!discussionName.trim() || !discussionMessage.trim()"
                            @click="confirmSendConsultation = true"
                            class="px-4 py-2 bg-[#39AA80] text-white rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-emerald-600 transition-colors duration-200"
                        >
                            Send Consultation Request
                        </button>
                    </div>
                </template>
            </form>
        </div>
    </div>


</div>

@endsection
