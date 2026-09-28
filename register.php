<?php
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nama) || empty($email) || empty($password)) {

        $message = 'Semua field wajib diisi.';
        $messageType = 'error';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Format email tidak valid.';
        $messageType = 'error';

    } elseif (strlen($password) < 6) {

        $message = 'Password minimal 6 karakter.';
        $messageType = 'error';

    } else {

        $nama = htmlspecialchars(
            $nama,
            ENT_QUOTES,
            'UTF-8'
        );

        $email = htmlspecialchars(
            $email,
            ENT_QUOTES,
            'UTF-8'
        );

        $file = 'users.json';

        $users = [];

        if (file_exists($file)) {

            $json = file_get_contents($file);

            $users = json_decode(
                $json,
                true
            ) ?? [];
        }

        $emailExists = false;

        foreach ($users as $user) {

            if (
                strtolower($user['email']) ===
                strtolower($email)
            ) {

                $emailExists = true;
                break;
            }
        }

        if ($emailExists) {

            $message = 'Email sudah terdaftar.';
            $messageType = 'error';

        } else {

            $hashedPassword =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

            $newUser = [
                'id' => count($users) + 1,
                'nama' => $nama,
                'email' => $email,
                'password' => $hashedPassword,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $users[] = $newUser;

            file_put_contents(
                $file,
                json_encode(
                    $users,
                    JSON_PRETTY_PRINT
                )
            );

            $message =
                'Registrasi berhasil. Silakan login.';

            $messageType =
                'success';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Register</title>

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

                <h1>Buat Akun</h1>

                <p>
                    Daftar untuk masuk ke sistem.
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

                    <label for="nama">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
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
                        placeholder="Minimal 6 karakter"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Daftar
                </button>

            </form>

            <p class="auth-footer">
                Sudah punya akun?
                <a href="login.php">
                    Login
                </a>
            </p>

        </section>

    </main>

</body>

</html>