@extends('dashboardPerusahaan.layouts.app')

@section('title', 'Consultations')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Company Dashboard</h1>
    <p class="text-gray-500">Welcome back, John! Here's what's happening.</p>
</div>

<div 
    class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6"
    x-data="{ 
        showModal: false,
        selectedRow: {},
        confirmDelete: false,
        pdfModal: false,
        pdfViewMode: 'view', // 'view' or 'download'
        replyModal: false,
        replyPdfModal: false
    }"
>

    @if (session('success'))
    <div class="mb-4 p-4 rounded-md border bg-[#39AA80] bg-green-50 text-green-800 shadow-sm flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
        <h2 class="text-xl font-semibold text-gray-800">Consultations</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('konsultasi.add') }}">
                <button class="flex items-center gap-2 px-4 py-2 bg-[#39AA80] text-white rounded-md hover:bg-[#207e5b] border border-[#39AA80]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Open Consultation
                </button>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consultation Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Analysis Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consultation Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(row, index) in filteredData" :key="index">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="row.no"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.nama_konsultasi"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.nama_analisis"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.tanggal_konsultasi"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>
                                <span 
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :class="row.status_konsultasi === 'OPEN' ? 
                                        'bg-green-100 text-green-800 border border-green-300' : 
                                        'bg-red-100 text-red-800 border border-red-300'"
                                    x-text="row.status_konsultasi"
                                ></span>
                            </div>
                        </td>
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

    <!-- Modal Detail -->
    <div 
        x-show="showModal && !pdfModal && !replyModal" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        style="display: none;"
    >
        <div 
            @click.away="showModal = false; confirmDelete = false"
            class="bg-white w-full max-w-6xl rounded-2xl shadow-xl p-6 relative"
        >
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h3 class="text-2xl font-bold text-gray-800">Consultation Detail</h3>
                <button @click="showModal = false; confirmDelete = false" class="text-gray-500 hover:text-gray-700 text-xl">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="bg-white rounded-xl shadow-md p-6 space-y-6 text-sm text-gray-700">
                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Consultation Information -->
                    <div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-500 mb-1">Consultation Name</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.nama_konsultasi"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Analysis Name</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.nama_analisis"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Consultation Date</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.tanggal_konsultasi"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Status</p>
                                <span 
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    :class="selectedRow.status_konsultasi === 'OPEN' ? 
                                        'bg-green-100 text-green-800 border border-green-300' : 
                                        'bg-red-100 text-red-800 border border-red-300'"
                                    x-text="selectedRow.status_konsultasi"
                                ></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Consultation Content -->
                    <div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-500 mb-1">Consultation Message</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.isi_konsultasi || 'Not specified'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t">
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

                    <!-- Show Reply Button for CLOSED status -->
                    <template x-if="selectedRow.status_konsultasi === 'CLOSED'">
                        <button 
                            @click="replyModal = true; showModal = false"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            Show Reply
                        </button>
                    </template>
                </div>

                <!-- Delete Button Group -->
                <div class="flex gap-2 items-center">
                    <!-- Confirm Prompt -->
                    <template x-if="confirmDelete">
                        <form :action="'{{ url('/dashboard/perusahaan/konsultasi') }}/' + selectedRow.id" method="POST" class="flex items-center gap-2">
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

    <!-- Reply Modal -->
    <div 
        x-show="replyModal && !replyPdfModal" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        style="display: none;"
    >
        <div 
            @click.away="replyModal = false"
            class="bg-white w-full max-w-6xl rounded-2xl shadow-xl p-6 relative"
        >
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">Consultation Reply</h3>
                        <p class="text-sm text-gray-600" x-text="selectedRow.nama_konsultasi"></p>
                    </div>
                </div>
                <button @click="replyModal = false" class="text-gray-500 hover:text-gray-700 text-xl">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="bg-white rounded-xl shadow-md p-6 space-y-6 text-sm text-gray-700">
                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Original Consultation Info -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Original Consultation</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-500 mb-1">Consultation Name</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.nama_konsultasi"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Analysis Name</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.nama_analisis"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Consultation Date</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.tanggal_konsultasi"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Original Message</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.isi_konsultasi || 'Not specified'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Reply Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Reply Information</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-500 mb-1">Reply Title</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.judul_pesan || 'No title provided'"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Reply Message</p>
                                <div class="bg-gray-50 rounded-lg p-4 max-h-40 overflow-y-auto">
                                    <p class="font-medium text-gray-900 text-base whitespace-pre-wrap" x-text="selectedRow.isi_pesan || 'No reply message available'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <!-- PDF Actions -->
                <div class="flex gap-2">
                    <template x-if="selectedRow.pdf_pesan">
                        <button 
                            @click="replyPdfModal = true"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Reply PDF
                        </button>
                    </template>
                    
                    <template x-if="selectedRow.pdf_pesan">
                        <a 
                            :href="'/reply/download/' + selectedRow.pdf_pesan "
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download Reply PDF
                        </a>
                    </template>

                    <template x-if="!selectedRow.pdf_pesan">
                        <div class="text-sm text-gray-500 italic">
                            No PDF attachment available
                        </div>
                    </template>
                </div>

                <!-- Close Button -->
                <div class="flex gap-2 items-center">
                    <button 
                        @click="replyModal = false"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced PDF Viewer Modal for Original Consultation -->
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
                        <h2 class="text-xl font-bold text-gray-800">Consultation PDF Document</h2>
                        <p class="text-sm text-gray-600" x-text="selectedRow.nama_konsultasi"></p>
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
                            :href="'/analysis/download/' + selectedRow.file_pdf "
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
                        Generated: <span x-text="selectedRow.tanggal_konsultasi"></span>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        @click="pdfModal = false"
                        class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-md"
                    >
                        Back to Detail
                    </button>
                    <span 
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                        :class="selectedRow.status_konsultasi === 'OPEN' ? 
                            'bg-green-100 text-green-800 border border-green-300' : 
                            'bg-red-100 text-red-800 border border-red-300'"
                        x-text="selectedRow.status_konsultasi"
                    ></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced PDF Viewer Modal for Reply -->
    <div 
        x-show="replyPdfModal" 
        x-transition 
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-70"
        style="display: none;"
    >
        <div 
            class="bg-white w-full max-w-6xl h-[95vh] rounded-2xl shadow-2xl relative overflow-hidden flex flex-col"
            @click.away="replyPdfModal = false"
        >
            <!-- Enhanced Header -->
            <div class="flex justify-between items-center p-6 border-b bg-gradient-to-r from-purple-50 to-pink-50">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Reply PDF Document</h2>
                        <p class="text-sm text-gray-600" x-text="selectedRow.judul_pesan || selectedRow.nama_konsultasi"></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- PDF Controls -->
                    <div class="flex items-center gap-2 bg-white rounded-lg p-1 shadow-sm">
                        <button 
                            class="p-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors"
                            title="Zoom In"
                            @click="document.querySelector('#replyPdfFrame').style.transform = 'scale(1.1)'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                        </button>
                        <button 
                            class="p-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors"
                            title="Zoom Out"
                            @click="document.querySelector('#replyPdfFrame').style.transform = 'scale(0.9)'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                            </svg>
                        </button>
                        <div class="w-px h-6 bg-gray-300"></div>
                        <a 
                            :href="'/reply/download/' + selectedRow.pdf_pesan "
                            class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors"
                            title="Download PDF"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </a>
                    </div>
                    
                    <button 
                        @click="replyPdfModal = false" 
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
                <div class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10" id="replyPdfLoader">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Loading reply PDF document...</p>
                    </div>
                </div>

                <!-- PDF Iframe dengan toolbar tersembunyi -->
                <iframe 
                    id="replyPdfFrame"
                    :src="'/messages/' + selectedRow.pdf_pesan + '#toolbar=0&navpanes=0&scrollbar=0&statusbar=0&messages=0&view=FitH'" 
                    type="application/pdf"
                    class="w-full h-full border-none transition-transform duration-300"
                    onload="document.getElementById('replyPdfLoader').style.display='none'"
                ></iframe>
            </div>

            <!-- Footer with Document Info -->
            <div class="p-4 bg-gray-50 border-t flex justify-between items-center text-sm text-gray-600">
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0a1 1 0 00-1 1v7a1 1 0 001 1h6a1 1 0 001-1V8a1 1 0 00-1-1" />
                        </svg>
                        Document Type: Reply PDF
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Reply to: <span x-text="selectedRow.nama_konsultasi"></span>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        @click="replyPdfModal = false"
                        class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-md"
                    >
                        Back to Reply
                    </button>
                    <span 
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-300"
                        x-text="selectedRow.status_konsultasi"
                    ></span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection