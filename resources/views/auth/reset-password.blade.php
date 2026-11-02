<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — DashBite</title>
    <link rel="stylesheet" href="{{ asset('food-dash/app.css') }}">
    <script type="module" src="{{ asset('food-dash/app.js') }}"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-orange-600 mb-6 text-center">🍕 DashBite</h1>
        <h2 class="text-lg font-semibold mb-4">Set your new password</h2>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <p class="text-sm text-gray-500 mb-4">
            Resetting password for
            <strong class="text-gray-700">{{ $email }}</strong>
        </p>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">New Password</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters.</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded hover:bg-orange-700">
                Reset Password
            </button>

            <p class="text-center text-sm text-gray-600">
                <a href="{{ route('login') }}" class="text-orange-600 hover:underline">Back to login</a>
            </p>
        </form>
    </div>
</body>
</html>