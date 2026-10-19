<table class="table table-bordered">
    <tr>
        {{-- ===== LEFT SIDE: ALL DETAILS (60%) ===== --}}
        <td style="width: 60%; vertical-align: top;">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td style="width: 35%; font-weight: 600;">{{ _lang('Branch Name') }}</td>
                    <td>{{ $branch->name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">{{ _lang('District') }}</td>
                    <td>{{ $branch->district }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">{{ _lang('State') }}</td>
                    <td>{{ $branch->state }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">{{ _lang('Pincode') }}</td>
                    <td>{{ $branch->pincode }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">{{ _lang('Contact Email') }}</td>
                    <td>{{ $branch->contact_email }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">{{ _lang('Contact Phone') }}</td>
                    <td>{{ $branch->contact_phone }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">{{ _lang('Address') }}</td>
                    <td>{{ $branch->address }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">{{ _lang('Descriptions') }}</td>
                    <td>{{ $branch->descriptions }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">{{ _lang('Agreements') }}</td>
                    <td>
                        @if ($branch->agreements)
                            @php $files = json_decode($branch->agreements, true) ?? []; @endphp
                            @if (!empty($files))
                                <ul class="list-unstyled mb-0">
                                    @foreach ($files as $file)
                                        <li>
                                            <a href="{{ asset('public/uploads/media/' . $file) }}"
                                                target="_blank"
                                                class="btn btn-xs btn-outline-primary my-1">
                                                <i class="ti-download"></i> {{ $file }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">{{ _lang('No Agreement Uploaded') }}</span>
                            @endif
                        @else
                            <span class="text-muted">{{ _lang('No Agreement Uploaded') }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </td>

        {{-- ===== RIGHT SIDE: BIG BRANCH IMAGE (40%) ===== --}}
        <td style="width: 40%; vertical-align: top; text-align: center;">
            <div style="font-weight: 600; margin-bottom: 8px;">{{ _lang('Branch Image') }}</div>
            @if ($branch->branch_image)
                <img src="{{ asset('public/uploads/media/' . $branch->branch_image) }}"
                    class="img-thumbnail"
                    style="width: 100%; max-width: 350px; height: auto; object-fit: cover;">
            @else
                <span class="text-muted">{{ _lang('No Branch Image') }}</span>
            @endif
        </td>
    </tr>
</table>