@extends('layouts.app')

@section('title', 'Tracking Setor Sampah')

@section('tracking')
    @php
        // Fungsi helper untuk styling status
        function statusBadge($status)
        {
            $colors = [
                'completed' => 'bg-green-200 text-green-800',
                'in_progress' => 'bg-blue-200 text-blue-800',
                'pending' => 'bg-gray-200 text-gray-600',
            ];
            return $colors[$status] ?? 'bg-gray-200 text-gray-600';
        }
    @endphp


    <div class="container mx-auto px-4 ">
        <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-white mb-8">Tracking Workflow Proses</h1>

        <!-- Progress Bar Horizontal (tetap ada) -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                @foreach ($workflowSteps as $index => $step)
                    <div class="flex flex-col items-center">
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold
                            {{ $step['status'] === 'completed' ? 'bg-green-500' : ($step['status'] === 'in_progress' ? 'bg-blue-500' : 'bg-gray-300') }}">
                            {{ $index + 1 }}
                        </div>
                        <span class="text-sm mt-2 text-gray-600 dark:text-white">{{ $step['name'] }}</span>
                    </div>
                    @if ($index < count($workflowSteps) - 1)
                        <div
                            class="flex-1 h-1 mx-4
                            {{ $step['status'] === 'completed' ? 'bg-green-500' : 'bg-gray-300' }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- <!-- Detail Tahapan dengan Garis dan Persentase -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($workflowSteps as $step)
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-{{ $step['status'] === 'completed' ? 'check-circle text-green-500' : ($step['status'] === 'in_progress' ? 'spinner fa-spin text-blue-500' : 'clock text-gray-400') }} text-2xl mr-3"></i>
                        <h3 class="text-xl font-semibold text-gray-800">{{ $step['name'] }}</h3>
                    </div>
                    <p class="text-gray-600 mb-4">{{ $step['description'] }}</p>

                    <!-- Persentase dan Garis Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progress</span>
                            <span class="text-sm font-bold text-gray-800">{{ $step['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300
                                {{ $step['status'] === 'completed' ? 'bg-green-500' : ($step['status'] === 'in_progress' ? 'bg-blue-500' : 'bg-gray-300') }}"
                                style="width: {{ $step['percentage'] }}%"></div>
                        </div>
                    </div>

                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                        {{ $step['status'] === 'completed' ? 'bg-green-100 text-green-800' : ($step['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst(str_replace('_', ' ', $step['status'])) }}
                    </span>
                </div>
            @endforeach
        </div> --}}


    @section('titleTable', 'tracking')

    @section('tbhead-tracking')
        <th>No</th>
        <th>Nama Nasabah</th>

        @foreach ($workflowSteps as $step)
            <th>{{ $step['name'] }}</th>
        @endforeach
        <th class="text-center">Aksi</th>
    @endsection

    @section('tbbody-tracking')

        @foreach ($nasabahList as $index => $nasabah)
            <tr class="dark:hover:bg-gray-700">
                <td class="px-4 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $nasabah['nama'] }}</td>

                @foreach ($workflowSteps as $step)
                    @php
                        $st = $nasabah['status'][$step['name']] ?? 'pending';
                    @endphp
                    <td class="px-4 py-2 text-center">
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ statusBadge($st) }}">
                            {{ ucfirst(str_replace('_', ' ', $st)) }}
                        </span>
                    </td>
                @endforeach

                <td class="px-4 py-2 text-center">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                        Detail
                    </button>
                </td>
            </tr>
        @endforeach

    @endsection

    {{-- Include tabel --}}
    @include('components.data-tables')


@endsection
