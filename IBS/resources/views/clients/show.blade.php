@extends('layouts.app')
<h1>{{ $client->name }}</h1>
<p>Email: {{ $client->email }}</p>
<a href="{{ route('clients.index') }}">🔙 Back to List</a>
