 <div class="rounded-full border p-3 flex gap-3 border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-800 dark:bg-white/3">
     <div class="m-auto">
         <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
        {{Auth::user()->user_detail->userName}}
         </h3>
     </div>
     <div class=" border-gray-100 dark:border-gray-800">
        @auth
    @php
        // Mengambil inisial: Muhammad Dzulfiqar -> MD
        $words = explode(' ', Auth::user()->user_detail->fullName);
        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    @endphp

    <div class="profile-circle py-1 px-2 rounded-full border border-gray-600 text-gray-800 dark:text-white">
        {{ $initials }}
    </div>
@else
    <div class="profile-circle">
        <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name=Guest&background=random" alt="Guest">
        {{-- Atau pakai Icon FontAwesome: <i class="fas fa-user-secret"></i> --}}
    </div>
@endauth
     </div>
 </div>
