@extends('layouts.auth')
@section('content')
    @php
        $bengkelSetting = \App\Models\BengkelSetting::first();
        $namaBengkel = $bengkelSetting ? $bengkelSetting->nama_bengkel : 'BengkelKu';
    @endphp
    
    <div class="page page-center bg-gradient">
        <div class="container container-tight py-4">
            <div class="text-center mb-5">
                <div class="brand-logo">
                    <span class="brand-text fs-1 fw-bold">{{ $namaBengkel }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('login') }}" class="card card-md shadow-lg">
                @csrf
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Welcome Back!</h2>
                    <p class="text-center text-muted mb-4">Please sign in to continue</p>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group input-group-flat">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                    <path d="M3 7l9 6l9 -6" />
                                </svg>
                            </span>
                            <input type="email" name="email" class="form-control" placeholder="your@email.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group input-group-flat">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-lock">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
                                    <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                    <path d="M8 13v-2a4 4 0 1 1 8 0v2" />
                                </svg>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="Your password" id="password-input" required>
                            <span class="input-group-text">
                                <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip" onclick="togglePassword(event)">
                                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember"/>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-green w-100 btn-pill">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-login">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M15 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M21 12h-13l3 -3" />
                                <path d="M11 15l-3 -3" />
                            </svg>
                            Sign in
                        </button>
                    </div>
                </div>
            </form>
            
            {{-- <div class="text-center text-muted mt-3">
                Don't have account yet? <a href="{{ route('register') }}" tabindex="-1">Sign up</a>
            </div> --}}
        </div>
    </div>

    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        
        .brand-text {
            color: #2d3748;
            letter-spacing: -0.5px;
        }
        
        .icon-brand {
            color: #4f46e5;
            width: 42px;
            height: 42px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .btn-pill {
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #a5b4fc;
            box-shadow: 0 0 0 3px rgba(199, 210, 254, 0.5);
        }
        
        .input-group-text {
            background-color: transparent;
            border-right: none;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .input-group .form-control:focus {
            border-left: 1px solid #a5b4fc;
        }
    </style>

    <script>
        function togglePassword(event) {
            event.preventDefault();
            const passwordInput = document.getElementById('password-input');
            const eyeIcon = document.getElementById('eye-icon');

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            // Optional: Swap icon (open eye vs closed eye)
            eyeIcon.innerHTML = isPassword ?
                `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" /><path d="M3 3l18 18" /></svg>` :
                `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>`;
        }
    </script>
@endsection