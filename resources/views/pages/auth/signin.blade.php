@extends('layouts.app')

@section('title', 'Login')

@section('login')
    @php
        $currentRouteName = Route::currentRouteName();
    @endphp
    <!-- Form -->
    <div class="flex flex-col  flex-1 w-96">

        <div class="flex flex-col justify-center flex-1 w-full max-w-md">
            <div x-data="{ showUsername: false }">
                <div class=" mb-5 justify-center sm:mb-8">

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5">
                        <h1
                            class="my-auto text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white font-[Poppins]">
                            <span class="text-emerald-600 dark:text-emerald-400">SI </span>BANKSA
                        </h1>

                        <div class="w-full flex justify-end">
                            <div class="transform scale-90 flex w-max items-center gap-3">
                               


                                <a href="{{ route('register') }}"
                                    class="group relative flex items-center justify-start gap-0 hover:gap-3 overflow-hidden rounded-full bg-gray-100 px-4 py-3 text-sm font-medium text-gray-700 w-max transition-all duration-300 hover:bg-gray-200 hover:pl-6 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>

                                    <span @click="showUsername = !showUsername" class="overflow-hidden pl-3">
                                        Register
                                    </span>
                                </a>
                            </div>
                        </div>


                    </div>
                    <p class="text-sm mt-3 text-gray-500 dark:text-gray-400">
                        Enter your email and password to sign in!
                    </p>
                </div>
                <div>


                @section('titleForm', 'Form Login')

                @section('formLogin')
                    @foreach ($formdata as $index => $form)
                        @foreach ($form as $field)
                            @if ($index === 'userAuth')
                                @if ($currentRouteName == 'login' && in_array($field['name'], ['email', 'password']))
                                    <!-- {{ $field['title'] }} -->
                                    <div class="mt-5">
                                        @if ($field['title'] == 'Password')
                                            <label
                                                class="block text-[11px] font-bold text-gray-400 uppercase mb-1.5 ml-1">{{ $field['title'] }}</label>

                                            <div x-data="{ showPassword: false }" class="relative">
                                                <input :type="showPassword ? 'text' : 'password'"
                                                    value="{{ old($field['name']) }}" name="{{ $field['name'] }}"
                                                    placeholder="{{ $field['placeholder'] }}" id="{{ $field['name'] }}"
                                                    class="w-full h-11 rounded-xl  @error($field['name']) border-red-500 ring-1 ring-red-500 @enderror  border-gray-200  
                                        bg-gray-50  pl-5 text-sm pr-11 dark:bg-gray-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">


                                                <span @click="showPassword = !showPassword"
                                                    class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-3 top-8 dark:text-gray-400">
                                                    <svg x-show="!showPassword" class="fill-current" width="20"
                                                        height="20" viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z"
                                                            fill="#98A2B3" />
                                                    </svg>
                                                    <svg x-show="showPassword" class="fill-current" width="20"
                                                        height="20" viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                                            fill="#98A2B3" />
                                                    </svg>
                                                </span>

                                            </div>
                                        @else
                                            <div class="col-span-1">
                                                <label
                                                    class="block text-[11px] font-bold text-gray-400 uppercase mb-1.5 ml-1">{{ $field['title'] }}</label>
                                                <input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
                                                    value="{{ old($field['name']) }}" name="{{ $field['name'] }}"
                                                    placeholder="{{ $field['placeholder'] }}"
                                                    class="w-full h-11 rounded-xl @error($field['name']) border-red-500 ring-1 ring-red-500 @enderror  border-gray-200  bg-gray-50  pl-5 dark:bg-gray-800 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @endforeach

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me"
                            class="inline-flex items-center bg-white rounded dark:bg-gray-900 border-gray-600 dark:border-gray-700">
                            <input id="remember_me" type="checkbox"
                                class="rounded dark:bg-gray-700 bg-gray-300 border-gray-600 dark:border-gray-700 text-emerald-600 shadow-sm focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:focus:ring-offset-gray-800"
                                name="remember">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        @if (Route::has('password.request'))
                            <a class=" text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>
                    <button type="submit"
                        class="flex items-center justify-center text-center px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-emerald-600 shadow-theme-xs hover:bg-emerald-700">
                        Sign In
                    </button>
                @endsection

                @include('components.tailwind-admin.form-element')
                {{-- <form action="{{ route('login.submit') }}" method="POST"> --}}

            </div>
        </div>
    </div>
</div>





@endsection
