<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Service - Đặt vé xem phim</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-3xl">🎬</span>
                    <h1 class="text-2xl font-bold text-gray-900">Galaxy Cinema</h1>
                </div>
                
                <!-- Desktop Menu (hidden on mobile) -->
                <div class="hidden md:flex gap-4 items-center">
                    <a href="{{ route('booking.index') }}" class="text-purple-600 hover:text-purple-700 font-semibold">Đặt Vé</a>
                    <a href="{{ route('my.bookings') }}" class="text-gray-600 hover:text-purple-600 font-medium">Vé Của Tôi</a>
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-purple-600 font-medium">Cài Đặt</a>
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-700 font-medium">{{ auth()->user()->full_name }}</span>
                    <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-all shadow-md hover:shadow-lg">
                            Đăng xuất
                        </button>
                    </form>
                </div>

                <!-- Hamburger Button (visible on mobile) -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Menu">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-200 pt-4">
                <div class="flex flex-col gap-3">
                    <a href="{{ route('booking.index') }}" class="text-purple-600 hover:text-purple-700 font-semibold py-2">Đặt Vé</a>
                    <a href="{{ route('my.bookings') }}" class="text-gray-600 hover:text-purple-600 font-medium py-2">Vé Của Tôi</a>
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-purple-600 font-medium py-2">Cài Đặt</a>
                    <div class="border-t border-gray-200 pt-3 mt-2">
                        <span class="text-gray-700 font-medium block mb-3">{{ auth()->user()->full_name }}</span>
                        <form action="{{ route('auth.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition-all shadow-md hover:shadow-lg min-h-[48px]">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-3 md:px-4 py-6 md:py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 md:px-6 py-3 md:py-4 rounded-lg mb-4 md:mb-6 flex items-center gap-3 shadow-md">
                    <span class="text-xl md:text-2xl">✓</span>
                    <span class="font-medium text-sm md:text-base">{{ session('success') }}</span>
                </div>
            @endif
            
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-2">🎬 Phim Đang Chiếu</h2>
            <p class="text-gray-600 mb-6 md:mb-8 text-sm md:text-base">Chọn phim và đặt vé ngay hôm nay!</p>

            @if($movies->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($movies as $movie)
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:border-purple-500 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group shadow-md max-w-full">
                            <a href="{{ route('booking.movie.detail', $movie->id) }}" class="block">
                                <!-- Movie Poster -->
                                <div class="relative overflow-hidden">
                                    @if($movie->poster)
                                        <img src="{{ $movie->poster }}" 
                                             alt="{{ $movie->title }}" 
                                             class="w-full h-64 md:h-96 object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-64 md:h-96 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400 text-5xl md:text-6xl">🎬</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Genre Badge -->
                                    @if($movie->genre)
                                        <div class="absolute top-3 left-3">
                                            <span class="bg-purple-600/90 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full font-semibold shadow-sm">
                                                {{ $movie->genre }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Movie Info -->
                                <div class="p-4 md:p-5">
                                    <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors">
                                        {{ $movie->title }}
                                    </h3>
                                    
                                    <div class="text-gray-500 text-sm mb-3 md:mb-4 flex items-center gap-2">
                                        @if($movie->duration_min)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $movie->duration_min }} phút
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Quick Showtimes -->
                                    @if($movie->showtimes && $movie->showtimes->count() > 0)
                                        <div class="flex flex-wrap gap-2 mb-3 md:mb-4">
                                            @foreach($movie->showtimes->take(3) as $showtime)
                                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded border border-gray-200 font-medium">
                                                    {{ \Carbon\Carbon::parse($showtime->start_at)->format('H:i') }}
                                                </span>
                                            @endforeach
                                            @if($movie->showtimes->count() > 3)
                                                <span class="text-xs text-gray-500 flex items-center">+{{ $movie->showtimes->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 md:py-2.5 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg text-sm md:text-base min-h-[44px]">
                                        Đặt vé ngay
                                    </button>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 p-8 md:p-16 text-center shadow-sm">
                    <span class="text-5xl md:text-6xl mb-4 block">🎬</span>
                    <p class="text-gray-500 text-lg md:text-xl">Hiện tại chưa có phim nào đang chiếu</p>
                </div>
            @endif
        </div>
    </div>
    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuBtn && mobileMenu) {
                // Toggle menu on button click
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    mobileMenu.classList.toggle('hidden');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                        mobileMenu.classList.add('hidden');
                    }
                });
                
                // Close menu when clicking on a menu link
                mobileMenu.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                    });
                });
            }
        });
    </script>
</body>
</html>
