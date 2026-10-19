@extends('layouts.app')

@section('title', 'Sidebar Menu Permissions')

@section('content')

    <div class="row">
        <div class="col-lg-10 offset-lg-1">

            {{-- Page Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1" style="font-weight:700; color:#3d5af1;">
                        <i class="fas fa-bars me-2"></i> Sidebar Menu Permissions
                    </h4>
                    <p class="text-muted mb-0" style="font-size:13px;">
                        Control which sidebar menu items each role can see. Admin always sees everything.
                    </p>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Role Selector Card --}}
            <div class="card shadow-sm mb-4" style="border-radius:12px; border:none;">
                <div class="card-body">
                    <div class="d-flex align-items-end gap-3">
                        <div class="flex-grow-1">
                            <label class="form-label fw-semibold mb-1">Select Role</label>
                            <select id="roleSelector" class="form-select" onchange="loadRole()">
                                <option value="">-- Select a Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary" onclick="loadRole()">
                                <i class="fas fa-sync-alt me-1"></i> Load
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Permission Form --}}
            @if ($role_id)
                <form method="POST" action="{{ route('sidebar.permissions.store') }}">
                    @csrf
                    <input type="hidden" name="role_id" value="{{ $role_id }}">

                    @foreach ($menuMap as $section)
                        <div class="card shadow-sm mb-4" style="border-radius:12px; border:none;">

                            {{-- Section Header --}}
                            <div class="card-header d-flex align-items-center justify-content-between"
                                style="background: linear-gradient(135deg, #3d5af1 0%, #5c77ff 100%);
                            border-radius:12px 12px 0 0; padding:14px 20px;">
                                <span style="color:#fff; font-weight:700; font-size:15px; letter-spacing:0.3px;">
                                    <i class="fas fa-shield-alt me-2"></i> {{ $section['section'] }}
                                </span>
                                {{-- Select All toggle --}}
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input section-select-all" type="checkbox"
                                        id="select_all_{{ $loop->index }}" data-section="{{ $loop->index }}"
                                        style="width:40px; height:20px; cursor:pointer;"
                                        {{ collect($section['items'])->every(fn($item) => in_array($item['key'], $assignedKeys)) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white ms-1" for="select_all_{{ $loop->index }}"
                                        style="font-size:12px; font-weight:500;">
                                        Select All
                                    </label>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="list-group list-group-flush" style="border-radius: 0 0 12px 12px;">
                                    @foreach ($section['items'] as $item)
                                        <label
                                            class="list-group-item list-group-item-action d-flex align-items-center gap-3 px-4 py-3"
                                            style="cursor:pointer; border-left:none; border-right:none; transition:background 0.15s;"
                                            onmouseover="this.style.background='#f0f4ff'"
                                            onmouseout="this.style.background='#fff'">

                                            <input
                                                class="form-check-input section-item-{{ $loop->parent->index }} flex-shrink-0"
                                                type="checkbox" name="menu_keys[]" value="{{ $item['key'] }}"
                                                style="width:18px; height:18px; accent-color:#3d5af1; cursor:pointer;"
                                                {{ in_array($item['key'], $assignedKeys) ? 'checked' : '' }}>

                                            <div>
                                                <div style="font-weight:600; font-size:14px; color:#1a1a2e;">
                                                    {{ $item['label'] }}
                                                </div>
                                                <div
                                                    style="font-size:11px; color:#9098a9; font-family:monospace; margin-top:2px;">
                                                    key: <span style="color:#3d5af1;">{{ $item['key'] }}</span>
                                                </div>
                                            </div>

                                            {{-- Status badge --}}
                                            @if (in_array($item['key'], $assignedKeys))
                                                <span class="ms-auto badge"
                                                    style="background:#e8f5e9; color:#27ae60; font-size:11px; padding:4px 10px; border-radius:20px;">
                                                    <i class="fas fa-eye me-1"></i> Visible
                                                </span>
                                            @else
                                                <span class="ms-auto badge"
                                                    style="background:#fff3e0; color:#e67e22; font-size:11px; padding:4px 10px; border-radius:20px;">
                                                    <i class="fas fa-eye-slash me-1"></i> Hidden
                                                </span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Save Button --}}
                    <div class="d-flex justify-content-end mb-5">
                        <button type="submit" class="btn btn-primary btn-lg px-5"
                            style="background: linear-gradient(135deg, #3d5af1, #5c77ff);
                               border:none; border-radius:10px; font-weight:600;
                               box-shadow: 0 4px 15px rgba(61,90,241,0.35);">
                            <i class="fas fa-save me-2"></i> Save Permissions
                        </button>
                    </div>
                </form>
            @else
                {{-- Empty state --}}
                <div class="card shadow-sm" style="border-radius:12px; border:none;">
                    <div class="card-body text-center py-5">
                        <div style="font-size:60px; mb-3;">🔐</div>
                        <h5 class="mt-3 text-muted">Select a role above to manage its sidebar permissions.</h5>
                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection

@section('js-script')
    <script>
        console.log("Sidebar Permission JS Loaded");

        // ─── Role Selector ───────────────────────────────────────────
        function loadRole() {
            var roleId = document.getElementById('roleSelector').value;
            console.log("Selecting Role ID:", roleId);

            if (!roleId) {
                console.log("No role selected, redirecting to index...");
                window.location.href = '{{ route('sidebar.permissions.index') }}';
                return;
            }

            var urlTemplate = '{{ route('sidebar.permissions.index', ['role_id' => ':id']) }}';
            var targetUrl = urlTemplate.replace(':id', roleId);

            console.log("Redirecting to:", targetUrl);
            window.location.href = targetUrl;
        }

        // Failsafe: Try to attach event listener too if DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            var selector = document.getElementById('roleSelector');
            if (selector) {
                console.log("Attaching event listener to roleSelector");
                selector.addEventListener('change', loadRole);
            } else {
                console.error("roleSelector NOT found in DOM!");
            }
        });



        // ─── Select All toggle per section ──────────────────────────
        document.querySelectorAll('.section-select-all').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const sectionIndex = this.dataset.section;
                document.querySelectorAll('.section-item-' + sectionIndex).forEach(function(cb) {
                    cb.checked = toggle.checked;
                    // Update badge
                    const badge = cb.closest('label').querySelector('.badge');
                    if (badge) {
                        if (toggle.checked) {
                            badge.style.background = '#e8f5e9';
                            badge.style.color = '#27ae60';
                            badge.innerHTML = '<i class="fas fa-eye me-1"></i> Visible';
                        } else {
                            badge.style.background = '#fff3e0';
                            badge.style.color = '#e67e22';
                            badge.innerHTML = '<i class="fas fa-eye-slash me-1"></i> Hidden';
                        }
                    }
                });
            });
        });

        // ─── Update badge on individual checkbox change ──────────────
        document.querySelectorAll('input[name="menu_keys[]"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const badge = this.closest('label').querySelector('.badge');
                if (badge) {
                    if (this.checked) {
                        badge.style.background = '#e8f5e9';
                        badge.style.color = '#27ae60';
                        badge.innerHTML = '<i class="fas fa-eye me-1"></i> Visible';
                    } else {
                        badge.style.background = '#fff3e0';
                        badge.style.color = '#e67e22';
                        badge.innerHTML = '<i class="fas fa-eye-slash me-1"></i> Hidden';
                    }
                }
            });
        });
    </script>
@endsection
