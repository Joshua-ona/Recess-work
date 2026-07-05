@extends('layouts.app')

@section('title','Attempt Quiz')

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar',[
        'role'=>'student',
        'user'=>auth()->user()
    ])

    <div class="dash-main">

        <div class="container-fluid">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h4 class="mb-0">{{ $quiz->title }}</h4>
                            <small>{{ $quiz->course->course_name ?? '' }}</small>
                        </div>

                        <div>

                            <h5 id="timer" class="mb-0">
                                01:30:00
                            </h5>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="mb-4">

                        <div class="progress">

                            <div class="progress-bar"
                                 style="width:{{ ($currentQuestion/$totalQuestions)*100 }}%">
                            </div>

                        </div>

                        <small>
                            Question {{ $currentQuestion }}
                            of
                            {{ $totalQuestions }}
                        </small>

                    </div>

                   <form method="POST" action="{{ route('student.quizzes.answer', [
                        'quiz' => $quiz->quiz_id,
                        'question' => $question->question_id
                    ]) }}">
                        @csrf

                        <h4 class="mb-4">

                            {{ $question->question }}

                        </h4>

                        @foreach($question->options as $option)

                            <div class="form-check border rounded p-3 mb-3">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="answer"
                                    value="{{ $option->id }}"
                                    id="option{{ $option->id }}">

                                <label
                                    class="form-check-label ms-2"
                                    for="option{{ $option->id }}">

                                    {{ $option->option_text }}

                                </label>

                            </div>

                        @endforeach

                        <div class="d-flex justify-content-between mt-5">

                            @if($previousQuestion)

                                <a
                                   href="{{ route('student.quizzes.attempt',[$quiz->id,$previousQuestion]) }}"
                                   class="btn btn-secondary">

                                    Previous

                                </a>

                            @else

                                <button
                                    class="btn btn-secondary"
                                    disabled>

                                    Previous

                                </button>

                            @endif

                            <button
                                class="btn btn-warning">

                                Save & Next

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>
<script>

let seconds = 5400;

setInterval(function(){

    let hrs=Math.floor(seconds/3600);

    let mins=Math.floor((seconds%3600)/60);

    let secs=seconds%60;

    document.getElementById('timer').innerHTML=

        String(hrs).padStart(2,'0')+":"+

        String(mins).padStart(2,'0')+":"+

        String(secs).padStart(2,'0');

    seconds--;

},1000);

</script>
<style>
    .option-box{

padding:18px;

border:1px solid #ddd;

border-radius:12px;

margin-bottom:15px;

cursor:pointer;

transition:.2s;

}

.option-box:hover{

background:#eef6ff;

border-color:#0d6efd;

}

.progress{

height:10px;

}

.question-nav{

display:flex;

flex-wrap:wrap;

gap:10px;

}

.question-nav button{

width:45px;

height:45px;

font-weight:bold;

border-radius:50%;

}
</style>

@endsection
