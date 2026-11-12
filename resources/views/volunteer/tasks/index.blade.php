@extends('layouts.app')

@section('title', 'Volunteer tasks')

@section('nav')
@include('partials.volunteer_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Pickup tasks</h1>
</div>

<p class="small text-muted">CLAIMED listings waiting for pickup. Mark complete when food is delivered.</p>

<div class="card page-card">
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Food</th>
                    <th>NGO</th>
                    <th>Donor</th>
                    <th>Pickup</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr>
                        <td>{{ $task->title }} <span class="badge badge-claimed">{{ $task->status }}</span></td>
                        <td class="small">{{ $task->claimedBy->ngo_name ?? $task->claimedBy->name ?? '—' }}</td>
                        <td class="small">{{ $task->donor->business_name ?? $task->donor->name }}</td>
                        <td class="small">{{ $task->pickup_address }}</td>
                        <td>
                            <form method="POST" action="{{ url('/volunteer/tasks/'.$task->id.'/complete') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-brand" data-confirm="Mark this pickup as completed (DELIVERED)?">Complete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted p-3">No active pickup tasks.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
