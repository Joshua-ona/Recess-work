@extends('layouts.admin')
@section('title', 'System Analytics')

@section('content')

<div class="flex items-center justify-between border-b pb-3 mb-6">
    <div>
        <span class="font-semibold text-xl">System analytics</span>
        <span class="text-sm text-gray-500 ml-2">Usage over the last {{ $days }} days</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.dashboard') }}" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">Dashboard</a>
        <a href="{{ route('admin.analytics.overview') }}" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">Student analytics</a>
    </div>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white border rounded-lg p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Active users today</p>
        <p class="text-3xl font-bold">{{ $active_today }}</p>
        @if($active_change_pct !== null)
            <p class="text-xs mt-1 {{ $active_change_pct >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $active_change_pct >= 0 ? '▲' : '▼' }} {{ abs($active_change_pct) }}% vs yesterday
            </p>
        @endif
    </div>
    <div class="bg-white border rounded-lg p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Content actions today</p>
        <p class="text-3xl font-bold">{{ $content_today }}</p>
        <p class="text-[11px] text-gray-400 mt-1">posts, replies, quizzes, messages</p>
        @if($content_change_pct !== null)
            <p class="text-xs mt-1 {{ $content_change_pct >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $content_change_pct >= 0 ? '▲' : '▼' }} {{ abs($content_change_pct) }}% vs yesterday
            </p>
        @endif
    </div>
    <div class="bg-white border rounded-lg p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Warned users</p>
        <p class="text-3xl font-bold text-amber-500">{{ $total_warned }}</p>
        <p class="text-[11px] text-gray-400 mt-1">{{ $total_warnings }} warnings issued total</p>
    </div>
    <div class="bg-white border rounded-lg p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Blacklisted</p>
        <p class="text-3xl font-bold text-red-600">{{ $total_blacklisted }}</p>
    </div>
</div>

<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Daily active users</p>
        <canvas id="activeChart" height="200"></canvas>
    </div>
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Content activity (posts, replies, quizzes, messages)</p>
        <canvas id="contentChart" height="200"></canvas>
    </div>
</div>

<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">New signups</p>
        <canvas id="signupChart" height="180"></canvas>
    </div>
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Warnings issued</p>
        <canvas id="warningChart" height="180"></canvas>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Users by role</p>
        <canvas id="roleChart" height="200"></canvas>
    </div>
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Moderation status</p>
        <canvas id="statusChart" height="200"></canvas>
    </div>
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Most flagged users</p>
        @if($top_flagged->isEmpty())
            <p class="text-sm text-gray-400">No users have been warned.</p>
        @else
            <div class="space-y-2">
                @foreach($top_flagged as $u)
                <div class="flex items-center justify-between text-sm">
                    <span>{{ $u->first_name }} {{ $u->last_name }}</span>
                    <span class="text-xs {{ $u->status === 'blacklisted' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700' }} px-2 py-0.5 rounded">
                        {{ $u->warning_count }} {{ Str::plural('warning', $u->warning_count) }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lineOpts = { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } };

    new Chart(document.getElementById('activeChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($active_trend['labels']) !!},
            datasets: [{
                label: 'Active users',
                data: {!! json_encode($active_trend['counts']) !!},
                borderColor: '#534AB7',
                backgroundColor: 'rgba(83,74,183,0.12)',
                tension: 0.3,
                fill: true,
            }],
        },
        options: lineOpts,
    });

    new Chart(document.getElementById('contentChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($content_trend['labels']) !!},
            datasets: [{
                label: 'Actions',
                data: {!! json_encode($content_trend['counts']) !!},
                backgroundColor: '#378ADD',
            }],
        },
        options: lineOpts,
    });

    new Chart(document.getElementById('signupChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($signup_trend['labels']) !!},
            datasets: [{
                label: 'Signups',
                data: {!! json_encode($signup_trend['counts']) !!},
                backgroundColor: '#1D9E75',
            }],
        },
        options: lineOpts,
    });

    new Chart(document.getElementById('warningChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($warning_trend['labels']) !!},
            datasets: [{
                label: 'Warnings',
                data: {!! json_encode($warning_trend['counts']) !!},
                backgroundColor: '#E24B4A',
            }],
        },
        options: lineOpts,
    });

    new Chart(document.getElementById('roleChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($role_distribution['labels']) !!},
            datasets: [{
                data: {!! json_encode($role_distribution['counts']) !!},
                backgroundColor: ['#534AB7', '#378ADD', '#BA7517'],
            }],
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($status_distribution['labels']) !!},
            datasets: [{
                data: {!! json_encode($status_distribution['counts']) !!},
                backgroundColor: ['#1D9E75', '#BA7517', '#E24B4A', '#9b9a96'],
            }],
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
    });
});
</script>

@endsection
