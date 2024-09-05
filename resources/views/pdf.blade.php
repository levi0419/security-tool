<!DOCTYPE html>
<html>
<head>
    <title>Scan Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            color: #333;
        }
        pre {
            background-color: #f8f8f8;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Scan Results</h1>

    @foreach ($results as $testName => $result)
        <h2>{{ $testName }}</h2>
        @if (is_array($result) || is_object($result))
            <pre>{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
        @else
            <pre>{{ $result }}</pre>
        @endif
    @endforeach
</body>
</html>
