@extends('layouts.app')

@section('title', 'Research Interests')

@section('content')
    <section class="auth-panel profile-panel">
        <p class="eyebrow">Profile</p>
        <h1>Research interests</h1>
        <p class="muted">Tune the starting point for your dashboard.</p>

        <form method="POST" action="{{ route('profile.preferences.update') }}" class="form-stack">
            @csrf
            @method('PUT')

            <label>Field or discipline
                <input type="text" name="field_of_study" value="{{ old('field_of_study', $user->field_of_study) }}" placeholder="Public health, civil engineering, literature">
            </label>

            <label>Topics you care about
                <textarea name="interest_keywords" rows="4" placeholder="Separate topics with commas">{{ old('interest_keywords', $user->interest_keywords) }}</textarea>
            </label>

            @if ($categories->isNotEmpty())
                <fieldset class="checkbox-grid">
                    <legend>Areas</legend>
                    @foreach ($categories as $category)
                        <label>
                            <input type="checkbox" name="preferred_categories[]" value="{{ $category->id }}" @checked(in_array($category->id, old('preferred_categories', $user->preferredCategoryIds())))>
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </fieldset>
            @endif

            <div class="form-actions">
                <button class="button" type="submit">Save interests</button>
                <a class="button button-ghost" href="{{ route('dashboard') }}">Back to dashboard</a>
            </div>
        </form>
    </section>
@endsection
