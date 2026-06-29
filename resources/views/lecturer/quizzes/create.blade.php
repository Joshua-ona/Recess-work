@extends('layouts.app')

@section('title', 'Create Quiz')

@section('body')

<div class="dash-body">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Create Quiz</h2>
            <p>Fill in the quiz details below.</p>
        </div>

        <a href="{{ route('lecturer.quizzes') }}" class="btn btn-outline">
            Return
        </a>


    <form action="{{ route('lecturer.quizzes.store') }}"
          method="POST">

        @csrf

        <div class="panel">

            <div class="panel-head">

                <div class="panel-title">

                    Quiz Information

                </div>

            </div>

            <div class="panel-body">

                <div class="form-group">

                    <label class="form-label">

                        Quiz Title

                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        required>

                </div>

                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">

                            Quiz ID

                        </label>

                        <input
                            type="number"
                            name="group_id"
                            class="form-control"
                            required>

                    </div>

                    <div class="form-group">

                        <label class="form-label">

                            Category

                        </label>

                        <input
                            type="text"
                            name="target_category"
                            class="form-control"
                            required>

                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">

                        <label class="form-label">

                            Start Time

                        </label>

                        <input
                            type="datetime-local"
                            name="start_time"
                            class="form-control"
                            required>

                    </div>

                    <div class="form-group">

                        <label class="form-label">

                            Duration (minutes)

                        </label>

                        <input
                            type="number"
                            name="duration_mins"
                            class="form-control"
                            required>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary">

                    Save Quiz

                </button>

            </div>

        </div>

    </form>

</div>

@endsection