@extends('layouts.app')

@section('title', 'Quiz Management')

@section('body')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Quiz Management</h2>

        <a href="{{ route('lecturer.quizzes.create') }}"
           class="btn btn-primary">

            + Create Quiz

        </a>

    </div>

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif

    <div class="card shadow">

        <div class="card-body">

            @if($quizzes->count())

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th>Title</th>
                            <th>Category</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    @foreach($quizzes as $quiz)

                        <tr>

                            <td>{{ $quiz->title }}</td>

                            <td>{{ $quiz->target_category }}</td>

                            <td>{{ $quiz->start_time }}</td>

                            <td>{{ $quiz->duration_mins }} mins</td>

                            <td>

                                @if($quiz->is_published)

                                    <span class="badge bg-success">

                                        Published

                                    </span>

                                @else

                                    <span class="badge bg-secondary">

                                        Draft

                                    </span>

                                @endif

                            </td>

                            <td>

                                <a href="{{ route('lecturer.quizzes.edit',$quiz->quiz_id) }}"
                                   class="btn btn-warning btn-sm">

                                    Edit

                                </a>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            @else

                <p>No quizzes have been created yet.</p>

            @endif

        </div>

    </div>

</div>

@endsection
