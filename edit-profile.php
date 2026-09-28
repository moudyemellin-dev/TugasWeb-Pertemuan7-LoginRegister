<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

$file = 'users.json';
$users = [];

if (file_exists($file)) {
    $json = file_get_contents($file);

    $users = json_decode(
        $json,
        true
    ) ?? [];
}

$currentUserIndex = null;

foreach ($users as $index => $user) {
    if (
        $user['id'] ==
        $_SESSION['user_id']
    ) {
        $currentUserIndex = $index;
        break;
    }
}

if ($currentUserIndex === null) {
    session_destroy();

    header('Location: login.php');
    exit;
}

$currentUser =
    $users[$currentUserIndex];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama =
        trim($_POST['nama'] ?? '');

    $email =
        trim($_POST['email'] ?? '');


    if (
        empty($nama) ||
        empty($email)
    ) {

        $message =
            'Nama dan email wajib diisi.';

        $messageType =
            'error';

    } elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $message =
            'Format email tidak valid.';

        $messageType =
            'error';

    } else {

        $nama =
            htmlspecialchars(
                $nama,
                ENT_QUOTES,
                'UTF-8'
            );

        $email =
            htmlspecialchars(
                $email,
                ENT_QUOTES,
                'UTF-8'
            );


        $emailExists = false;

        foreach (
            $users as $index => $user
        ) {

            if (
                $index !==
                    $currentUserIndex
                &&
                strtolower($user['email']) ===
                    strtolower($email)
            ) {

                $emailExists = true;
                break;
            }
        }


        if ($emailExists) {

            $message =
                'Email sudah digunakan oleh akun lain.';

            $messageType =
                'error';

        } else {

            $users[$currentUserIndex]['nama'] =
                $nama;

            $users[$currentUserIndex]['email'] =
                $email;

            file_put_contents(
                $file,
                json_encode(
                    $users,
                    JSON_PRETTY_PRINT
                )
            );


            $_SESSION['username'] =
                $nama;

            $_SESSION['email'] =
                $email;


            header(
                'Location: dashboard.php?msg=profile_updated'
            );

            exit;
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

    <title>Edit Profile</title>

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
                    ✏️
                </span>

                <h1>Edit Profile</h1>

                <p>
                    Perbarui data akun kamu.
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
                        value="<?= htmlspecialchars(
                            $currentUser['nama'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
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
                        value="<?= htmlspecialchars(
                            $currentUser['email'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="primary-btn"
                >
                    Simpan Perubahan
                </button>

            </form>


            <p class="auth-footer">

                <a href="dashboard.php">
                    ← Kembali ke Dashboard
                </a>

            </p>

        </section>

    </main>

</body>

</html>