@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ _lang('Knowledge List') }}</h4>
                    <a href="{{ route('admin.knowledge-base.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ _lang('Add New Knowledge') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ _lang('Title') }}</th>
                                    <th>{{ _lang('Category') }}</th>
                                    <th>{{ _lang('Description') }}</th>
                                    <th>{{ _lang('Images') }}</th>
                                    <th>{{ _lang('Status') }}</th>
                                    <th>{{ _lang('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($knowledge as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ ucfirst($item->category) }}</td>
                                        <td>{{ Str::limit($item->description, 50) }}</td>
                                        <td>
                                            @if($item->images && count($item->images) > 0)
                                                <span class="badge badge-info">{{ count($item->images) }} images</span>
                                            @else
                                                <span class="badge badge-secondary">{{ _lang('No images') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.knowledge.toggle-status', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ $item->is_active ? _lang('Active') : _lang('Inactive') }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <!-- ✅ View Button -->
                                            <a href="{{ route('admin.knowledge-base.show', $item) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="{{ _lang('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.knowledge-base.edit', $item) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="{{ _lang('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Delete Button -->
                                            <!--<form action="{{ route('admin.knowledge-base.destroy', $item) }}" -->
                                            <!--      method="POST" -->
                                            <!--      class="d-inline">-->
                                            <!--    @csrf-->
                                            <!--    @method('DELETE')-->
                                            <!--    <button type="submit" -->
                                            <!--            class="btn btn-sm btn-danger" -->
                                            <!--            onclick="return confirm('{{ _lang('Are you sure?') }}')"-->
                                            <!--            title="{{ _lang('Delete') }}">-->
                                            <!--        <i class="fas fa-trash"></i>-->
                                            <!--    </button>-->
                                            <!--</form>-->
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ _lang('No knowledge entries found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection