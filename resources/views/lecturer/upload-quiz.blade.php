@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Upload Quiz CSV</h2>

    <form action="{{ route('quizzes.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="file" name="file" accept=".csv" required>

        <button type="submit">Upload Quiz</button>
    </form>
</div>
@endsection