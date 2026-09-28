<?php
session_start();

$message = '';
$messageType = '';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if (isset($_GET['msg'])) {

    if ($_GET['msg'] === 'logged_out') {
        $message = 'Logout berhasil.';
        $messageType = 'success';
    }

    if ($_GET['msg'] === 'profile_updated') {
        $message = 'Profile berhasil diperbarui.';
        $messageType = 'success';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    if (empty($email) || empty($password)) {

        $message = 'Email dan password wajib diisi.';
        $messageType = 'error';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Format email tidak valid.';
        $messageType = 'error';

    } else {

        $file = 'users.json';
        $users = [];

        if (file_exists($file)) {

            $json = file_get_contents($file);

            $users = json_decode(
                $json,
                true
            ) ?? [];
        }

        $foundUser = null;

        foreach ($users as $user) {

            if (
                strtolower($user['email']) ===
                strtolower($email)
            ) {

                $foundUser = $user;
                break;
            }
        }

        if (
            $foundUser &&
            password_verify(
                $password,
                $foundUser['password']
            )
        ) {

            $_SESSION['user_id'] =
                $foundUser['id'];

            $_SESSION['username'] =
                $foundUser['nama'];

            $_SESSION['email'] =
                $foundUser['email'];

            if ($rememberMe) {

                $expiry =
                    time() +
                    (30 * 24 * 60 * 60);

                setcookie(
                    'remember_email',
                    $foundUser['email'],
                    $expiry,
                    '/',
                    '',
                    false,
                    true
                );
            } else {

                setcookie(
                    'remember_email',
                    '',
                    time() - 3600,
                    '/'
                );
            }

            header(
                'Location: dashboard.php'
            );

            exit;

        } else {

            $message =
                'Email atau password salah.';

            $messageType =
                'error';
        }
    }
}

$rememberedEmail =
    $_COOKIE['remember_email'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login</title>

    <link
        rel="stylesheet"
        href="style.css"
    >
</head>

<body>

    <main class="auth-container">

        <section class="auth-card">

            <div class="auth-header">

                <span class="auth-icon">
                    🌿
                </span>

                <h1>Selamat Datang</h1>

                <p>
                    Login untuk masuk ke dashboard.
                </p>

            </div>

            <?php if (!empty($message)): ?>

                <div
                    class="message <?= $messageType ?>"
                >
                    <?= htmlspecialchars(
                        $message,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            <?php endif; ?>

            <form
                method="POST"
                action=""
                class="auth-form"
            >

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars(
                            $rememberedEmail,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="Masukkan email"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >

                </div>

                <label class="remember-me">

                    <input
                        type="checkbox"
                        name="remember_me"
                        <?= !empty($rememberedEmail)
                            ? 'checked'
                            : '' ?>
                    >

                    <span>
                        Ingat email saya
                    </span>

                </label>

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Login
                </button>

            </form>

            <p class="auth-footer">
                Belum punya akun?
                <a href="register.php">
                    Daftar
                </a>
            </p>

        </section>

    </main>

</body>

</html>