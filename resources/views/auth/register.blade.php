@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <section class="auth-panel register-panel">
        <h1>Create account</h1>
        <p class="muted">Add a few research signals so your dashboard starts with better matches.</p>

        <form method="POST" action="{{ route('register') }}" class="form-stack">
            @csrf
            @if (request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif
            <label>Name
                <input type="text" name="name" value="{{ old('name') }}" required autofocus>
            </label>
            <label>Email
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <label>Confirm password
                <input type="password" name="password_confirmation" required>
            </label>
            <label>Field or discipline
                <input type="text" name="field_of_study" value="{{ old('field_of_study') }}" placeholder="Public health, civil engineering, literature">
            </label>
            <label>Topics you care about
                <textarea name="interest_keywords" rows="3" placeholder="Separate topics with commas">{{ old('interest_keywords') }}</textarea>
            </label>
            @if ($categories->isNotEmpty())
                <fieldset class="checkbox-grid">
                    <legend>Starting areas</legend>
                    @foreach ($categories as $category)
                        <label>
                            <input type="checkbox" name="preferred_categories[]" value="{{ $category->id }}" @checked(in_array($category->id, old('preferred_categories', [])))>
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </fieldset>
            @endif
            <button class="button" type="submit">Create account</button>
        </form>
    </section>
@endsection
