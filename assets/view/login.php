
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Parkir IKEA</title>

    <!-- CSS dipisahkan -->
    <link rel="stylesheet" href="../assets/login.css">
</head>

<body>

<div class="login-container">

    <!-- BAGIAN KIRI -->
    <div class="login-left">

        <div class="logo">
            <span class="car-icon">🚗</span>
            <h1>Parkir IKEA</h1>
        </div>

        <p class="subtitle">Sistem Informasi Parkir</p>

        <div class="parking-image">

            <div class="parking-sign">
                P
            </div>

            <div class="car">
                🚗
            </div>

            <div class="gate">
                <div class="gate-top"></div>
                <div class="gate-body"></div>
            </div>

            <div class="barrier">
                ╱
            </div>

        </div>

        <p class="slogan">
            Parkir lebih mudah,<br>
            lebih teratur, lebih nyaman
        </p>

        <div class="heart">
            ♥
        </div>

    </div>


    <!-- BAGIAN KANAN -->
    <div class="login-right">

        <h2>Login</h2>

        <p class="login-description">
            Masuk ke akun Anda
        </p>

        <form action="../controller/c_login.php" method="POST">

            <!-- USERNAME -->
            <div class="input-box">

                <span class="input-icon">♙</span>

                <input
                    type="text"
                    name="username"
                    placeholder="Username atau Nama Lengkap"
                    required
                >

            </div>


            <!-- PASSWORD -->
            <div class="input-box">

                <span class="input-icon">🔒</span>

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password"
                    required
                >

                <span
                    class="eye"
                    onclick="showPassword()"
                >
                    👁
                </span>

            </div>


            <!-- TOMBOL LOGIN -->
            <button
                type="submit"
                name="login"
                class="login-button"
            >
                Login
            </button>

        </form>

        <p class="system-name">
            Sistem Parkir IKEA
        </p>

    </div>

</div>


<script>

function showPassword() {

    const password = document.getElementById("password");

    if (password.type === "password") {
        password.type = "text";
    } else {
        password.type = "password";
    }

}

</script>

</body>
</html>

