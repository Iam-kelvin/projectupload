@php
    $cloudUpload = $cloudUpload ?? null;
@endphp

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
        <p class="muted">Add new tags below to attach them here.</p>
    @endif
</fieldset>

<label class="full-span">Add new tags
    <input type="text" name="new_tags" value="{{ old('new_tags') }}" placeholder="Separate new tags with commas, for example: Folklore, Maternal Health, Textile Design">
    <span class="help-text">Use this when none of the existing tags fit. New tags are saved for future uploads.</span>
</label>

<label class="full-span">PDF file
    <input
        type="file"
        name="pdf_file"
        accept="application/pdf,.pdf"
        @required(! $project && ! old('cloud_pdf_url'))
        @if ($cloudUpload)
            data-direct-file
        @endif
    >
    @if ($project?->hasPdf())
        <span class="help-text">Current file: {{ $project->pdfDisplayName() }}</span>
    @else
        <span class="help-text">PDFs up to 50 MB are accepted here. The app stores the file path and extracted text, not the full PDF inside the database.</span>
    @endif
</label>

@if ($cloudUpload)
    <div
        class="full-span upload-status"
        data-direct-upload
        data-handle-url="{{ $cloudUpload['handleUrl'] }}"
        data-access="{{ $cloudUpload['access'] }}"
        data-client-payload="{{ $cloudUpload['intent'] }}"
        data-max-bytes="{{ $cloudUpload['maxBytes'] }}"
        hidden
    ></div>
    <input type="hidden" name="cloud_pdf_url" data-cloud-field="url" value="{{ old('cloud_pdf_url') }}">
    <input type="hidden" name="cloud_pdf_download_url" data-cloud-field="downloadUrl" value="{{ old('cloud_pdf_download_url') }}">
    <input type="hidden" name="cloud_pdf_storage_key" data-cloud-field="pathname" value="{{ old('cloud_pdf_storage_key') }}">
    <input type="hidden" name="cloud_pdf_original_name" data-cloud-field="originalName" value="{{ old('cloud_pdf_original_name') }}">
    <input type="hidden" name="cloud_pdf_mime" data-cloud-field="mime" value="{{ old('cloud_pdf_mime') }}">
    <input type="hidden" name="cloud_pdf_size" data-cloud-field="size" value="{{ old('cloud_pdf_size') }}">
    <input type="hidden" name="cloud_file_hash" data-cloud-field="hash" value="{{ old('cloud_file_hash') }}">
@endif
