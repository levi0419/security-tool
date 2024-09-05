@extends('layouts.app')

@section('title', 'Scan Results')

@section('content')
    <h1>Scan Results</h1>

    @if (!empty($results))
        @foreach ($results as $testName => $result)
            <h2>{{ $testName }}</h2>
            @if (is_array($result) || is_object($result))
                <pre>{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
            @else
                <pre>{{ $result }}</pre>
            @endif
        @endforeach
    @else
        <p>No results found.</p>
    @endif

    <a href="{{ url('/') }}" class="btn btn-primary">Scan Another URL</a>
    <a href="{{ route('download.results') }}" class="btn btn-secondary">Download as PDF</a>
@endsection
