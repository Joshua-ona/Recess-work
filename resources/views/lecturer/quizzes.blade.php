@extends('layouts.app')

@section('title', 'Quiz Management')

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar', [
        'role' => 'lecturer',
        'user' => auth()->user()
    ])

    <div class="dash-main">

        <h2>Quiz Management</h2>

        <a href="{{ route('lecturer.quizzes.upload') }}"
           class="btn btn-primary">
            Upload Quiz CSV
        </a>

    </div>

</div>

@endsection