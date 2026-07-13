@extends('layouts.app') {{-- or whatever layout you're using --}}

@section('content')
<div class="container">
    <h1>Available Quizzes</h1>
    
    @forelse($quizzes as $quiz)
        <div class="quiz-item">
            <h3>{{ $quiz->title }}</h3>
            <p>{{ $quiz->description }}</p>
            <a href="{{ route('student.quiz.start', $quiz->id) }}" class="btn btn-primary">
                Start Quiz
            </a>
        </div>
    @empty
        <p>No quizzes available.</p>
    @endforelse
</div>
@endsection