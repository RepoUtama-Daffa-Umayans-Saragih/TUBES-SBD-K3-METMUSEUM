@extends('admin.layout.layout')

@section('admin-title')
    {{ $title }}
@endsection

@section('admin-content')
<div class="admin-page-section">
    <div class="page-header">
        <h1>{{ $title }}</h1>
        <p class="page-subtitle">{{ $subtitle }}</p>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="{{ $isEdit ? route('admin.artworks.update', $artwork->art_work_id) : route('admin.artworks.store') }}" 
              method="POST" class="admin-form" id="artworkForm">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>⚠️ Validation Errors:</strong>
                    <ul style="margin: 0.5rem 0 0 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- SECTION 1: BASIC INFORMATION -->
            <div class="form-section">
                <h3 class="section-title">Basic Information</h3>

                <!-- MET Object ID -->
                <div class="form-group">
                    <label for="met_object_id" class="form-label">MET Object ID <span class="required">*</span></label>
                    <input type="number" id="met_object_id" name="met_object_id" class="form-control @error('met_object_id') is-invalid @enderror"
                        placeholder="Enter MET Museum object ID"
                        value="{{ old('met_object_id', $artwork?->met_object_id ?? '') }}" required>
                    @error('met_object_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label">Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                        placeholder="Enter artwork title"
                        value="{{ old('title', $artwork?->title ?? '') }}" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Accession Number & Year -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="accession_number" class="form-label">Accession Number <span class="required">*</span></label>
                        <input type="text" id="accession_number" name="accession_number" class="form-control @error('accession_number') is-invalid @enderror"
                            placeholder="e.g. 1997.219.4"
                            value="{{ old('accession_number', $artwork?->accession_number ?? '') }}" required>
                        @error('accession_number')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="accession_year" class="form-label">Accession Year</label>
                        <input type="number" id="accession_year" name="accession_year" class="form-control @error('accession_year') is-invalid @enderror"
                            placeholder="e.g. 1997"
                            value="{{ old('accession_year', $artwork?->accession_year ?? '') }}"
                            min="1000" max="2100">
                        @error('accession_year')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                        placeholder="Enter artwork description" rows="4">{{ old('description', $artwork?->description ?? '') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Gallery Number -->
                <div class="form-group">
                    <label for="gallery_number" class="form-label">Gallery Number</label>
                    <input type="text" id="gallery_number" name="gallery_number" class="form-control @error('gallery_number') is-invalid @enderror"
                        placeholder="Gallery location"
                        value="{{ old('gallery_number', $artwork?->gallery_number ?? '') }}">
                    @error('gallery_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- SECTION 2: DATING & DIMENSIONS -->
            <div class="form-section">
                <h3 class="section-title">Dating & Dimensions</h3>

                <!-- Object Date Display -->
                <div class="form-group">
                    <label for="object_date_display" class="form-label">Date Display (Text)</label>
                    <input type="text" id="object_date_display" name="object_date_display" class="form-control @error('object_date_display') is-invalid @enderror"
                        placeholder="e.g. ca. 1810"
                        value="{{ old('object_date_display', $artwork?->object_date_display ?? '') }}">
                    @error('object_date_display')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date Range -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="object_begin_date" class="form-label">Begin Date (Year)</label>
                        <input type="number" id="object_begin_date" name="object_begin_date" class="form-control @error('object_begin_date') is-invalid @enderror"
                            placeholder="Starting year"
                            value="{{ old('object_begin_date', $artwork?->object_begin_date ?? '') }}"
                            min="1000" max="2100">
                        @error('object_begin_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="object_end_date" class="form-label">End Date (Year)</label>
                        <input type="number" id="object_end_date" name="object_end_date" class="form-control @error('object_end_date') is-invalid @enderror"
                            placeholder="Ending year"
                            value="{{ old('object_end_date', $artwork?->object_end_date ?? '') }}"
                            min="1000" max="2100">
                        @error('object_end_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Dimensions -->
                <div class="form-group">
                    <label for="dimensions_display" class="form-label">Dimensions (Text)</label>
                    <textarea id="dimensions_display" name="dimensions_display" class="form-control @error('dimensions_display') is-invalid @enderror"
                        placeholder="e.g. H. 25 1/2 in. (64.8 cm)" rows="2">{{ old('dimensions_display', $artwork?->dimensions_display ?? '') }}</textarea>
                    @error('dimensions_display')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- SECTION 3: CLASSIFICATION & LOCATION -->
            <div class="form-section">
                <h3 class="section-title">Classification & Location</h3>

                <!-- Department -->
                <div class="form-group">
                    <label for="department_id" class="form-label">Department <span class="required">*</span></label>
                    <select id="department_id" name="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" 
                                {{ old('department_id', $artwork?->department_id) == $dept->department_id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Object Type -->
                <div class="form-group">
                    <label for="type_id" class="form-label">Object Type</label>
                    <select id="type_id" name="type_id" class="form-control @error('type_id') is-invalid @enderror">
                        <option value="">-- Select Object Type --</option>
                        @foreach($objectTypes as $type)
                            <option value="{{ $type->type_id }}"
                                {{ old('type_id', $artwork?->type_id) == $type->type_id ? 'selected' : '' }}>
                                {{ $type->object_type_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Classification -->
                <div class="form-group">
                    <label for="classification_id" class="form-label">Classification</label>
                    <select id="classification_id" name="classification_id" class="form-control @error('classification_id') is-invalid @enderror">
                        <option value="">-- Select Classification --</option>
                        @foreach($classifications as $classification)
                            <option value="{{ $classification->classification_id }}"
                                {{ old('classification_id', $artwork?->classification_id) == $classification->classification_id ? 'selected' : '' }}>
                                {{ $classification->classification_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('classification_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label for="location_id" class="form-label">Location</label>
                    <select id="location_id" name="location_id" class="form-control @error('location_id') is-invalid @enderror">
                        <option value="">-- Select Location --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->location_id }}"
                                {{ old('location_id', $artwork?->location_id) == $location->location_id ? 'selected' : '' }}>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Repository -->
                <div class="form-group">
                    <label for="repository_id" class="form-label">Repository</label>
                    <select id="repository_id" name="repository_id" class="form-control @error('repository_id') is-invalid @enderror">
                        <option value="">-- Select Repository --</option>
                        @foreach($repositories as $repo)
                            <option value="{{ $repo->repository_id }}"
                                {{ old('repository_id', $artwork?->repository_id) == $repo->repository_id ? 'selected' : '' }}>
                                {{ $repo->repository_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('repository_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Credit Line -->
                <div class="form-group">
                    <label for="credit_line_id" class="form-label">Credit Line</label>
                    <select id="credit_line_id" name="credit_line_id" class="form-control @error('credit_line_id') is-invalid @enderror">
                        <option value="">-- Select Credit Line --</option>
                        @foreach($creditLines as $credit)
                            <option value="{{ $credit->credit_line_id }}"
                                {{ old('credit_line_id', $artwork?->credit_line_id) == $credit->credit_line_id ? 'selected' : '' }}>
                                {{ $credit->credit_line_text }}
                            </option>
                        @endforeach
                    </select>
                    @error('credit_line_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- SECTION 4: PHYSICAL ATTRIBUTES -->
            <div class="form-section">
                <h3 class="section-title">Physical Attributes</h3>

                <!-- Materials -->
                <div class="form-group">
                    <label for="materials" class="form-label">Materials</label>
                    <select id="materials" name="materials[]" class="form-control @error('materials') is-invalid @enderror" 
                        multiple size="8">
                        @foreach($materials as $material)
                            <option value="{{ $material->material_id }}"
                                {{ in_array($material->material_id, old('materials', $artwork?->materials->pluck('material_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $material->material_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple</small>
                    @error('materials')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Mediums -->
                <div class="form-group">
                    <label for="mediums" class="form-label">Mediums</label>
                    <select id="mediums" name="mediums[]" class="form-control @error('mediums') is-invalid @enderror"
                        multiple size="8">
                        @foreach($mediums as $medium)
                            <option value="{{ $medium->medium_id }}"
                                {{ in_array($medium->medium_id, old('mediums', $artwork?->mediums->pluck('medium_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $medium->medium_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple</small>
                    @error('mediums')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- SECTION 5: CULTURAL & HISTORICAL CONTEXT -->
            <div class="form-section">
                <h3 class="section-title">Cultural & Historical Context</h3>

                <!-- Cultures -->
                <div class="form-group">
                    <label for="cultures" class="form-label">Cultures</label>
                    <select id="cultures" name="cultures[]" class="form-control @error('cultures') is-invalid @enderror"
                        multiple size="6">
                        @foreach($cultures as $culture)
                            <option value="{{ $culture->culture_id }}"
                                {{ in_array($culture->culture_id, old('cultures', $artwork?->cultures->pluck('culture_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $culture->culture_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl to select multiple</small>
                    @error('cultures')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Periods -->
                <div class="form-group">
                    <label for="periods" class="form-label">Periods</label>
                    <select id="periods" name="periods[]" class="form-control @error('periods') is-invalid @enderror"
                        multiple size="6">
                        @foreach($periods as $period)
                            <option value="{{ $period->period_id }}"
                                {{ in_array($period->period_id, old('periods', $artwork?->periods->pluck('period_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $period->period_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl to select multiple</small>
                    @error('periods')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Dynasties -->
                <div class="form-group">
                    <label for="dynasties" class="form-label">Dynasties</label>
                    <select id="dynasties" name="dynasties[]" class="form-control @error('dynasties') is-invalid @enderror"
                        multiple size="6">
                        @foreach($dynasties as $dynasty)
                            <option value="{{ $dynasty->dynasty_id }}"
                                {{ in_array($dynasty->dynasty_id, old('dynasties', $artwork?->dynasties->pluck('dynasty_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $dynasty->dynasty_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl to select multiple</small>
                    @error('dynasties')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Reigns -->
                <div class="form-group">
                    <label for="reigns" class="form-label">Reigns</label>
                    <select id="reigns" name="reigns[]" class="form-control @error('reigns') is-invalid @enderror"
                        multiple size="6">
                        @foreach($reigns as $reign)
                            <option value="{{ $reign->reign_id }}"
                                {{ in_array($reign->reign_id, old('reigns', $artwork?->reigns->pluck('reign_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $reign->reign_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl to select multiple</small>
                    @error('reigns')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tags -->
                <div class="form-group">
                    <label for="tags" class="form-label">Tags</label>
                    <select id="tags" name="tags[]" class="form-control @error('tags') is-invalid @enderror"
                        multiple size="6">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->tag_id }}"
                                {{ in_array($tag->tag_id, old('tags', $artwork?->tags->pluck('tag_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $tag->tag_term }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl to select multiple</small>
                    @error('tags')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Portfolios -->
                <div class="form-group">
                    <label for="portfolios" class="form-label">Portfolios</label>
                    <select id="portfolios" name="portfolios[]" class="form-control @error('portfolios') is-invalid @enderror"
                        multiple size="6">
                        @foreach($portfolios as $portfolio)
                            <option value="{{ $portfolio->portfolio_id }}"
                                {{ in_array($portfolio->portfolio_id, old('portfolios', $artwork?->portfolios->pluck('portfolio_id')->toArray() ?? [])) ? 'selected' : '' }}>
                                {{ $portfolio->portfolio_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl to select multiple</small>
                    @error('portfolios')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- SECTION 5A: CONSTITUENTS (ARTISTS & CONTRIBUTORS) -->
            <div class="form-section">
                <h3 class="section-title">Artists & Contributors</h3>
                <p style="color: #666; margin-bottom: 1rem;">Add artists, architects, photographers, and other contributors to this artwork.</p>

                <!-- Existing Constituents -->
                @if($isEdit && $artwork && $artwork->constituents->isNotEmpty())
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="margin-bottom: 1rem;">Current Contributors ({{ $artwork->constituents->count() }})</h4>
                        <div class="constituents-list">
                            @foreach($artwork->constituents as $constituent)
                                <div class="constituent-item">
                                    <div class="constituent-info">
                                        <strong>{{ $constituent->display_name }}</strong>
                                        @if($constituent->birth_year || $constituent->death_year)
                                            <small>({{ $constituent->birth_year ?? '?' }}-{{ $constituent->death_year ?? '?' }})</small>
                                        @endif
                                    </div>
                                    <div class="constituent-controls">
                                        <select class="form-control-small" style="width: 120px; display: inline-block;">
                                            <option value="">Role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->role_id }}" {{ $constituent->pivot->role_id == $role->role_id ? 'selected' : '' }}>
                                                    {{ $role->role_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn-small" onclick="alert('Constituent editing in form will be implemented. For now, use the show page.')">
                                            Edit
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add New Constituent -->
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
                    <h4 style="margin-bottom: 1rem;">Add Contributor</h4>
                    <div class="form-group">
                        <label for="new_constituent_id" class="form-label">Select Contributor</label>
                        <select id="new_constituent_id" name="new_constituent_id" class="form-control">
                            <option value="">-- Select a contributor --</option>
                            @foreach($constituents as $constituent)
                                <option value="{{ $constituent->constituent_id }}">
                                    {{ $constituent->display_name }}
                                    @if($constituent->birth_year || $constituent->death_year)
                                        ({{ $constituent->birth_year ?? '?' }}-{{ $constituent->death_year ?? '?' }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_constituent_role" class="form-label">Role</label>
                            <select id="new_constituent_role" name="new_constituent_role" class="form-control">
                                <option value="">-- No Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_constituent_prefix" class="form-label">Prefix</label>
                            <select id="new_constituent_prefix" name="new_constituent_prefix" class="form-control">
                                <option value="">-- No Prefix --</option>
                                @foreach($prefixes as $prefix)
                                    <option value="{{ $prefix->prefix_id }}">{{ $prefix->prefix_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_constituent_suffix" class="form-label">Suffix</label>
                            <select id="new_constituent_suffix" name="new_constituent_suffix" class="form-control">
                                <option value="">-- No Suffix --</option>
                                @foreach($suffixes as $suffix)
                                    <option value="{{ $suffix->suffix_id }}">{{ $suffix->suffix_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: ARTWORK IMAGES -->
            <div class="form-section">
                <h3 class="section-title">Artwork Images</h3>
                
                @if($isEdit && $artwork && $artwork->images->isNotEmpty())
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="margin-bottom: 1rem;">Existing Images</h4>
                        <div class="existing-images">
                            @foreach($artwork->images as $image)
                                <div class="image-item">
                                    <div class="image-preview">
                                        <img src="{{ $image->image_url }}" alt="Artwork image" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                    </div>
                                    <div class="image-info">
                                        <p style="margin: 0 0 0.5rem 0; word-break: break-all; font-size: 0.85rem;">{{ $image->image_url }}</p>
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            <label class="image-primary-checkbox">
                                                <input type="radio" name="primary_image_id" value="{{ $image->image_id }}" {{ $image->is_primary ? 'checked' : '' }}>
                                                Set as primary
                                            </label>
                                            <button type="button" class="btn-small btn-danger-small" onclick="if(confirm('Delete this image?')) { this.closest('.image-item').remove(); }">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add New Image -->
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0;">
                    <h4 style="margin-bottom: 1rem;">Add Image</h4>
                    <div class="form-group">
                        <label for="new_image_url" class="form-label">Image URL</label>
                        <input type="text" id="new_image_url" name="new_image_url" class="form-control"
                            placeholder="Enter image URL (e.g., https://...)">
                        <small class="form-text text-muted">Add the full URL to the image. Leave empty to skip.</small>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="new_image_primary" name="new_image_primary" class="form-check-input" value="1">
                        <label class="form-check-label" for="new_image_primary">
                            Set as primary image
                        </label>
                    </div>
                </div>
            </div>

            <!-- SECTION 7: ADDITIONAL INFORMATION -->
            <div class="form-section">
                <h3 class="section-title">Additional Information</h3>

                <!-- Provenance -->
                <div class="form-group">
                    <label for="provenance" class="form-label">Provenance</label>
                    <textarea id="provenance" name="provenance" class="form-control @error('provenance') is-invalid @enderror"
                        placeholder="Artwork ownership history" rows="3">{{ old('provenance', $artwork?->provenance ?? '') }}</textarea>
                    @error('provenance')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Rights & Reproduction -->
                <div class="form-group">
                    <label for="rights_and_reproduction" class="form-label">Rights & Reproduction</label>
                    <textarea id="rights_and_reproduction" name="rights_and_reproduction" class="form-control @error('rights_and_reproduction') is-invalid @enderror"
                        placeholder="Copyright and reproduction information" rows="3">{{ old('rights_and_reproduction', $artwork?->rights_and_reproduction ?? '') }}</textarea>
                    @error('rights_and_reproduction')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- SECTION 8: FLAGS -->
            <div class="form-section">
                <h3 class="section-title">Display Flags</h3>

                <!-- Flags -->
                <div class="form-flags">
                    <div class="form-check">
                        <input type="checkbox" id="is_on_view" name="is_on_view" class="form-check-input" value="1"
                            {{ old('is_on_view', $artwork?->is_on_view) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_on_view">
                            On View (Currently displayed in museum)
                        </label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="is_highlight" name="is_highlight" class="form-check-input" value="1"
                            {{ old('is_highlight', $artwork?->is_highlight) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_highlight">
                            Highlight (Featured artwork)
                        </label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="is_public_domain" name="is_public_domain" class="form-check-input" value="1"
                            {{ old('is_public_domain', $artwork?->is_public_domain) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_public_domain">
                            Public Domain (Allowed for reproduction)
                        </label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="is_timeline_work" name="is_timeline_work" class="form-check-input" value="1"
                            {{ old('is_timeline_work', $artwork?->is_timeline_work) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_timeline_work">
                            Timeline Work (Include in timeline)
                        </label>
                    </div>
                </div>
            </div>

            <!-- SECTION 9: SUBMIT BUTTONS -->
            <div class="form-section">
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">
                        {{ $isEdit ? '💾 Update Artwork' : '➕ Create Artwork' }}
                    </button>
                    <a href="{{ route('admin.artworks.index') }}" class="btn btn-secondary">
                        ← Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.admin-page-section {
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
}

.page-subtitle {
    font-size: 0.95rem;
    color: #666;
    margin: 0;
}

.form-container {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.admin-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-section {
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e0e0e0;
}

.form-section:last-child {
    border-bottom: none;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 1rem 0;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.required {
    color: #dc3545;
    font-weight: bold;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.75rem;
    font-size: 0.95rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-flags {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-check {
    display: flex;
    align-items: center;
}

.form-check-input {
    margin-right: 0.75rem;
    cursor: pointer;
    width: 18px;
    height: 18px;
}

.form-check-label {
    cursor: pointer;
    margin: 0;
}

.form-text {
    display: block;
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.25rem;
}

.form-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}

.btn-primary {
    background-color: #2196F3;
    color: white;
}

.btn-primary:hover {
    background-color: #1976D2;
}

.btn-secondary {
    background-color: #f0f0f0;
    color: #333;
}

.btn-secondary:hover {
    background-color: #e0e0e0;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert ul {
    padding-left: 1.25rem;
}

.alert li {
    margin-bottom: 0.25rem;
}

/* Image Management Styles */
.existing-images {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

.image-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    align-items: flex-start;
}

.image-preview {
    flex-shrink: 0;
    width: 150px;
    height: 150px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.image-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.image-info {
    flex-grow: 1;
    min-width: 0;
}

.image-primary-checkbox {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.5rem 0.75rem;
    background: #f0f0f0;
    border-radius: 4px;
    font-size: 0.9rem;
}

.image-primary-checkbox:hover {
    background: #e0e0e0;
}

.btn-small {
    padding: 0.4rem 0.75rem;
    font-size: 0.85rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}

.btn-danger-small {
    background-color: #dc3545;
    color: white;
}

.btn-danger-small:hover {
    background-color: #c82333;
}

/* Constituent Management Styles */
.constituents-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

.constituent-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    gap: 1rem;
}

.constituent-info {
    flex-grow: 1;
}

.constituent-info strong {
    display: block;
    color: #333;
    margin-bottom: 0.25rem;
}

.constituent-info small {
    color: #999;
}

.constituent-controls {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.form-control-small {
    padding: 0.4rem 0.5rem;
    font-size: 0.85rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .form-buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        text-align: center;
    }

    .image-item {
        flex-direction: column;
    }

    .image-preview {
        width: 100%;
    }
}
</style>
@endsection
