<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | CityFix</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <b>CITYFIX</b> Bumi Sholawat
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Masuk untuk melaporkan dan memantau kerusakan</p>

                    <form method="POST" action="{{ route('login.process') }}">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Email" required autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text"><span class="fas fa-lock"></span></div>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember">Ingat saya</label>
                                </div>
                            </div>
                            <div class="col-5">
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>

</html>
