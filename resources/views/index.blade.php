@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="jumbotron text-center">
    <h1>Welcome to the Web Security Audit Tool</h1>
    <p>Scan your website for vulnerabilities with ease.</p>
    <a href="{{ url('/scan') }}" class="btn btn-primary">Start a Scan</a>
</div>
@endsection
