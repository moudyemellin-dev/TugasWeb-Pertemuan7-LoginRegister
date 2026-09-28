<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if (
    isset($_GET['msg']) &&
    $_GET['msg'] === 'profile_updated'
) {
    $message = 'Profile berhasil diperbarui.';
}

$nama = $_SESSION['username'] ?? 'User';
$email = $_SESSION['email'] ?? '-';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard</title>

    <link
        rel="stylesheet"
        href="style.css"
    >
</head>

<body>

    <main class="dashboard-container">

        <section class="dashboard-card">

            <div class="dashboard-header">

                <span class="dashboard-icon">
                    🌿
                </span>

                <div>
                    <p class="eyebrow">
                        Dashboard
                    </p>

                    <h1>
                        Selamat Datang!
                    </h1>

                    <p>
                        Kamu berhasil login ke sistem.
                    </p>
                </div>

            </div>

<?php if (!empty($message)): ?>

    <div class="message success">
        <?= htmlspecialchars(
            $message,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </div>

<?php endif; ?>

            <div class="profile-card">

                <div class="profile-avatar">
                    👤
                </div>

                <div class="profile-info">

                    <p class="profile-label">
                        Nama
                    </p>

                    <h2>
                        <?= htmlspecialchars(
                            $nama,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

                    <p class="profile-label">
                        Email
                    </p>

                    <p>
                        <?= htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

            </div>


            <div class="dashboard-actions">

                <a
                    href="edit-profile.php"
                    class="primary-btn action-link"
                >
                    ✏️ Edit Profile
                </a>

                <a
                    href="logout.php"
                    class="secondary-btn action-link"
                >
                    🚪 Logout
                </a>

            </div>

        </section>

    </main>

</body>

</html>