@extends('layouts.admin')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/admin/art/create/create.css')
@endpush

@section('title', 'Create Artwork - Admin - MET Museum')
@section('page_title', 'Create Artwork')

@section('content')
<div class="admin-container">
    <div class="admin-header">
        <h1>Add New Artwork</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>⚠ Please fix the following errors:</strong>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('admin.art.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information -->
            <div class="section-title">Basic Information</div>

            <div class="form-group @error('title') error @enderror">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                @error('title') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group @error('description') error @enderror">
                <label for="description">Description*</label>
                <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                @error('description') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Classification -->
            <div class="section-title">Classification</div>

            <div class="form-group grid-2">
                <div class="field @error('department_id') error @enderror">
                    <label for="department_id">Department *</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" @selected(old('department_id') == $dept->department_id)>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="field @error('object_type_id') error @enderror">
                    <label for="object_type_id">Object Type *</label>
                    <select id="object_type_id" name="object_type_id" required>
                        <option value="">Select Type</option>
                        @foreach($types as $type)
                            <option value="{{ $type->type_id }}" @selected(old('object_type_id') == $type->type_id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('object_type_id') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Location -->
            <div class="section-title">Location</div>

            <div class="form-group grid-2">
                <div class="field @error('geo_location_id') error @enderror">
                    <label for="geo_location_id">Geographic Location</label>
                    <select id="geo_location_id" name="geo_location_id">
                        <option value="">Select Geographic Location</option>
                        @foreach($geoLocations as $geo)
                            <option value="{{ $geo->geo_id }}" @selected(old('geo_location_id') == $geo->geo_id)>
                                {{ $geo->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('geo_location_id') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="field @error('location_id') error @enderror">
                    <label for="location_id">Museum Location</label>
                    <select id="location_id" name="location_id">
                        <option value="">Select Museum Location</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->location_id }}" @selected(old('location_id') == $loc->location_id)>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Dating -->
            <div class="section-title">Dating</div>

            <div class="form-group grid-2">
                <div class="field @error('year_start') error @enderror">
                    <label for="year_start">Year Start</label>
                    <input type="number" id="year_start" name="year_start" value="{{ old('year_start') }}" min="0" max="9999">
                    @error('year_start') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="field @error('year_end') error @enderror">
                    <label for="year_end">Year End</label>
                    <input type="number" id="year_end" name="year_end" value="{{ old('year_end') }}" min="0" max="9999">
                    @error('year_end') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Accession -->
            <div class="section-title">Accession Information</div>

            <div class="form-group @error('object_number') error @enderror">
                <label for="object_number">Object Number *</label>
                <input type="text" id="object_number" name="object_number" value="{{ old('object_number') }}" required>
                <small class="form-helper-text">Must be unique. Example: 1998.242</small>
                @error('object_number') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Images -->
            <div class="section-title">Images</div>

            <div class="form-group @error('images') error @enderror">
                <label>Upload Images</label>
                <div class="file-upload-area" id="uploadArea">
                    <div class="file-upload-icon">📸</div>
                    <div class="file-upload-text">
                        <p class="mb-0-5rem">Drag images here or click to select</p>
                        <small class="text-muted-light">JPEG, PNG, GIF - Max 5MB per image</small>
                    </div>
                </div>
                <input type="file" id="images" name="images[]" class="file-upload-input" multiple accept="image/*">
                <div id="imagePreview" class="image-preview-grid"></div>
                @error('images') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary">Create Artwork</button>
                <a href="{{ route('admin.art.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Handle file upload area
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');

    // Click to upload
    uploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        displayPreview();
    });

    // File selection
    fileInput.addEventListener('change', displayPreview);

    function displayPreview() {
        imagePreview.innerHTML = '';
        const files = fileInput.files;

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const item = document.createElement('div');
                item.className = 'image-preview-item';
                item.innerHTML = `<img src="${e.target.result}" alt="Preview ${index + 1}">`;
                imagePreview.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
