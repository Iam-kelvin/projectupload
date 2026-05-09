<label>Title
    <input type="text" name="title" value="{{ old('title', $project?->title) }}" required>
</label>

<label>Author / Student
    <input type="text" name="student_name" value="{{ old('student_name', $project?->student_name) }}" required>
</label>

<label>Supervisor
    <input type="text" name="supervisor" value="{{ old('supervisor', $project?->supervisor) }}">
</label>

<label>Project type
    <input type="text" name="project_type" value="{{ old('project_type', $project?->project_type) }}" placeholder="Research, Case Study, Design, Policy, Creative Work">
</label>

<label>Completion year
    <input type="number" name="completion_year" value="{{ old('completion_year', $project?->completion_year) }}" min="1900" max="{{ now()->year + 1 }}" required>
</label>

<label>Category
    <select name="category_id">
        <option value="">Uncategorized</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $project?->category_id) === (string) $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</label>

<label class="full-span">Abstract
    <textarea name="abstract" rows="6">{{ old('abstract', $project?->abstract) }}</textarea>
</label>

<label class="full-span">Keywords
    <input type="text" name="keywords" value="{{ old('keywords', $project?->keywords) }}" placeholder="Comma-separated keywords">
</label>

<fieldset class="full-span checkbox-grid">
    <legend>Tags</legend>
    @foreach ($tags as $tag)
        <label>
            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tags', $project?->tags->pluck('id')->all() ?? [])))>
            <span>{{ $tag->name }}</span>
        </label>
    @endforeach
    @if ($tags->isEmpty())
        <p class="muted">Create tags from the Tags section to attach them here.</p>
    @endif
</fieldset>

<label class="full-span">Add new tags
    <input type="text" name="new_tags" value="{{ old('new_tags') }}" placeholder="Separate new tags with commas, for example: Folklore, Maternal Health, Textile Design">
    <span class="help-text">Use this when none of the existing tags fit. New tags are saved for future uploads.</span>
</label>

<label class="full-span">PDF file
    <input type="file" name="pdf_file" accept="application/pdf,.pdf" @required(! $project)>
    @if ($project?->pdfAbsolutePath())
        <span class="help-text">Current file: {{ $project->pdfDisplayName() }}</span>
    @endif
</label>
