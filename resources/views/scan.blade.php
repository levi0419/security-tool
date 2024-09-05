@extends('layouts.app')

@section('title', 'Start a Scan')

@section('content')
<div class="jumbotron">
    <h1>Start a New Scan</h1>
    <p>Enter the URL you want to scan for security vulnerabilities.</p>
</div>
<form method="POST" action="{{ url('/scan-test') }}">
    @csrf
    <div class="form-group">
        <label for="url">Enter URL:</label>
        <input type="text" id="url" name="url" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Start Scan</button>
</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.3/echo.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    // Initialize Pusher
    Pusher.logToConsole = true;
    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
    });

    // Initialize Echo
    var channel = pusher.subscribe('scan-progress');
    channel.bind('ScanProgressUpdated', function(data) {
        document.getElementById('progress').innerText = `Scan Progress: ${data.progress}%`;
    });
</script>
@endpush
