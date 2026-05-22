@extends('layouts.app')

@section('title', 'Submit Report')

@section('content')
<div class="container-fluid p-5 pb-4 px-4">
    <h2 class="text-primary">Submit New Report</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="resident_name" class="form-label"><strong>Resident Name</strong></label>
            <input type="text" name="resident_name" id="resident_name"
                   class="form-control bg-secondary" value="{{ old('resident_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label"><strong>Category</strong></label>
            <select name="category_id" id="category_id" class="form-select bg-secondary">
                <option value="">— Select a category —</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="subject" class="form-label"><strong>Subject</strong></label>
            <input type="text" name="subject" id="subject"
                   class="form-control bg-secondary" value="{{ old('subject') }}" required>
        </div>

        <div class="mb-3 pb-2">
            <label for="description" class="form-label"><strong>Description</strong></label>
            <textarea name="description" id="description"
                      class="form-control bg-secondary" rows="7" required>{{ old('description') }}</textarea>
        </div>

        {{-- Status is intentionally omitted — new reports always start as Pending --}}

        <div class="mb-3">
            <label for="attachments" class="form-label"><strong>Attachments (optional)</strong></label>
            <input type="file" name="attachments[]" id="attachments" class="form-control bg-secondary" multiple>
            <small class="text-muted">You can upload multiple files (jpg, png, pdf). Max 2MB each.</small>
        </div>

        <button type="submit" class="btn btn-success">Submit</button>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection