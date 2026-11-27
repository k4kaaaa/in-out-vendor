<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - In-Out Vendor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { poppins: ['Poppins', 'sans-serif'] },
                    colors: {
                        accent: '#16a34a',
                        secondary: '#0b3b2e',
                        deepgreen: '#064e3b',
                        mint: '#34d399',
                    }
                }
            }
        }
    </script>
    <script src="https://www.google.com/recaptcha/api.js?hl=id" async defer></script>
    <style>
        * { font-family: 'Poppins', sans-serif; }

        .bubble {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            animation: float 6s ease-in-out infinite;
        }
        .bubble::before {
            content: '';
            position: absolute;
            top: 12%;
            left: 18%;
            width: 30%;
            height: 30%;
            border-radius: 50%;
            background: rgba(255,255,255,0.55);
            filter: blur(2px);
        }
        .bubble::after {
            content: '';
            position: absolute;
            bottom: 20%;
            right: 15%;
            width: 18%;
            height: 18%;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            filter: blur(3px);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-18px); }
        }
        .b1 { width: 130px; height: 130px; top: 6%; right: 12%; animation-delay: 0s; opacity: 0.7; }
        .b2 { width: 50px; height: 50px; top: 18%; right: 4%; animation-delay: 1.2s; opacity: 0.5; }
        .b3 { width: 70px; height: 70px; top: 44%; left: 4%; animation-delay: 2.4s; opacity: 0.45; }
        .b4 { width: 35px; height: 35px; bottom: 22%; right: 22%; animation-delay: 0.8s; opacity: 0.6; }
        .b5 { width: 90px; height: 90px; bottom: 10%; left: 18%; animation-delay: 3s; opacity: 0.35; }
        .b6 { width: 110px; height: 110px; bottom: -30px; left: -30px; animation-delay: 1.5s; opacity: 0.3; }
        .rb1 { width: 80px; height: 80px; top: -20px; right: -10px; animation-delay: 0.5s; opacity: 0.5; background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.7), rgba(255,255,255,0.05))!important; }
        .rb2 { width: 50px; height: 50px; bottom: 30px; left: -8px; animation-delay: 2s; opacity: 0.4; }
        .rb3 { width: 30px; height: 30px; top: 25%; right: 5%; animation-delay: 1s; opacity: 0.35; }
        .card-glow { box-shadow: 0 8px 40px rgba(6, 78, 59, 0.1), 0 1px 3px rgba(0,0,0,0.06); }
    </style>
</head>
<body class="h-full font-poppins antialiased bg-deepgreen">
    <div class="min-h-full flex flex-col md:flex-row">

        <!-- ===== LEFT PANEL ===== -->
        <div class="hidden md:flex md:w-1/2 relative overflow-hidden" style="background: linear-gradient(160deg, #064e3b 0%, #0b3b2e 55%, #0f5132 100%);">
            <div class="bubble b1" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.18), rgba(255,255,255,0.03));"></div>
            <div class="bubble b2" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.22), rgba(255,255,255,0.05));"></div>
            <div class="bubble b3" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.15), rgba(255,255,255,0.02));"></div>
            <div class="bubble b4" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.2), rgba(255,255,255,0.04));"></div>
            <div class="bubble b5" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.12), rgba(255,255,255,0.02));"></div>
            <div class="bubble b6" style="background: radial-gradient(circle at 30% 30%, rgba(52,211,153,0.25), transparent 70%);"></div>

            <div class="relative z-10 flex flex-col justify-between px-6 xl:p-12 py-12 h-full text-white">
                <!-- Logo -->
                <div class="flex flex-col gap-1.5">
                    <div class="inline-flex items-center gap-3">
                        <div class="w-16 h-16 flex items-center justify-center shrink-0 text-emerald-400">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
                        <span class="text-2xl font-bold tracking-tight">In-Out Vendor</span>
                    </div>
                    <p class="text-sm text-emerald-200/70 mt-1">Sistem Manajemen Gudang & Logistik</p>
                </div>

                <!-- Main copy -->
                <div class="space-y-3">
                    <h2 class="text-3xl font-bold leading-tight tracking-tight">
                        Kelola Barang Masuk<br>
                        dengan <span class="text-emerald-300">Lebih Mudah</span>
                    </h2>
                    <p class="text-base text-emerald-100/70 leading-relaxed">
                        Pantau penerimaan, persediaan, dan vendor dalam satu sistem terintegrasi.
                    </p>
                </div>

                <p class="text-emerald-200/40 text-xs">&copy; {{ date('Y') }} In-Out Vendor</p>
            </div>
        </div>

        <!-- ===== RIGHT PANEL ===== -->
        <div class="flex-1 flex items-center justify-center relative overflow-hidden" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 40%, #ecfdf5 100%);">
            <div class="bubble rb1" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.8), rgba(255,255,255,0.05))!important;"></div>
            <div class="bubble rb2" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.7), rgba(255,255,255,0.05))!important;"></div>
            <div class="bubble rb3" style="background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.6), rgba(255,255,255,0.05))!important;"></div>

            <div class="w-full max-w-[420px] w-full max-w-lg px-4 sm:p-6 relative z-10">

                <!-- Mobile/Tablet logo only -->
                <div class="lg:hidden flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0 text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
                    <div>
                        <h1 class="text-lg font-bold text-deepgreen tracking-tight">In-Out Vendor</h1>
                        <p class="text-xs text-slate-500">Sistem Manajemen Gudang & Logistik</p>
                    </div>
                </div>

                <!-- Login Card -->
                <div class="bg-white/70 rounded-3xl card-glow border border-white/80 p-6 sm:p-8 max-w-full">

                    <!-- Box icon -->
                    <div class="mb-5">
                        <div class="w-16 h-16 mx-auto flex items-center justify-center mb-4 text-emerald-600">
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
    <path d="M3.27 6.96L12 12.01L20.73 6.96"></path>
    <path d="M12 22.08V12"></path>
</svg></div>
                    </div>

                    <div class="mb-5">
                        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Selamat Datang</h2>
                        <p class="text-sm text-slate-500 mt-1">Masuk untuk melanjutkan ke dashboard.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border border-red-100 text-red-600 rounded-xl text-xs font-medium space-y-0.5">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Email</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                </svg>
                                <input type="email" name="email" id="email" required autofocus value="{{ old('email') }}" placeholder="admin@inoutvendor.com"
                                    class="w-full pl-10 pr-3 py-2.5 bg-blue-50 border border-blue-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition duration-200"/>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Password</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                                <input type="password" name="password" id="password" required placeholder="••••••••"
                                    class="w-full pl-10 pr-9 py-2.5 bg-blue-50 border border-blue-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition duration-200"/>
                                <span onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-slate-400 hover:text-emerald-500 transition-colors duration-150">
                                    <svg id="eyeOn" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 6.837M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                                    </svg>
                                    <svg id="eyeOff" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.5M6.228 6.228L21 21m-4.242-4.243l-2.829-2.829M9.75 15.75a3 3 0 11-4.242-4.242M16.5 7.5L18 6m0 0l1.5 1.5M18 6a9 9 0 00-9 0"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Remember + Lupa password -->
                        <div class="flex items-center justify-between text-xs py-1 mb-5">
                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-400"/>
                                <span class="text-xs font-medium text-slate-600">Ingat saya</span>
                            </label>
                            <a href="#" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Lupa password?</a>
                        </div>

                        <!-- reCAPTCHA -->
                        <div class="mb-4 flex justify-center">
                            <div class="g-recaptcha" data-sitekey="6LfZ2BYuAAAAACs_xpP9xzo1dKsxEV9rD28Ioedl" data-theme="light" style="transform: scale(0.77); transform-origin: 0 0;"></div>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white py-3 rounded-xl text-sm font-medium shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 hover:from-emerald-400 hover:to-emerald-500 active:scale-[0.96] transition-all duration-200 flex items-center justify-center gap-2">
                            <span>Masuk</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Footer -->
                    <p class="text-center text-xs text-slate-500 mt-4">
                        Belum punya akun? <a href="#" class="font-semibold text-emerald-600 hover:text-emerald-700 transition">Hubungi Admin/Manager</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pw = document.getElementById('password');
            const on = document.getElementById('eyeOn');
            const off = document.getElementById('eyeOff');
            if (pw.type === 'password') {
                pw.type = 'text';
                on.classList.add('hidden');
                off.classList.remove('hidden');
            } else {
                pw.type = 'password';
                on.classList.remove('hidden');
                off.classList.add('hidden');
            }
        }
    </script>
</body>
</html>