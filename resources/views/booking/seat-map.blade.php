<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn ghế - {{ $showtime->movie->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('booking.movie.detail', $showtime->movie_id) }}" class="text-gray-500 hover:text-purple-600 text-2xl transition-colors">←</a>
                    <h1 class="text-2xl font-bold text-gray-900">🎬 Cinema Service</h1>
                </div>
                
                <!-- Desktop Menu (hidden on mobile) -->
                <div class="hidden md:flex gap-4">
                    @auth('web')
                        <a href="{{ route('booking.index') }}" class="text-gray-600 hover:text-purple-600 font-medium">Đặt Vé</a>
                        <a href="{{ route('my.bookings') }}" class="text-gray-600 hover:text-purple-600 font-medium">Vé Của Tôi</a>
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-purple-600 font-medium">Cài Đặt</a>
                    @else
                        <a href="{{ route('auth.login.form') }}" class="text-gray-600 hover:text-purple-600 font-medium">Đăng nhập</a>
                    @endauth
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
                    @auth('web')
                        <a href="{{ route('booking.index') }}" class="text-gray-600 hover:text-purple-600 font-medium py-2">Đặt Vé</a>
                        <a href="{{ route('my.bookings') }}" class="text-gray-600 hover:text-purple-600 font-medium py-2">Vé Của Tôi</a>
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-purple-600 font-medium py-2">Cài Đặt</a>
                    @else
                        <a href="{{ route('auth.login.form') }}" class="text-gray-600 hover:text-purple-600 font-medium py-2">Đăng nhập</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-4 md:py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Movie Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 mb-6 md:mb-8 shadow-md">
                <div class="flex items-center gap-4 md:gap-6">
                    @if($showtime->movie->poster)
                        <img src="{{ $showtime->movie->poster }}" 
                             alt="{{ $showtime->movie->title }}" 
                             class="w-16 h-24 md:w-24 md:h-36 object-cover rounded-lg shadow-sm flex-shrink-0">
                    @endif
                    
                    <div class="min-w-0">
                        <h1 class="text-xl md:text-3xl font-bold text-gray-900 mb-1 md:mb-2 truncate">{{ $showtime->movie->title }}</h1>
                        <div class="text-gray-600 text-sm md:text-base">
                            <p class="flex items-center gap-2 mb-1">
                                <span class="text-lg md:text-xl">🏛️</span> {{ $showtime->screen->name ?? 'Screen' }}
                            </p>
                            <p class="flex items-center gap-2">
                                <span class="text-lg md:text-xl">🕐</span> {{ \Carbon\Carbon::parse($showtime->start_at)->format('H:i, d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seat Map -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-8 mb-8 shadow-md">
                <!-- Screen -->
                <div class="mb-8 md:mb-12">
                    <div class="bg-gradient-to-b from-purple-600 to-transparent h-3 rounded-t-3xl mb-2 opacity-50"></div>
                    <p class="text-center text-gray-400 text-sm font-medium uppercase tracking-widest">Màn hình</p>
                </div>

                <!-- Seats with horizontal scroll wrapper for mobile -->
                <div class="relative">
                    <div class="overflow-x-auto pb-4 -mx-4 px-4">
                        <div class="flex justify-center">
                            <div class="inline-block min-w-max">
                                @foreach($seatsByRow as $row => $seats)
                                    <div class="flex items-center gap-1 md:gap-2 mb-2 md:mb-3">
                                        <!-- Row Label -->
                                        <div class="w-6 md:w-8 text-center text-gray-500 font-bold text-sm md:text-base">{{ $row }}</div>
                                        
                                        <!-- Seats in Row -->
                                        <div class="flex gap-1 md:gap-2">
                                            @foreach($seats as $seat)
                                                @php
                                                    $isBooked = in_array($seat->id, $bookedSeats);
                                                    $isLocked = in_array($seat->id, $lockedSeats);
                                                    $isAvailable = !$isBooked && !$isLocked;
                                                    
                                                    // Minimum 36x36px touch target on mobile (w-9 h-9 = 36px), larger on desktop
                                                    $seatClass = 'seat min-w-[36px] min-h-[36px] w-9 h-9 md:w-10 md:h-10 rounded-t-lg flex items-center justify-center text-xs font-bold transition-all cursor-pointer shadow-sm ';
                                                    
                                                    if ($isBooked) {
                                                        $seatClass .= 'bg-red-100 text-red-400 cursor-not-allowed border border-red-200';
                                                    } elseif ($isLocked) {
                                                        $seatClass .= 'bg-yellow-100 text-yellow-600 cursor-not-allowed border border-yellow-200';
                                                    } else {
                                                        $seatClass .= 'bg-gray-200 hover:bg-purple-600 hover:text-white text-gray-600 seat-available border border-gray-300 hover:border-purple-600';
                                                    }
                                                @endphp
                                                
                                                <div class="{{ $seatClass }}" 
                                                     data-seat-id="{{ $seat->id }}"
                                                     data-seat-number="{{ $seat->seat_number }}"
                                                     data-row="{{ $row }}"
                                                     @if($isAvailable)
                                                        onclick="toggleSeat(this)"
                                                     @endif>
                                                    {{ $seat->seat_number }}
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Row Label (right side) -->
                                        <div class="w-6 md:w-8 text-center text-gray-500 font-bold text-sm md:text-base">{{ $row }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- Scroll hint on mobile -->
                    <div class="md:hidden text-center text-gray-400 text-sm mt-2">
                        ← Vuốt để xem thêm →
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex justify-center gap-4 md:gap-8 mt-8 md:mt-12 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-gray-200 rounded-t-lg border border-gray-300"></div>
                        <span class="text-gray-600 text-xs md:text-sm">Trống</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-purple-600 rounded-t-lg border border-purple-600"></div>
                        <span class="text-gray-600 text-xs md:text-sm">Đang chọn</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-red-100 rounded-t-lg border border-red-200"></div>
                        <span class="text-gray-600 text-xs md:text-sm">Đã đặt</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-yellow-100 rounded-t-lg border border-yellow-200"></div>
                        <span class="text-gray-600 text-xs md:text-sm">Đang giữ</span>
                    </div>
                </div>
            </div>

            <!-- Booking Summary - Sticky at bottom on mobile -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 shadow-md fixed bottom-0 left-0 right-0 md:sticky md:bottom-4 z-40 rounded-b-none md:rounded-b-xl">
                <div class="container mx-auto max-w-7xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-1 md:mb-2">Ghế đã chọn</h3>
                            <p class="text-gray-600 text-sm md:text-base" id="selected-seats">Chưa chọn ghế nào</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500 mb-1 text-xs md:text-sm">Tổng tiền</p>
                            <p class="text-xl md:text-3xl font-bold text-purple-600"><span id="total-price">0</span> đ</p>
                        </div>
                    </div>
                    <button id="continue-btn" disabled 
                            class="w-full mt-4 md:mt-6 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white py-3 md:py-4 rounded-lg font-bold text-base md:text-lg transition-all shadow-md hover:shadow-lg min-h-[48px]">
                        Tiếp tục
                    </button>
                </div>
            </div>
            <!-- Spacer for fixed bottom summary on mobile -->
            <div class="h-40 md:hidden"></div>
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

    <script>
        let totalPrice=0;
        let selectedSeats = [];
        const pricePerSeat = Number('{{ $showtime->base_price }}');

        function toggleSeat(element) {
            const seatId = element.getAttribute('data-seat-id');
            const seatNumber = element.getAttribute('data-seat-number');
            const row = element.getAttribute('data-row');
            const seatLabel = row + seatNumber;

            if (element.classList.contains('bg-purple-600')) {
                // Deselect
                element.classList.remove('bg-purple-600', 'text-white', 'border-purple-600');
                element.classList.add('bg-gray-200', 'text-gray-600', 'border-gray-300');
                selectedSeats = selectedSeats.filter(s => s.id !== seatId);
            } else {
                // Select
                element.classList.remove('bg-gray-200', 'text-gray-600', 'border-gray-300');
                element.classList.add('bg-purple-600', 'text-white', 'border-purple-600');
                selectedSeats.push({
                    id: seatId,
                    label: seatLabel
                });
            }

            updateSummary();
        }

        function updateSummary() {
            const continueBtn = document.getElementById('continue-btn');
            const selectedSeatsEl = document.getElementById('selected-seats');
            const totalPriceEl = document.getElementById('total-price');

            if (selectedSeats.length === 0) {
                selectedSeatsEl.textContent = 'Chưa chọn ghế nào';
                totalPriceEl.textContent = '0';
                continueBtn.disabled = true;
            } else {
                const seatLabels = selectedSeats.map(s => s.label).join(', ');
                selectedSeatsEl.textContent = seatLabels;
                
                const total = selectedSeats.length * pricePerSeat;
                totalPrice = total;
                totalPriceEl.textContent = total.toLocaleString('vi-VN');
                
                continueBtn.disabled = false;
            }
        }

        document.getElementById('continue-btn').addEventListener('click', function() {
            if (selectedSeats.length === 0) return;
            
            // Tạo form để gửi dữ liệu đến trang checkout
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route('payment.checkout') }}';
            
            // Thêm showtime_id
            const showtimeInput = document.createElement('input');
            showtimeInput.type = 'hidden';
            showtimeInput.name = 'showtime_id';
            showtimeInput.value = '{{ $showtime->id }}';
            form.appendChild(showtimeInput);
            
            // Thêm seat_ids
            selectedSeats.forEach(seat => {
                const seatInput = document.createElement('input');
                seatInput.type = 'hidden';
                seatInput.name = 'seat_ids[]';
                seatInput.value = seat.id;
                form.appendChild(seatInput);
            });

            const priceInput = document.createElement('input');
            priceInput.type = "hidden";
            priceInput.name="price";
            priceInput.value = totalPrice;
            form.appendChild(priceInput);            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>
</html>
