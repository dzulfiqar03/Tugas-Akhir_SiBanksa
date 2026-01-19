@extends('layouts.app')

@section('title', 'Register')

@section('register')
    @php
        $currentRouteName = Route::currentRouteName();
        $isTargetError = old('id_roles') == 2 || old('id_roles') == 3;
        $isBankSampahActive = old('id_roles', 2) == 2;
        $isNasabahActive = old('id_roles') == 3;
    @endphp

    <div class="flex flex-col flex-1 w-full lg:w-[32rem]  rounded-3xl">

        <div class="mb-5 text-center lg:text-left">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white font-[Poppins]">
                    <span class="text-emerald-600 dark:text-emerald-400">Si</span>Banksa
                </h1>

                <div class="flex gap-3">
        



                    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
                    <div
                        class="px-4 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider">
                        Join Us
                    </div>
                </div>

            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Create your account</h2>
            <p class="text-sm mt-2 text-gray-500 dark:text-gray-400">Pilih tipe akun untuk memulai pengalaman baru Anda.</p>
        </div>

        <div class="flex p-1.5 mb-5 bg-gray-100 dark:bg-gray-800/50 rounded-2xl shadow-inner overflow-hidden">
            <button @click="showForm = 'BankSampah'"
                :class="showForm === 'BankSampah' ? 'bg-white dark:bg-gray-700 shadow-md text-emerald-600 scale-[1.02]' :
                    'text-gray-500 hover:text-gray-700'"
                class="flex-1 flex items-center justify-center gap-3 py-3.5 rounded-xl transition-all duration-500 font-semibold text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-10V4m-5 4h1m-1 4h1m5 10V4a1 1 0 00-1-1h-5a1 1 0 00-1 1v14a1 1 0 001 1h5a1 1 0 001-1z" />
                </svg>
                Bank Sampah
            </button>
            <button @click="showForm = 'Nasabah'"
                :class="showForm === 'Nasabah' ? 'bg-white dark:bg-gray-700 shadow-md text-emerald-600 scale-[1.02]' :
                    'text-gray-500 hover:text-gray-700'"
                class="flex-1 flex items-center justify-center gap-3 py-3.5 rounded-xl transition-all duration-500 font-semibold text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Warga
            </button>
        </div>

    @section('titleForm', 'Form Register')

    @section('formRegisterBankSampah')

        <div x-data="{
            step: 1
        }" class="space-y-6">

            <div class="flex items-center gap-4 mb-8">
                <div class="flex items-center gap-2 cursor-pointer" @click="step = 1">
                    <span
                        :class="step >= 1 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-100  dark:shadow-gray-800' :
                            'bg-gray-200 text-gray-500'"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">1</span>
                    <span :class="step >= 1 ? 'text-emerald-700' : 'text-gray-400'"
                        class="text-[10px] font-bold uppercase tracking-widest">Data Diri</span>
                </div>
                <div class="h-px bg-gray-200 flex-1"></div>
                <div class="flex items-center gap-2 cursor-pointer" @click="step = 2">
                    <span
                        :class="step >= 2 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-100 dark:shadow-gray-800' :
                            'bg-gray-200 text-gray-500'"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">2</span>
                    <span :class="step >= 2 ? 'text-emerald-700' : 'text-gray-400'"
                        class="text-[10px] font-bold uppercase tracking-widest">Akun</span>
                </div>
            </div>

            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-x-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    @foreach ($formdata['nasabah'] as $field)
                        @if ($currentRouteName == 'register')
                            @if ($field['name'] == 'rt')
                                <div class="col-span-2">
                                    <label
                                        class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">{{ $field['title'] }}</label>
                                    <select id="{{ $field['name'] }}" name="id_rt"
                                        class="w-full h-11 rounded-xl @if ($isBankSampahActive) @error('bankSampah.' . $field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror @endif bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                        <option value="">Pilih RT</option>
                                        @foreach ($field['options'] as $option)
                                            {{-- Gunakan $option sebagai value agar sinkron dengan hasil scan --}}
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($field['name'] != 'status' && $field['type'] != 'file' && $field['type'] != 'radio')
                                <div class="col-span-1">
                                    <label
                                        class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">{{ $field['title'] }}</label>
                                    <input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
                                        name="bankSampah[{{ $field['name'] }}]"
                                        value="{{ old('bankSampah.' . $field['name']) }}"
                                        placeholder="{{ $field['placeholder'] }}"
                                        class="w-full h-11 rounded-xl  @if ($isBankSampahActive) @error('bankSampah.' . $field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror @endif bg-gray-50 dark:bg-gray-800 dark:text-white  pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" @click="step = 2"
                        class="px-10 py-3 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-100 dark:shadow-gray-800 hover:bg-emerald-700 transition-all active:scale-95">
                        Lanjut Ke Akun &rarr;
                    </button>
                </div>
            </div>

            <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-x-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    @foreach ($formdata['userAuth'] as $field)
                        @if ($field['name'] == 'password' || $field['name'] == 'password_confirmation')
                            <div class="col-span-1" x-data="{ show: false }">
                                <label
                                    class="block text-[11px] font-bold text-gray-400 uppercase mb-1.5 ml-1">{{ $field['title'] }}</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="bankSampah[{{ $field['name'] }}]"
                                        placeholder="{{ $field['placeholder'] }}"
                                        value="{{ old('bankSampah.' . $field['name']) }}"
                                        class="w-full h-11 rounded-xl @if ($isBankSampahActive) @error('bankSampah.' . $field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror @endif
                                         bg-gray-50  pl-5 text-sm pr-11 dark:bg-gray-800 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                    <button type="button" @click="show = !show"
                                        class="absolute right-3 top-1/4 text-gray-400 hover:text-emerald-600 transition-colors">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057-5.064-7-9.542-7-4.477 0-8.268-2.943-9.542-7zM3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="col-span-1">
                                <label
                                    class="block text-[11px] font-bold text-gray-400 uppercase mb-1.5 ml-1">{{ $field['title'] }}</label>
                                <input type="{{ $field['type'] }}" name="bankSampah[{{ $field['name'] }}]"
                                    value="{{ old('bankSampah.' . $field['name']) }}"
                                    placeholder="{{ $field['placeholder'] }}"
                                    class="w-full h-11 rounded-xl @if ($isBankSampahActive) @error('bankSampah.' . $field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror @endif bg-gray-50  pl-5 dark:bg-gray-800 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-8 flex justify-between gap-4 border-t pt-6">
                    <button type="button" @click="step = 1"
                        class="text-sm font-bold text-gray-400 hover:text-emerald-600 transition-all">
                        &larr; Kembali ke Data Diri
                    </button>
                    <button type="submit"
                        class="px-10 py-3 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-100 dark:shadow-gray-800 hover:bg-emerald-700 transition-all active:scale-95">
                        Simpan Pendaftaran
                    </button>
                </div>
            </div>

            <div id="barcode-engine" style="display: none;"></div>
        </div>
    @endsection

    @section('formRegisterNasabah')

        <div x-data="{
            step: 1
        }" class="space-y-6">

            <div class="flex items-center gap-4 mb-8">
                <div class="flex items-center gap-2 cursor-pointer" @click="step = 1">
                    <span
                        :class="step >= 1 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-100 dark:shadow-gray-800' :
                            'bg-gray-200 text-gray-500'"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">1</span>
                    <span :class="step >= 1 ? 'text-emerald-700' : 'text-gray-400'"
                        class="text-[10px] font-bold uppercase tracking-widest">Data Diri</span>
                </div>
                <div class="h-px bg-gray-200 flex-1"></div>
                <div class="flex items-center gap-2 cursor-pointer" @click="step = 2">
                    <span
                        :class="step >= 2 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-100 dark:shadow-gray-800' :
                            'bg-gray-200 text-gray-500'"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-bold transition-all">2</span>
                    <span :class="step >= 2 ? 'text-emerald-700' : 'text-gray-400'"
                        class="text-[10px] font-bold uppercase tracking-widest">Akun</span>
                </div>
            </div>

            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-x-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    @foreach ($formdata['nasabah'] as $field)
                        @if ($currentRouteName == 'register')
                           @if ($field['type'] == 'radio')
    <div class="col-span-full">
        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">
            {{ $field['title'] }}
        </label>
        
        <div class="flex gap-3">
            @foreach ($field['options'] as $index => $option)
                @php
                    // Sesuaikan value jika di database mulai dari 1
                    $currentValue = $index + 1;
                    $errorKey = 'nasabah.' . $field['name'];
                    $hasError = $errors->has($errorKey);
                @endphp
                
                <label class="flex-1 cursor-pointer group">
                    <input type="radio" 
                        name="nasabah[{{ $field['name'] }}]"
                        value="{{ $currentValue }}"
                        class="peer sr-only"
                        {{ old('nasabah.' . $field['name']) == $currentValue ? 'checked' : '' }}>
                    
                    <div class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border-2 bg-gray-50 text-gray-500 transition-all 
                        {{ $hasError ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }}
                        peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 peer-checked:text-emerald-700
                        dark:peer-checked:bg-emerald-500/10 dark:peer-checked:text-emerald-400">
                        
                        <div class="w-2 h-2 rounded-full transition-colors
                            {{ $hasError ? 'bg-red-500' : 'bg-gray-300' }}
                            peer-checked:bg-emerald-500">
                        </div>
                        
                        <span class="text-sm font-bold">{{ $option }}</span>
                    </div>
                </label>
            @endforeach
        </div>

    </div>
 @elseif ($field['name'] == 'rt')
                                <div class="col-span-2">
                                    <label
                                        class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">{{ $field['title'] }}</label>
                                    <select id="{{ $field['name'] }}" name="nasabah[id_rt]"
                                        class="w-full h-11 rounded-xl 
                                        @if ($isNasabahActive) @error('nasabah.' . $field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror @endif
                                        bg-gray-50 dark:bg-gray-800 dark:text-white  text-sm  pl-5 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                        <option value="">Pilih RT</option>
                                        @foreach ($field['options'] as $option)
                                            {{-- Gunakan $option sebagai value agar sinkron dengan hasil scan --}}
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($field['name'] != 'status' && $field['type'] != 'file')
                                <div class="col-span-1">
                                    <label
                                        class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 ml-1">{{ $field['title'] }}</label>
                                    <input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
                                        name="nasabah[{{ $field['name'] }}]"
                                        value="{{ old(key: 'nasabah.' . $field['name']) }}"
                                        placeholder="{{ $field['placeholder'] }}"
                                        class="w-full h-11 rounded-xl bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all 
    
                                        @if ($isNasabahActive) @error('nasabah.' . $field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror @endif">
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" @click="step = 2"
                        class="px-10 py-3 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-100 dark:shadow-gray-800 hover:bg-emerald-700 transition-all active:scale-95">
                        Lanjut Ke Akun &rarr;
                    </button>
                </div>
            </div>

            <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-x-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    @foreach ($formdata['userAuth'] as $field)
                        @if ($field['name'] == 'password' || $field['name'] == 'password_confirmation')
                            <div class="col-span-1" x-data="{ show: false }">
                                <label
                                    class="block text-[11px] font-bold text-gray-400 uppercase mb-1.5 ml-1">{{ $field['title'] }}</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="nasabah[{{ $field['name'] }}]"
                                        placeholder="{{ $field['placeholder'] }}"
                                        value="{{ old(key: 'nasabah.' . $field['name']) }}"
                                        class="w-full h-11 rounded-xl  bg-gray-50 
                                        @if ($isNasabahActive) @error('nasabah.' . $field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror @endif
                                        text-sm pr-11 dark:bg-gray-800 dark:text-white    pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                    <button type="button" @click="show = !show"
                                        class="absolute right-3 top-1/4  text-gray-400 hover:text-emerald-600 transition-colors">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057-5.064-7-9.542-7-4.477 0-8.268-2.943-9.542-7zM3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="col-span-1">
                                <label
                                    class="block text-[11px] font-bold text-gray-400 uppercase mb-1.5 ml-1">{{ $field['title'] }}</label>
                                <input type="{{ $field['type'] }}" name="nasabah[{{ $field['name'] }}]"
                                    value="{{ old(key: 'nasabah.' . $field['name']) }}"
                                    placeholder="{{ $field['placeholder'] }}"
                                    class="w-full h-11 rounded-xl border-gray-200 bg-gray-50 
                                    @if ($isNasabahActive) @error('nasabah.' . $field['name']) border-red-500 ring-1 ring-red-500 @else border-gray-200 @enderror @endif
                                    text-sm dark:bg-gray-800 dark:text-white   pl-5 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-8 flex justify-between gap-4 border-t pt-6">
                    <button type="button" @click="step = 1"
                        class="text-sm font-bold text-gray-400 hover:text-emerald-600 transition-all">
                        &larr; Kembali ke Data Diri
                    </button>
                    <button type="submit"
                        class="px-10 py-3 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-100 dark:shadow-gray-800 hover:bg-emerald-700 transition-all active:scale-95">
                        Simpan Pendaftaran
                    </button>
                </div>
            </div>

            <div id="barcode-engine" style="display: none;"></div>
        </div>
    @endsection


    <div class="relative transition-all duration-500">
        @php $formType = 'BankSampah'; @endphp
        <div x-show="showForm === 'BankSampah'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @include('components.tailwind-admin.form-element', ['formType' => 'BankSampah'])
        </div>

        @php $formType = 'Nasabah'; @endphp
        <div x-show="showForm === 'Nasabah'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @include('components.tailwind-admin.form-element', ['formType' => 'Nasabah'])
        </div>
    </div>

    <p class=" mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
        Already have an account?
        <a href="{{ route('login') }}"
            class="font-bold text-emerald-600 hover:text-emerald-500 transition-colors">Sign In</a>
    </p>
</div>

@endsection
