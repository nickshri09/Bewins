@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ _lang('Live Screen Banners') }}</h4>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">{{ _lang('Add Banner') }}</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ _lang('Banner') }}</th>
                            <th>{{ _lang('Title') }}</th>
                            <th>{{ _lang('Status') }}</th>
                            <th>{{ _lang('Updated') }}</th>
                            <th>{{ _lang('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banners as $banner)
                            <tr>
                                <td style="width: 180px">
                                    @if ($banner->image)
                                        <img src="{{ asset('uploads/live-banners/' . $banner->image) }}" alt="" class="img-fluid" style="max-height: 70px">
                                    @else
                                        <span class="text-muted">{{ _lang('Text banner') }}</span>
                                    @endif
                                </td>
                                <td>{{ $banner->title }}</td>
                                <td>
                                    @if ($banner->is_active)
                                        <span class="badge badge-success">{{ _lang('Active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ _lang('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $banner->updated_at?->format('d M Y, h:i A') }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-info">{{ _lang('Edit') }}</a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ _lang('Delete this banner?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">{{ _lang('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">{{ _lang('No banners created yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $banners->links() }}
        </div>
    </div>
@endsection
