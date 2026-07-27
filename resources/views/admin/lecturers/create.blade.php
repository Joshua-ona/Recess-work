@extends('layouts.app')
@section('title', 'Add a lecturer')
@section('body')

<div class="dash-wrap">
    @include('layouts.sidebar', [
    'role' => 'system_admin',
    'user' => auth()->user(),
    ])
    <div class="dash-main">
        <div class="dash-header">
            <div class="dash-header-title">Add a lecturer</div>
        </div>

        <div class="dash-body" style="flex-direction:column;">

            <div class="panel" style="max-width:480px;">
                <div class="panel-body">
                    <p style="font-size:13px; color:var(--muted); margin-bottom:1.25rem;">
                        This creates the account and emails the lecturer an activation
                        link. They'll set their own password and won't be able to sign
                        in until they do.
                    </p>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.lecturers.store') }}">
                        @csrf

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="first_name">First name</label>
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                                    class="form-control" required autofocus>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="last_name">Last name</label>
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                                    class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control"
                                placeholder="lecturer@university.ac.ug" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Send invitation
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection