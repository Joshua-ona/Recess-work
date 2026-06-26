@extends('layouts.app')

@section('title', 'Upload Quiz')

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar', [
        'role' => 'lecturer',
        'user' => auth()->user()
    ])

    <div class="dash-main">

        <h2>Upload Quiz CSV</h2>

        @if(session('success'))
            <div>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lecturer.quizzes.upload.submit') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <input type="file"
                   name="csv_file"
                   accept=".csv"
                   required>

            <button type="submit">
                Upload
            </button>

        </form>

    </div>

</div>

@endsection