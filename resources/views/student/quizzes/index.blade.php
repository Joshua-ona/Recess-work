@extends('layouts.app')

@section('title', 'Available Quizzes')

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar',[
        'role'=>'student',
        'user'=>auth()->user()
    ])

    <div class="dash-main">

        <div class="dash-header">
            <div>
                <div class="dash-header-title">Available quizzes</div>
                <div class="dash-header-sub">
                    {{ $quizzes->count() }} {{ Str::plural('quiz', $quizzes->count()) }} published
                </div>
            </div>
        </div>

        <div class="dash-body">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($quizzes->isEmpty())
                <div class="full-panel">
                    <div class="panel-body" style="text-align:center; padding:2.5rem 1rem;">
                        <i class="ti ti-clipboard-list" style="font-size:32px;color:var(--hint);" aria-hidden="true"></i>
                        <p style="color:var(--muted); margin-top:10px;">No quizzes available right now.</p>
                    </div>
                </div>
            @else
                <div class="full-panel">
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Start time</th>
                                    <th>Duration</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quizzes as $quiz)
                                    @php
                                        $opensAt = \Carbon\Carbon::parse($quiz->start_time);
                                        $isLocked = now()->lessThan($opensAt);
                                    @endphp
                                    <tr>
                                        <td style="font-weight:500;">{{ $quiz->title }}</td>
                                        <td>{{ $opensAt->format('M j, Y g:i A') }}</td>
                                        <td>
                                            <span class="badge badge-purple">
                                                <i class="ti ti-clock" aria-hidden="true"></i>&nbsp;{{ $quiz->duration_mins }} mins
                                            </span>
                                        </td>
                                        <td>
                                            @if($isLocked)
                                                <span class="btn btn-outline btn-sm" style="width:auto; cursor:not-allowed; opacity:.7;" title="Opens {{ $opensAt->format('M j, Y g:i A') }}">
                                                    <i class="ti ti-lock" aria-hidden="true"></i>&nbsp;Opens {{ $opensAt->diffForHumans() }}
                                                </span>
                                            @else
                                                <a href="{{ route('student.quizzes.attempt', $quiz->quiz_id) }}"
                                                   class="btn btn-primary btn-sm" style="width:auto;">
                                                    Start quiz <i class="ti ti-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

@endsection
