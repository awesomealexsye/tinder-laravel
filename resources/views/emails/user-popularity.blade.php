<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Has Received {{ $likeCount }}+ Likes</title>
</head>
<body>
    <h2>User Has Received {{ $likeCount }} Likes!</h2>
    
    <p>Hello Admin,</p>
    
    <p>User <strong>{{ $user->name }}</strong> (ID: {{ $user->id }}) has received {{ $likeCount }} likes!</p>
    
    <h3>Profile Details:</h3>
    <ul>
        <li><strong>Name:</strong> {{ $user->name }}</li>
        <li><strong>Age:</strong> {{ $user->age }}</li>
        <li><strong>Gender:</strong> {{ $user->gender }}</li>
        <li><strong>Location:</strong> {{ $user->location ?? 'Not specified' }}</li>
        <li><strong>Total Likes:</strong> {{ $likeCount }}</li>
        <li><strong>Email:</strong> {{ $user->email }}</li>
    </ul>
    
    <p>This is an automated notification.</p>
</body>
</html>

