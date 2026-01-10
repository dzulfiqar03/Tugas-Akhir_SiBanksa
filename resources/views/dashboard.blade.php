@extends('layouts.app')

@section('title', 'Dashboard')

@section('dashboard')
    @if (Auth::user()->user_detail->status === 'Pengajuan Verifikasi')
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="card collapsed-card w-full" id="card_pengajuan">

                <div class="card-header my-3 p-5 flex flex-col gap-5 dark:bg-gray-800 bg-gray-200 rounded-2xl">
                    <h3 class="card-title border-b border-gray-600 font-bold text-xl py-5 text-red-500 dark:text-white  w-full">
                        Anda belum melakukan verifikasi akun !!!
                    </h3>

                    <span class="w-full font-medium text-black dark:text-gray-300">Isi Biodata anda dan keperluan dokumen (Opsional)</span>


                    <button type="button" data-card-widget="collapse" title="Collapse"
                        class=" @if (Auth::user()->user_detail->status === 'Pengajuan Verifikasi')
                        bg-red-800
                        @endif hover:bg-green-600 text-white btn-tool font-medium px-4 py-2 rounded-md shadow-sm transition">
                        <span><i :class="open ? 'fas fa-minus' : 'fas fa-plus'"></i></span> Lengkapi Data dan Dokumen
                    </button>

                </div>


                <div class="card-body flex flex-col gap-3 p-5 bg-gray-100 dark:bg-gray-900 rounded-lg"
                    style="display: none">
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

            </div>


        </div>
    @else
        <h2 class="text-3xl font-semibold text-gray-800 dark:text-gray-200">Welcome to the Dashboard</h2>
        <p class="mt-4 text-gray-600 dark:text-gray-400">This is your main dashboard where you can find an overview of your
            application.</p>
    @endif
@endsection
