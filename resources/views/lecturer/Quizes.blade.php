@extends('layouts.lecturer')

@section('content')

<div class="flex justify-between items-center mb-4">
    <h2>Quizzes</h2>

    <a href="{{ route('quizzes.upload') }}"
       class="btn btn-primary">
       Upload Quiz CSV
    </a>
</div>

<!-- Existing quiz list/table goes here -->
<div>
    {{-- your quizzes table or cards --}}
</div>

@endsection