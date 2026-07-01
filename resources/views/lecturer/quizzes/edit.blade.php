@extends('layouts.app')

@section('title', 'Edit Quiz')

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar', [
        'role' => 'lecturer',
        'user' => auth()->user()
    ])

    <div class="dash-main">

        <div class="dash-header mb-4">
            <div>
                <div class="text-muted small">
                    Java Quiz Edit Page
                </div>

                <h1 class="fw-bold mt-2">
                    Edit & Schedule Quiz
                </h1>

                <p class="text-muted">
                    Configure your assessment settings, define your target audience,
                    and manage your examination.
                </p>
            </div>
        </div>

        <div class="dash-body">

<form method="POST"
      action="{{ route('lecturer.quizzes.update',$quiz->quiz_id) }}">

@csrf
@method('PUT')



<div class="panel mb-4">

    <div class="panel-head">
        <h3 class="panel-title">
            <i class="ti ti-file-text"></i>
            Quiz Essentials
        </h3>
    </div>

    <div class="panel-body">

        <div class="form-group mb-3">

            <label class="form-label">
                Quiz Title
            </label>

            <input
                type="text"
                name="title"
                class="form-control"
                value="{{ old('title',$quiz->title) }}"
                placeholder="Quiz title">

        </div>

        <div class="form-group mb-3">

            <label class="form-label">
                Target Student Category
            </label>

            <select
                name="target_category"
                class="form-control">

                <option value="Level 100"
                    {{ $quiz->target_category=='Level 100'?'selected':'' }}>
                    Undergraduate - Java Students
                </option>

                <option value="Level 200"
                    {{ $quiz->target_category=='Level 200'?'selected':'' }}>
                    Undergraduate - Data Structures Students
                </option>

                <option value="Level 300"
                    {{ $quiz->target_category=='Level 300'?'selected':'' }}>
                    Undergraduate - Algorithms Students
                </option>

                <option value="Masters"
                    {{ $quiz->target_category=='Masters'?'selected':'' }}>
                    Masters
                </option>

            </select>

        </div>

        <div class="form-group">

            <label class="form-label">
                Quiz Duration
            </label>

            <div class="d-flex align-items-center">

                <input
                    type="number"
                    name="duration_mins"
                    class="form-control"
                    value="{{ old('duration_mins',$quiz->duration_mins) }}">

                <span class="ms-2">
                    minutes
                </span>

            </div>

        </div>

    </div>

</div>



<div class="panel mb-4">

    <div class="panel-head">

        <h3 class="panel-title">

            <i class="ti ti-calendar-event"></i>

            Schedule

        </h3>

    </div>

    <div class="panel-body">

        <div class="form-group mb-3">

            <label>

                Start Date & Time

            </label>

            <input
                type="datetime-local"
                name="starts_at"
                class="form-control"
                value="{{ old('starts_at',$quiz->starts_at) }}">

        </div>

        <div class="form-group">

            <label>

                End Date & Time

            </label>

            <input
                type="datetime-local"
                name="ends_at"
                class="form-control"
                value="{{ old('ends_at',$quiz->ends_at) }}">

        </div>

    </div>

</div>



<div class="panel mb-4">

    <div class="panel-head">

        <h3 class="panel-title">

            <i class="ti ti-shield-lock"></i>

            Security

        </h3>

    </div>

    <div class="panel-body">

        <div class="form-check mb-2">

            <input
                type="checkbox"
                class="form-check-input"
                name="secure_mode"
                value="1"
                {{ $quiz->secure_mode ? 'checked' : '' }}>

            <label class="form-check-label">

                Secure Exam Mode

            </label>

        </div>

        <div class="form-check">

            <input
                type="checkbox"
                class="form-check-input"
                name="shuffle_questions"
                value="1"
                {{ $quiz->shuffle_questions ? 'checked' : '' }}>

            <label class="form-check-label">

                Shuffle Questions

            </label>

        </div>

    </div>

</div>



<div class="panel mb-4"
     style="background:#f5f3ff;border-left:5px solid #5b50d6;">

    <div class="panel-body">

        <h4
            style="color:#4338ca;font-weight:700;">

            Complexity Review

        </h4>

        <p class="text-muted mb-0">

            Students note that the current Quiz will contribute 20% to your course work results.
            Therefore attempt them with maximum desire.

        </p>

    </div>

</div>


<div class="panel mb-4">

    <div class="panel-head d-flex justify-content-between align-items-center">

        <h3 class="panel-title">
            <i class="ti ti-list-check"></i>
            Question Builder
        </h3>

        <div>

           <a href="{{ route('lecturer.quizzes.upload.form', $quiz) }}"
               class="btn btn-outline-primary">

                <i class="ti ti-file-upload"></i>
                Import CSV

            </a>

            <button
                type="button"
                id="addQuestion"
                class="btn btn-primary">

                <i class="ti ti-plus"></i>
                New Question

            </button>

        </div>

    </div>

    <div class="panel-body">

        <div id="questionContainer">

            @forelse($quiz->questions as $index=>$question)

            <div class="question-card card shadow-sm mb-4">

                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                   <strong class="question-number">
                         MCQ #{{ $index+1 }}
                    </strong>
                    <div>

                        <button
                            type="button"
                            class="btn btn-sm btn-light">

                            <i class="ti ti-edit"></i>

                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-danger remove-question">

                            <i class="ti ti-trash"></i>

                        </button>

                    </div>

                </div>

                <div class="card-body">

                    <div class="form-group mb-3">

                        <label>

                            Question

                        </label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="questions[{{ $index }}][question]">{{ $question->question }}</textarea>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label>Option A</label>

                            <input
                                type="text"
                                class="form-control"
                                name="questions[{{ $index }}][option_a]"
                                value="{{ $question->option_a }}">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Option B</label>

                            <input
                                type="text"
                                class="form-control"
                                name="questions[{{ $index }}][option_b]"
                                value="{{ $question->option_b }}">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Option C</label>

                            <input
                                type="text"
                                class="form-control"
                                name="questions[{{ $index }}][option_c]"
                                value="{{ $question->option_c }}">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Option D</label>

                            <input
                                type="text"
                                class="form-control"
                                name="questions[{{ $index }}][option_d]"
                                value="{{ $question->option_d }}">

                        </div>

                    </div>

                    <div class="form-group">

                        <label>

                            Correct Answer

                        </label>

                        <select
                            class="form-control"
                            name="questions[{{ $index }}][correct_answer]">

                            <option value="A" {{ $question->correct_answer=="A" ? "selected":"" }}>A</option>
                            <option value="B" {{ $question->correct_answer=="B" ? "selected":"" }}>B</option>
                            <option value="C" {{ $question->correct_answer=="C" ? "selected":"" }}>C</option>
                            <option value="D" {{ $question->correct_answer=="D" ? "selected":"" }}>D</option>

                        </select>

                    </div>

                </div>

            </div>

            @empty

            <div
                id="emptyQuestions"
                class="text-center p-5 border rounded bg-light">

                <i
                    class="ti ti-help-circle"
                    style="font-size:60px;color:#6b46c1;"></i>

                <h4 class="mt-3">

                    No Questions Added Yet

                </h4>

                <p class="text-muted">

                    Click <strong>New Question</strong> or import a CSV.

                </p>

            </div>

            @endforelse

        </div>

        <div
            class="border rounded mt-4 p-4 text-center"
            style="border-style:dashed !important;">

            <i
                class="ti ti-plus"
                style="font-size:40px;color:#6b46c1;"></i>

            <h5 class="mt-2">

                Add Question to this Thread

            </h5>

            <p class="text-muted">

                Press the New Question button above.

            </p>

        </div>

    </div>

</div>


<div class="d-flex justify-content-between mb-5">

    <button
        type="submit"
        name="draft"
        class="btn btn-outline-secondary">

        <i class="ti ti-device-floppy"></i>

        Save as Draft

    </button>

    <div>

        <a
            href="{{ route('lecturer.quizzes') }}"
            class="btn btn-light">

            Cancel

        </a>

        <button
            class="btn btn-primary"
            type="submit">

            <i class="ti ti-calendar-event"></i>

            Schedule Quiz

        </button>

    </div>

</div>

</form>
<script>

let questionIndex = {{ $quiz->questions->count() }};

const container = document.getElementById('questionContainer');

const addButton = document.getElementById('addQuestion');

function updateNumbers() {

    document.querySelectorAll('.question-card').forEach((card,index)=>{

        card.querySelector('.question-number').innerHTML =
            'MCQ #' + (index + 1);

    });

}

addButton.addEventListener('click',function(){

    const empty=document.getElementById('emptyQuestions');

    if(empty){

        empty.remove();

    }

    const html=`

<div class="question-card card shadow-sm mb-4">

<div class="card-header bg-white d-flex justify-content-between align-items-center">

<strong class="question-number">

MCQ #${questionIndex+1}

</strong>

<button
type="button"
class="btn btn-danger btn-sm remove-question">

<i class="ti ti-trash"></i>

</button>

</div>

<div class="card-body">

<div class="form-group mb-3">

<label>Question</label>

<textarea
class="form-control"
rows="3"
name="questions[${questionIndex}][question]"
placeholder="Enter question"></textarea>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label>Option A</label>

<input
class="form-control"
name="questions[${questionIndex}][option_a]">

</div>

<div class="col-md-6 mb-3">

<label>Option B</label>

<input
class="form-control"
name="questions[${questionIndex}][option_b]">

</div>

<div class="col-md-6 mb-3">

<label>Option C</label>

<input
class="form-control"
name="questions[${questionIndex}][option_c]">

</div>

<div class="col-md-6 mb-3">

<label>Option D</label>

<input
class="form-control"
name="questions[${questionIndex}][option_d]">

</div>

</div>

<div class="form-group">

<label>Correct Answer</label>

<select
class="form-control"
name="questions[${questionIndex}][correct_answer]">

<option value="">Choose...</option>
<option value="A">A</option>
<option value="B">B</option>
<option value="C">C</option>
<option value="D">D</option>

</select>

</div>

</div>

</div>

`;

    container.insertAdjacentHTML('beforeend',html);

    questionIndex++;

    updateNumbers();

});

document.addEventListener('click',function(e){

    const btn=e.target.closest('.remove-question');

    if(!btn) return;

    btn.closest('.question-card').remove();

    updateNumbers();

});

</script>

@endsection