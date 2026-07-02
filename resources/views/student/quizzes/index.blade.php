@extends('layouts.app')

@section('title', 'Available Quizzes')

@section('body')

<div class="container mt-4">

    <h2>Available Quizzes</h2>

    @if($quizzes->isEmpty())
        <p>No quizzes available.</p>
    @else

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>Title</th>
                    <th>Start Time</th>
                    <th>Duration</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            @foreach($quizzes as $quiz)

                <tr>

                    <td>{{ $quiz->title }}</td>

                    <td>{{ $quiz->start_time }}</td>

                    <td>{{ $quiz->duration_mins }} mins</td>

                    <td>
                        <a href="#" class="btn btn-primary">
                            Start Quiz
                        </a>
                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    @endif

</div>

@endsection