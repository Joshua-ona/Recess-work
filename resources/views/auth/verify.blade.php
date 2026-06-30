@extends('layouts.app')

@section('body')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{session('success')}}
                        </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{$error}}
                        @endforeach
                    </div>   
                    @endif
                    
                    <p> {{__('Please enter the 6 digit code sent to verifyyour email')}} </p>

                    <form class="d-inline" method="POST" action="{{ route('verification.check') }}">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="otp" class="form-control" maxlength="6" placeholder="Enter 6-digit verification code" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Verify</button>.
                    </form>

                    <hr>
                    <p> {{__('if you did not receive the code,')}}</p>
                    
                    <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{__('click here to request for another')}}</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
