
<!DOCTYPE html>
<html>
<head>
    <title>Visitor Count</title>
</head>
<body>
    <h1>Visitors</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>IP Address</th>
                <th>URL</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visitors as $visitor)
                <tr>
                    <td>{{ $visitor->id }}</td>
                    <td>{{ $visitor->ip_address }}</td>
                    <td>{{ $visitor->url }}</td>
                    <td>{{ $visitor->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
