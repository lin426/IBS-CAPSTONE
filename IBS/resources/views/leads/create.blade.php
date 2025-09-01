@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add New Lead</h2>

    <form action="{{ route('leads.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Contact</label>
            <input type="email" name="contact" class="form-control" placeholder="example@gmail.com" required>
        </div>

        <div class="mb-3">
            <label>Stage</label>
            <select name="stage" class="form-control" required>
                <option value="Prospect">Prospect</option>
                <option value="Contacted">Contacted</option>
                <option value="Proposal">Proposal</option>
                <option value="Closed">Closed</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Value</label>
            <input type="number" step="0.01" name="value" class="form-control">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="open">Open</option>
                <option value="won">Won</option>
                <option value="lost">Lost</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Save Lead</button>
        <a href="{{ route('leads.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
