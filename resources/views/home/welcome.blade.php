<section class="relative bg-gradient-to-br from-[#39AA80] to-[#2C8A68] text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-repeat opacity-5"></div>
        @for ($i = 0; $i < 5; $i++)
            <div class="absolute rounded-full bg-white/10 animate-float"
                style="width: {{ rand(100, 400) }}px; height: {{ rand(100, 400) }}px; top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-duration: {{ rand(20, 30) }}s;">
            </div>
        @endfor
    </div>

    <div class="container mx-auto px-4 py-20 md:py-32 relative z-10">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0 -mt-40 ml-10">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                Optimize Your Business, Minimize Your Carbon Footprint
                </h1>
                <p class="text-xl mb-8 text-white/90 max-w-lg">
                We provide smart carbon emission analysis to help companies grow sustainably, meet compliance, and drive environmental impact
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-6 py-3 bg-white text-[#39AA80] font-medium rounded-md hover:bg-white/90 transition-colors">
                        Get Started
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                    <a href="#services" class="inline-flex justify-center items-center px-6 py-3 border border-white text-white font-medium rounded-md hover:bg-white/10 transition-colors">
                        Learn More
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <div class="relative w-full max-w-md aspect-square">
                    <div class="absolute inset-0 bg-white/10 rounded-full animate-pulse"></div>
                    <img src="{{ asset('images/home_image.jpg') }}" alt="Hero Image" class="relative z-10 drop-shadow-2xl rounded-lg">
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

<section class="py-20 bg-white relative z-10 -mt-32" id="services">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Our Services</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Choose the right service for your business needs and scale with confidence.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($dataService as $index => $service)
                @php
                    $features = json_decode($service->deskripsi_service, true) ?? [];
                    $isRecommended = $index === 1; // Make the second service recommended
                @endphp

                <div class="border {{ $isRecommended ? 'border-2 border-[#39AA80]' : 'border-gray-200' }} rounded-xl p-8 shadow-sm hover:shadow-lg transition relative flex flex-col h-full">
                    @if($isRecommended)
                        <span class="absolute top-0 right-0 bg-[#39AA80] text-white text-xs px-3 py-1 rounded-bl-lg font-semibold">Recommended</span>
                    @endif

                    <h3 class="text-xl font-bold mb-2 text-[#39AA80]">{{ $service->nama_service }}</h3>
                    <p class="text-gray-600 mb-4">Duration: {{ $service->durasi_service }} minutes</p>
                    <div class="text-3xl font-bold text-[#39AA80] mb-6">
                        Rp {{ number_format($service->harga_service, 0, ',', '.') }}
                        <span class="text-base font-normal text-gray-500">/ service</span>
                    </div>

                    <ul class="space-y-3 text-gray-700 mb-6">
                        @foreach($features as $feature)
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✔</span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <a href="/register/service/" class="mt-auto block text-center bg-[#39AA80] text-white px-6 py-3 rounded-md hover:bg-[#2C8A68] transition font-medium">
                        Choose {{ $service->nama_service }}
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No services available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50" id="about">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Latest Information</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Stay updated with the latest insights and tips about sustainability and carbon footprint reduction.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($dataInformasi as $info)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow duration-300 group">
                    <div class="relative h-48 overflow-hidden">
                        {{-- PERBAIKAN DI SINI: MENAMBAHKAN this.onerror=null --}}
                        <img src="{{ asset('images/' . $info->gambar_informasi) }}"
                             alt="{{ $info->judul_informasi }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                             onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}'">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#39AA80]/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-3 group-hover:text-[#39AA80] transition-colors duration-300">
                            {{ $info->judul_informasi }}
                        </h3>
                        <p class="text-gray-600">
                            {{ Str::limit($info->isi_informasi, 150) }}
                        </p>
                        <div class="mt-4">
                            {{-- <a href="/informasi/{{ $info->id }}" class="inline-flex items-center text-[#39AA80] hover:text-[#2C8A68] transition-colors font-medium">
                                Read More
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </a> --}}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No information available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="py-20 bg-[#39AA80]" id="contact">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-white">Ready to Get Started?</h2>
        <p class="text-white/90 max-w-2xl mx-auto mb-8 text-lg">
            Join thousands of satisfied customers who have transformed their businesses with our solutions.
        </p>
        {{-- <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('contact') }}" class="inline-flex justify-center items-center px-6 py-3 bg-white text-[#39AA80] font-medium rounded-md hover:bg-white/90 transition-colors">
                Contact Us Today
            </a>
        </div> --}}
    </div>
</section>
