<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Novatra CRM - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for eye icon (optional, but clean) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* subtle transition for eye button */
        .password-toggle {
            cursor: pointer;
            transition: color 0.2s;
        }
        .password-toggle:hover {
            color: #1f2937; /* gray-800 */
        }
    </style>
</head>
<body class="min-h-screen bg-gray-950 flex items-center justify-center">

    <div class="w-full min-h-screen flex">

        <!-- Left Branding Section -->
        <div class="hidden lg:flex w-1/2 bg-gray-950 text-white p-12 flex-col justify-between">
            <div>
                <h1 class="text-4xl font-bold tracking-wide">NOVATRA</h1>
                <p class="text-gray-400 mt-2">CRM & Business Management Platform</p>
            </div>
            <div>
                <h2 class="text-5xl font-bold leading-tight">
                    Manage Leads.<br />
                    Close Deals.<br />
                    Scale Faster.
                </h2>
                <p class="text-gray-400 mt-6 text-lg max-w-lg">
                    Empower your real estate team with smart lead tracking,
                    sales pipelines, and performance insights.
                </p>
                <div class="mt-10 grid grid-cols-3 gap-5">
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-2xl font-bold">10K+</p>
                        <p class="text-gray-400 text-sm">Leads Managed</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-2xl font-bold">50+</p>
                        <p class="text-gray-400 text-sm">Agents</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-2xl font-bold">24/7</p>
                        <p class="text-gray-400 text-sm">Tracking</p>
                    </div>
                </div>
            </div>
            <p class="text-gray-500 text-sm">© 2026 Novatra Solution</p>
        </div>

        <!-- Login Section -->
        <div class="w-full lg:w-1/2 bg-white flex items-center justify-center p-8">
            <div class="w-full max-w-md">

                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-gray-900">Welcome Back</h2>
                    <p class="text-gray-500 mt-2">Login to your CRM account</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-5">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="name@company.com"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-gray-900 focus:outline-none"
                        />
                    </div>

                    <!-- Password with Show/Hide toggle -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input
                                id="password-field"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-gray-900 focus:outline-none pr-12"
                            />
                            <span
                                id="toggle-password"
                                class="password-toggle absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-gray-800"
                            >
                                <i id="eye-icon" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex justify-between items-center mb-6">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remember" class="rounded" />
                            Remember me
                        </label>
                        <a href="#" class="text-sm font-medium text-gray-900 hover:underline">
                            Forgot password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gray-950 text-white py-3 rounded-xl font-semibold hover:bg-gray-800 transition"
                    >
                        Sign In
                    </button>
                </form>

                <p class="text-center text-gray-400 text-sm mt-8">
                    Secure access for authorized team members
                </p>
            </div>
        </div>
    </div>

    <!-- Toggle password visibility script -->
    <script>
        (function() {
            const toggleBtn = document.getElementById('toggle-password');
            const passwordField = document.getElementById('password-field');
            const eyeIcon = document.getElementById('eye-icon');

            if (toggleBtn && passwordField && eyeIcon) {
                toggleBtn.addEventListener('click', function() {
                    // toggle type
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);

                    // toggle icon
                    if (type === 'password') {
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    } else {
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    }
                });
            }
        })();
    </script>

</body>
</html>