<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — DashBite</title>
    <link rel="stylesheet" href="{{ asset('food-dash/app.css') }}">
    <script type="module" src="{{ asset('food-dash/app.js') }}"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-orange-600 mb-6 text-center">🍕 DashBite</h1>
        <h2 class="text-lg font-semibold mb-4">Forgot your password?</h2>
        <p class="text-sm text-gray-500 mb-4">
            Enter your email and we'll send you a 6-digit code to reset your password.
        </p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded hover:bg-orange-700">
                Send Verification Code
            </button>

            <p class="text-center text-sm text-gray-600">
                <a href="{{ route('login') }}" class="text-orange-600 hover:underline">Back to login</a>
            </p>
        </form>
    </div>
</body>
</html>