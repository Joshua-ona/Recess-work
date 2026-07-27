@extends('layouts.admin')
@section('title', 'Student Analytics')

@section('content')

<div class="flex items-center justify-between border-b pb-3 mb-6">
    <div>
        <span class="font-semibold text-xl">Student analytics</span>
        <span class="text-sm text-gray-500 ml-2">Performance & engagement across every student</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.dashboard') }}"
            class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">Dashboard</a>
        <a href="{{ route('admin.analytics') }}" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">System
            analytics</a>
    </div>
</div>

<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white border rounded-lg p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total students</p>
        <p class="text-3xl font-bold">{{ $total_students }}</p>
    </div>
    <div class="bg-white border rounded-lg p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Platform-wide avg quiz score</p>
        <p class="text-3xl font-bold">{{ $system_avg_pct }}%</p>
    </div>
    <div class="bg-white border rounded-lg p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Quiz submissions, last 14 days</p>
        <p class="text-3xl font-bold">{{ array_sum($submission_trend['counts']) }}</p>
    </div>
</div>

<div class="bg-white border rounded-lg p-5 mb-6">
    <p class="font-semibold mb-3">Quiz submissions per day</p>
    <canvas id="submissionChart" height="160"></canvas>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Top performers</p>
        @if($top_performers->isEmpty())
        <p class="text-sm text-gray-400">No quiz data yet.</p>
        @else
        <div class="space-y-2">
            @foreach($top_performers as $s)
            <a href="{{ route('admin.analytics.student', $s['id']) }}"
                class="flex items-center justify-between text-sm hover:bg-gray-50 rounded px-1 py-1">
                <span>{{ $s['name'] }}</span>
                <span class="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded">{{ $s['avg_pct'] }}%</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Needs attention</p>
        @if($needs_attention->isEmpty())
        <p class="text-sm text-gray-400">No quiz data yet.</p>
        @else
        <div class="space-y-2">
            @foreach($needs_attention as $s)
            <a href="{{ route('admin.analytics.student', $s['id']) }}"
                class="flex items-center justify-between text-sm hover:bg-gray-50 rounded px-1 py-1">
                <span>{{ $s['name'] }}</span>
                <span class="text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded">{{ $s['avg_pct'] }}%</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
    <div class="bg-white border rounded-lg p-5">
        <p class="font-semibold mb-3">Most active in discussions</p>
        @if($most_active->isEmpty())
        <p class="text-sm text-gray-400">No discussion activity yet.</p>
        @else
        <div class="space-y-2">
            @foreach($most_active as $s)
            <a href="{{ route('admin.analytics.student', $s['id']) }}"
                class="flex items-center justify-between text-sm hover:bg-gray-50 rounded px-1 py-1">
                <span>{{ $s['name'] }}</span>
                <span
                    class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded">{{ $s['discussions'] + $s['replies'] }}
                    posts</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="bg-white border rounded-lg p-5">
    <p class="font-semibold mb-3">Leaderboard</p>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-400 text-left">
                    <th class="pb-2 font-medium">#</th>
                    <th class="pb-2 font-medium">Student</th>
                    <th class="pb-2 font-medium">Score</th>
                    <th class="pb-2 font-medium">Avg quiz %</th>
                    <th class="pb-2 font-medium">Quizzes</th>
                    <th class="pb-2 font-medium">Discussions</th>
                    <th class="pb-2 font-medium">Replies</th>
                    <th class="pb-2 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($leaderboard as $i => $s)
                <tr>
                    <td class="py-2 text-gray-400">{{ $i + 1 }}</td>
                    <td class="py-2">{{ $s['name'] }}</td>
                    <td class="py-2 font-medium">{{ $s['score'] }}</td>
                    <td class="py-2">{{ $s['avg_pct'] !== null ? $s['avg_pct'].'%' : '—' }}</td>
                    <td class="py-2 text-gray-500">{{ $s['quizzes_taken'] }}</td>
                    <td class="py-2 text-gray-500">{{ $s['discussions'] }}</td>
                    <td class="py-2 text-gray-500">{{ $s['replies'] }}</td>
                    <td class="py-2 text-right">
                        <a href="{{ route('admin.analytics.student', $s['id']) }}" class="text-indigo-600 text-xs">View
                            →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-4 text-center text-gray-400">No students yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('submissionChart'), {
        type: 'bar',
        data: {
            labels: {
                !!json_encode($submission_trend['labels']) !!
            },
            datasets: [{
                label: 'Submissions',
                data: {
                    !!json_encode($submission_trend['counts']) !!
                },
                backgroundColor: '#534AB7',
            }],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
        },
    });
});
</script>

@endsection