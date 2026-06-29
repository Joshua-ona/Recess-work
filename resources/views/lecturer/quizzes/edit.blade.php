@extends('layouts.app')

@section('title','Edit Quiz')

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar',[
        'role'=>'lecturer',
        'user'=>auth()->user()
    ])

    <div class="dash-main">

        <div class="dash-header">

            <div>

                <p>{{ $quiz->title }}</p>

            </div>

        </div>

        <div class="dash-body">

        </div>

    </div>

</div>
<div class="panel">

    <div class="panel-head">

        <h3 class="panel-title">
            Quiz Information
        </h3>
        <div class="panel mt-4">

    <div class="panel-head">
        <h3 class="panel-title">
            Import Questions
        </h3>
    </div>

    <div class="panel-body">

        <form
            method="POST"
            action="{{ route('lecturer.quizzes.upload',$quiz->quiz_id) }}"
            enctype="multipart/form-data">

            @csrf

            <input
                type="file"
                name="csv_file"
                class="form-control">

            <br>

            <button
                class="btn btn-primary">

                Upload CSV

            </button>

        </form>

    </div>

</div>

    </div>

    <div class="panel-body">

        <form>

            <div class="form-group">

                <label>Quiz Title</label>

                <input
                    type="text"
                    class="form-control"
                    value="{{ $quiz->title }}">

            </div>

            <div class="form-group">

                <label>Category</label>

                <input
                    type="text"
                    class="form-control"
                    value="{{ $quiz->target_category }}">

            </div>

            <div class="form-group">

                <label>Duration</label>

                <input
                    type="number"
                    class="form-control"
                    value="{{ $quiz->duration_mins }}">

            </div>

        </form>

    </div>

</div>
<div class="panel mt-3">

    <div class="panel-head">

        <h3 class="panel-title">

            Schedule

        </h3>

    </div>

    <div class="panel-body">

        <label>Start Time</label>

        <input
            type="datetime-local"
            class="form-control">

    </div>

</div>
<div class="panel mt-3">

    <div class="panel-head">

        <h3 class="panel-title">

            Security

        </h3>

    </div>

    <div class="panel-body">

        <label>

            <input type="checkbox">

            Secure Mode

        </label>

        <br>

        <label>

            <input type="checkbox">

            Shuffle Questions

        </label>

    </div>

</div>
<div class="panel mt-3">

    <div class="panel-head">

        <h3 class="panel-title">

            Question Builder

        </h3>

    </div>

    <div class="panel-body">

        <a
            href="{{ route('lecturer.quizzes.upload') }}"
            class="btn btn-outline">

            Import CSV

        </a>

        <button
            class="btn btn-primary">

            + New Question

        </button>

    </div>

</div>
@foreach($quiz->questions as $question)

<div class="panel mt-3">

    <div class="panel-body">

        <strong>

            {{ $question->question }}

        </strong>

        <hr>

        A. {{ $question->option_a }}<br>

        B. {{ $question->option_b }}<br>

        C. {{ $question->option_c }}<br>

        D. {{ $question->option_d }}

        <div class="alert alert-success mt-2">
            <strong>Correct Answer:</strong>
            {{ $question->correct_answer }}
        </div>


    </div>


</div>

@endforeach

@endsection
