@extends('admin.layouts.layout')

@section('styles')
<style>
    .psychology-settings .nav-tabs { flex-wrap: wrap; row-gap: .35rem; }
    .psychology-settings .form-control,
    .psychology-settings .form-select,
    .psychology-settings textarea { max-width: 100%; }
</style>
@endsection

@section('content')
<div class="main-content app-content psychology-settings">
    <div class="container-fluid">
        <div class="my-4 page-header-breadcrumb d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="page-title fw-medium fs-18 mb-2">Psychology Settings</h1>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Tools Management</li>
                        <li class="breadcrumb-item">Psychology</li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3 mb-3">
            @foreach ($tools as $tool)
                <div class="col-md-4">
                    <div class="card custom-card h-100 mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                @if ($tool->icon)<i class="{{ $tool->icon }}" aria-hidden="true"></i>@endif
                                <span class="fw-semibold">{{ $tool->name }}</span>
                            </div>
                            <div class="small text-muted">{{ $tool->statusLabel() }} · {{ $tool->canLaunch() ? 'Public' : 'Not publicly available' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="post" action="{{ route('admin.psychology.settings.update') }}">
            @csrf
            @method('PUT')
            <input id="settings-tab" type="hidden" name="tab" value="{{ $tab }}">

            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($tab === 'mood') active @endif" id="mood-tab" data-bs-toggle="tab" data-bs-target="#mood-pane" data-tab="mood" type="button" role="tab">Mood Check-in</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($tab === 'journal') active @endif" id="journal-tab" data-bs-toggle="tab" data-bs-target="#journal-pane" data-tab="journal" type="button" role="tab">Digital Journal</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($tab === 'gratitude') active @endif" id="gratitude-tab" data-bs-toggle="tab" data-bs-target="#gratitude-pane" data-tab="gratitude" type="button" role="tab">Gratitude Journal</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade @if($tab === 'mood') show active @endif" id="mood-pane" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header"><div class="card-title mb-0">Fixed check-in rules</div></div>
                        <div class="card-body">
                            <ul class="mb-0">
                                @foreach ($notes['mood'] as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card custom-card">
                        <div class="card-header"><div class="card-title mb-0">Primary moods</div></div>
                        <div class="card-body">
                            <p class="text-muted small">Five moods stay available. You can change the display wording and order. A check-in still stores exactly one of them.</p>
                            <div class="row g-2">
                                @foreach ($moods as $mood)
                                    <div class="col-md-6 col-xl-4">
                                        <label class="form-label" for="mood-{{ $mood->item_key }}">{{ $mood->label }}</label>
                                        <input id="mood-{{ $mood->item_key }}" class="form-control" name="moods[{{ $mood->item_key }}][label]" value="{{ old('moods.'.$mood->item_key.'.label', $mood->label) }}" maxlength="60" required>
                                        <input class="form-control mt-1" type="number" name="moods[{{ $mood->item_key }}][display_order]" value="{{ old('moods.'.$mood->item_key.'.display_order', $mood->display_order) }}" min="0" max="9999" aria-label="Display order for {{ $mood->label }}" required>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card custom-card">
                        <div class="card-header"><div class="card-title mb-0">Emotions</div></div>
                        <div class="card-body">
                            <p class="text-muted small">Members can choose up to six emotions in total. The groups stay Positive, Neutral, and Difficult.</p>
                            <div class="row g-3">
                                @foreach ($buckets as $bucket => $title)
                                    <div class="col-lg-4">
                                        <div class="border rounded p-3 h-100">
                                            <h2 class="fs-6 fw-semibold">{{ $title }}</h2>
                                            @foreach ($emotions->where('bucket', $bucket) as $emotion)
                                                <label class="form-label mt-2" for="emotion-{{ $emotion->item_key }}">{{ $emotion->label }}</label>
                                                <input id="emotion-{{ $emotion->item_key }}" class="form-control" name="emotions[{{ $emotion->item_key }}][label]" value="{{ old('emotions.'.$emotion->item_key.'.label', $emotion->label) }}" maxlength="60" required>
                                                <input type="hidden" name="emotions[{{ $emotion->item_key }}][display_order]" value="{{ old('emotions.'.$emotion->item_key.'.display_order', $emotion->display_order) }}">
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade @if($tab === 'journal') show active @endif" id="journal-pane" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header"><div class="card-title mb-0">Entry rules</div></div>
                        <div class="card-body">
                            <ul class="mb-0">
                                @foreach ($notes['journal'] as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card custom-card">
                        <div class="card-header"><div class="card-title mb-0">Writing prompts</div></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Display text</th>
                                            <th style="width: 7rem;">Order</th>
                                            <th style="width: 7rem;">Active</th>
                                            <th style="width: 8rem;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($prompts as $prompt)
                                            <tr>
                                                <td>
                                                    <label class="visually-hidden" for="prompt-{{ $prompt->item_key }}">{{ $prompt->label }}</label>
                                                    <input id="prompt-{{ $prompt->item_key }}" class="form-control" name="prompts[{{ $prompt->item_key }}][label]" value="{{ old('prompts.'.$prompt->item_key.'.label', $prompt->label) }}" maxlength="120" required>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="number" name="prompts[{{ $prompt->item_key }}][display_order]" value="{{ old('prompts.'.$prompt->item_key.'.display_order', $prompt->display_order) }}" min="0" max="9999" aria-label="Order for {{ $prompt->label }}" required>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="prompts[{{ $prompt->item_key }}][is_active]" value="0">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="prompts[{{ $prompt->item_key }}][is_active]" value="1" @checked(filter_var(old('prompts.'.$prompt->item_key.'.is_active', $prompt->is_active), FILTER_VALIDATE_BOOLEAN))>
                                                        <label class="form-check-label">On</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    @unless ($prompt->is_system)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="remove_prompts[]" value="{{ $prompt->id }}" id="remove-prompt-{{ $prompt->id }}">
                                                            <label class="form-check-label" for="remove-prompt-{{ $prompt->id }}">Remove</label>
                                                        </div>
                                                    @endunless
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td>
                                                <label class="form-label" for="new-prompt">Add a prompt</label>
                                                <input id="new-prompt" class="form-control" name="new_prompt[label]" value="{{ old('new_prompt.label') }}" maxlength="120" placeholder="Future prompt">
                                            </td>
                                            <td>
                                                <label class="form-label" for="new-prompt-order">Order</label>
                                                <input id="new-prompt-order" class="form-control" type="number" name="new_prompt[display_order]" value="{{ old('new_prompt.display_order', 20) }}" min="0" max="9999">
                                            </td>
                                            <td colspan="2" class="text-muted small">Added prompts can be turned off or removed. The six documented prompts stay.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade @if($tab === 'gratitude') show active @endif" id="gratitude-pane" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header"><div class="card-title mb-0">Three-card rules</div></div>
                        <div class="card-body">
                            <ul class="mb-0">
                                @foreach ($notes['gratitude'] as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="row g-3">
                        @foreach ($cards as $card)
                            <div class="col-lg-4">
                                <div class="card custom-card h-100 mb-0">
                                    <div class="card-header"><div class="card-title mb-0">Card {{ $loop->iteration }}</div></div>
                                    <div class="card-body">
                                        <label class="form-label" for="card-label-{{ $card->item_key }}">Title</label>
                                        <input id="card-label-{{ $card->item_key }}" class="form-control mb-2" name="cards[{{ $card->item_key }}][label]" value="{{ old('cards.'.$card->item_key.'.label', $card->label) }}" maxlength="80" required>
                                        <label class="form-label" for="card-hint-{{ $card->item_key }}">Guidance</label>
                                        <textarea id="card-hint-{{ $card->item_key }}" class="form-control mb-2" name="cards[{{ $card->item_key }}][hint]" rows="3" maxlength="400" required>{{ old('cards.'.$card->item_key.'.hint', $card->hint) }}</textarea>
                                        <label class="form-label" for="card-detail-{{ $card->item_key }}">Question</label>
                                        <textarea id="card-detail-{{ $card->item_key }}" class="form-control" name="cards[{{ $card->item_key }}][detail]" rows="3" maxlength="400" required>{{ old('cards.'.$card->item_key.'.detail', $card->detail) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 my-4">
                <button class="btn btn-primary" type="submit">Save Changes</button>
                <a class="btn btn-light" href="{{ route('admin.tool-settings.edit') }}">Tool availability</a>
            </div>
        </form>
    </div>
</div>
<script>
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tab) {
    tab.addEventListener('shown.bs.tab', function (event) {
        var field = document.getElementById('settings-tab');
        if (field) {
            field.value = event.target.getAttribute('data-tab');
        }
    });
});
</script>
@endsection
