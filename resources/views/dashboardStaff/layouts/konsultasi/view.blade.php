@extends('dashboardStaff.layouts.app')

@section('title', 'Consultations')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Staff Dashboard</h1>
    <p class="text-gray-500">Welcome back, John! Here's what's happening.</p>
</div>

<div 
    class="bg-white rounded-md shadow-sm shadow-blue-100 border border-gray-300 p-6 mb-6"
    x-data="{ 
        showModal: false,
        selectedRow: {},
        confirmDelete: false,
        showReplyModal: false,
        pdfModal: false,
        selectedFileName: '',
        replyTitle: '',
        replyMessage: '',
        confirmSendReply: false,
        successModal: {{ json_encode(session('success') ? true : false) }},
        pdfViewMode: 'view' // 'view' or 'download'
    }"
    x-init="if (successModal) { setTimeout(() => successModal = false, 4000) }"
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
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consultation Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consultation Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="(row, index) in filteredData" :key="index">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="row.no"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.nama_perusahaan"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="row.nama_konsultasi"></td>
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
        x-show="showModal && !pdfModal" 
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
                                <p class="text-gray-500 mb-1">Company Name</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.nama_perusahaan"></p>
                            </div>
                            <div>
                                <p class="text-gray-500 mb-1">Consultation Name</p>
                                <p class="font-medium text-gray-900 text-base" x-text="selectedRow.nama_konsultasi"></p>
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
                                <p class="font-medium text-gray-900 text-base whitespace-pre-line" x-text="selectedRow.isi_konsultasi || 'Not specified'"></p>
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
                </div>

                <!-- Reply Button -->
                <div class="flex gap-2 items-center">
                    <button 
                        @click="showModal = false; showReplyModal = true"
                        class="px-4 py-2 bg-[#39AA80] hover:bg-green-700 text-white text-sm font-semibold rounded-lg"
                    >
                        Reply the Consultation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Modal Reply -->
    <div 
        x-show="showReplyModal" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
        style="display: none;"
    >
        <div 
            @click.away="showReplyModal = false; replyTitle = ''; replyMessage = ''; selectedFileName = ''; confirmSendReply = false"
            class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6"
        >
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-gray-800">Reply the Consultation</h2>
                </div>
                <button 
                    @click="showReplyModal = false; replyTitle = ''; replyMessage = ''; selectedFileName = ''; confirmSendReply = false" 
                    class="text-gray-500 hover:text-gray-700 text-xl"
                >
                    &times;
                </button>
            </div>

            <!-- Form untuk Reply Konsultasi -->
            <form method="POST" action="{{ route('staff.konsultasi.reply') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="consultation_id" :value="selectedRow.id">

                <!-- Form Inputs -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="replyTitle">
                            Subject
                        </label>
                        <input 
                            id="replyTitle"
                            type="text" 
                            maxlength="35"
                            name="title"
                            x-model="replyTitle" 
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-[#39AA80] focus:border-[#39AA80] focus:outline-none transition-colors duration-200"
                            placeholder="Enter reply subject..."
                            required
                        />
                        <div class="text-right text-xs text-gray-500 mt-1" x-text="replyTitle.length + '/35'"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="replyMessage">
                            Message
                        </label>
                        <textarea 
                            id="replyMessage"
                            name="message"
                            x-model="replyMessage" 
                            rows="4" 
                            class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-[#39AA80] focus:border-[#39AA80] focus:outline-none transition-colors duration-200"
                            placeholder="Type your reply message here..."
                            required
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Attach File
                        </label>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center px-4 py-2 bg-[#39AA80] text-white rounded-lg cursor-pointer hover:bg-green-700 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Choose File
                                <input 
                                    type="file" 
                                    name="file" 
                                    class="hidden"
                                    @change="selectedFileName = $event.target.files[0]?.name || ''"
                                />
                            </label>
                            <span 
                                class="text-xs text-gray-500 flex-1" 
                                x-text="selectedFileName || 'PDF, DOCX, JPG up to 5MB'"
                            ></span>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Prompt -->
                <template x-if="confirmSendReply">
                    <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="text-sm font-medium text-amber-800">Are you sure you want to send this reply?</span>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button 
                                type="button"
                                @click="confirmSendReply = false"
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
                <template x-if="!confirmSendReply">
                    <div class="flex justify-end gap-3 mt-6">
                        <button 
                            type="button"
                            @click="showReplyModal = false; showModal = true; replyTitle = ''; replyMessage = ''; selectedFileName = ''; confirmSendReply = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        >
                            Back
                        </button>

                        <button 
                            type="button"
                            :disabled="!replyTitle.trim() || !replyMessage.trim()"
                            @click="confirmSendReply = true"
                            class="px-4 py-2 bg-[#39AA80] text-white rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-emerald-600 transition-colors duration-200"
                        >
                            Send Reply
                        </button>
                    </div>
                </template>
            </form>
        </div>
    </div>

    <!-- Enhanced PDF Viewer Modal -->
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

    <!-- Modal Success -->
    <div 
        x-show="successModal" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        style="display: none;"
    >
        <div 
            @click.away="successModal = false" 
            class="bg-white max-w-md w-full rounded-xl shadow-lg p-6 text-center relative"
            @click.stop
        >
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-green-700">Success</h3>
                <button @click="successModal = false" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
            </div>

            <!-- Content -->
            <div class="text-green-600 text-sm mb-4">
                <p>{{ session('success') }}</p>
            </div>

            <!-- Footer -->
            <div class="mt-4">
                <button 
                    @click="successModal = false" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

</div>

@endsection