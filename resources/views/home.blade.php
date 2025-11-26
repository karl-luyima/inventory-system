@extends('layouts.public')

@section('title', 'MySMEAccess')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center text-center px-6 md:px-12
            bg-gradient-to-br from-green-100 via-green-50 to-green-100">

    <!-- Hero Section -->
    <div class="max-w-3xl space-y-6 mt-12 animate-fadeIn">
        <h1 class="text-6xl md:text-7xl font-extrabold text-green-700 drop-shadow-lg">
            MySMEAccess
        </h1>
        <p class="text-gray-700 text-lg md:text-xl">  
            Simplifying SME management. Track products, manage inventory, and grow your SME seamlessly.
        </p>

        <!-- Buttons -->
        <div class="flex flex-col md:flex-row justify-center gap-4 mt-8">
            <a href="{{ route('login') }}" 
               class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-8 py-3 rounded-lg shadow-lg hover:from-blue-700 hover:to-blue-600 hover:shadow-xl transition transform hover:-translate-y-1 hover:scale-105">
               Login
            </a>
            <a href="{{ route('register') }}" 
               class="bg-gradient-to-r from-green-600 to-green-500 text-white px-8 py-3 rounded-lg shadow-lg hover:from-green-700 hover:to-green-600 hover:shadow-xl transition transform hover:-translate-y-1 hover:scale-105">
               Register
            </a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-6xl mt-20 grid grid-cols-1 md:grid-cols-3 gap-12 text-left">
        @php
            $features = [
                ['icon'=>'📦','title'=>'Inventory Management','desc'=>'Keep track of your products, stock levels, and sales all in one place.'],
                ['icon'=>'📊','title'=>'Sales Analytics','desc'=>'Monitor sales performance, generate reports, and identify growth opportunities.'],
                ['icon'=>'⚡','title'=>'Quick Access','desc'=>'Access all your SME tools quickly and efficiently, anytime, anywhere.'],
            ];
        @endphp

        @foreach($features as $feature)
        <div class="feature-card bg-white rounded-lg shadow-lg p-6 flex flex-col items-center text-center transform transition hover:-translate-y-2 hover:shadow-xl opacity-0">
            <div class="text-green-600 text-4xl mb-4">{{ $feature['icon'] }}</div>
            <h3 class="text-xl font-bold mb-2">{{ $feature['title'] }}</h3>
            <p class="text-gray-600">{{ $feature['desc'] }}</p>
        </div>
        @endforeach
    </div>

</div>

<!-- Tailwind Animations -->
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 1s ease-out forwards;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slideUp {
    animation: slideUp 0.8s ease-out forwards;
}
</style>

<!-- Scroll Animation Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.feature-card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-slideUp');
                entry.target.style.opacity = 1;
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    cards.forEach(card => observer.observe(card));
});
</script>
@endsection
