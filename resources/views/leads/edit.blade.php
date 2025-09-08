@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Lead</h2>

    <form action="{{ route('leads.update', $lead->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ $lead->name }}" class="form-control" required>
        </div>

<div class="mb-3">
    <label class="text-white">Contact (Gmail)</label>
    <input type="email" name="contact" class="form-control" value="{{ old('contact', $lead->contact) }}" required>
</div>

        <div class="mb-3">
            <label>Stage</label>
            <select name="stage" class="form-control" required>
                @foreach(['Prospect', 'Contacted', 'Proposal', 'Closed'] as $stage)
                    <option value="{{ $stage }}" {{ $lead->stage == $stage ? 'selected' : '' }}>{{ $stage }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Value</label>
            <input type="number" step="0.01" name="value" value="{{ $lead->value }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                @foreach(['open', 'won', 'lost'] as $status)
                    <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Lead</button>
        <a href="{{ route('leads.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
