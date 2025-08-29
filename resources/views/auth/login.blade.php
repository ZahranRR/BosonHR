<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="icon" href="{{ asset('dist/img/icongrob.png') }}" type="image/x-icon" />
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="h-screen bg-white dark:bg-slate-900">
    <div class="flex flex-col md:flex-row h-full">
        <!-- Login Card -->
        <div
            class="relative w-full md:w-2/5 h-full flex items-center justify-center bg-white dark:bg-slate-800 sm:px-6 sm:py-10 px-4 py-6">

            <!-- Background image for mobile -->
            <div class="absolute inset-0 bg-cover bg-center md:hidden"
                style="background-image: url('{{ asset('dist/img/photo4.jpg') }}'); z-index: 0;">
            </div>

            <!-- Card Content -->
            <div
                class="bg-white dark:bg-gray-800 p-6 md:p-8 mx-4 md:mx-0 rounded-xl shadow-xl relative z-10 w-full max-w-lg h-auto md:h-full flex flex-col justify-center">
                <div class="flex flex-col items-center">
                    @if (isset($companyname) && $companyname->image)
                        <img src="{{ asset('storage/' . $companyname->image) }}" alt="Logo"
                            class="h-[80px] object-contain mb-8">
                    @endif
                </div>


                <h2 class="text-lg font-medium text-start text-gray-800 dark:text-white mb-5 font-[Inter]">
                    Sign in to account
                </h2>

                @if ($errors->any())
                    <div class="mb-4">
                        @foreach ($errors->all() as $error)
                            <div class="text-red-600 dark:text-red-400 text-sm">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Email Field -->
                    <div class="mb-6">
                        <label for="email"
                            class="block text-base font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input id="email" type="email" name="email" required autofocus
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-gray-700 focus:border-gray-700 sm:text-sm dark:bg-slate-800 dark:text-gray-200" />
                    </div>

                    <!-- Password Field -->
                    <div class="mb-6">
                        <label for="password"
                            class="block text-base font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-gray-700 focus:border-gray-700 sm:text-sm dark:bg-slate-800 dark:text-gray-200" />
                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-3 flex items-center">
                                <i class="fas fa-eye text-gray-600 dark:text-gray-400"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox -->
                    <div class="flex items-center mb-6">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="w-5 h-5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 border-gray-300 dark:border-gray-600 rounded focus:ring-gray-700 focus:ring-offset-gray-700 dark:focus:ring-offset-slate-900">
                        <label for="remember_me" class="ml-2 text-base text-gray-600 dark:text-gray-400">Remember
                            me</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-gray-800 text-white py-3 px-4 rounded-md hover:bg-gray-700 focus:outline-none focus:ring focus:ring-gray-600">
                        Log in
                    </button>
                </form>
            </div>

        </div>

        <!-- Background Image for Large Screens -->
        <div class="hidden md:block w-3/5 bg-cover bg-center h-full"
            style="background-image: url('{{ asset('dist/img/photo4.jpg') }}');">
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
