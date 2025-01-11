<?php
session_start();
include('../config/database.php');

// Cek role superadmin
if ($_SESSION['role'] != 'superadmin') {
    header('Location: ../auth/login.php');
    exit;
}

// Proses validasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action']; // 'approve' atau 'reject'

    if (!empty($id) && in_array($action, ['approve', 'reject'])) {
        try {
            $status = $action === 'approve' ? 'valid' : 'rejected';
            $stmt = $conn->prepare("UPDATE users SET status_validasi = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $id]);

            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Validasi berhasil diperbarui.',
            ];
        } catch (PDOException $e) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    } else {
        $_SESSION['flash'] = [
            'type' => 'warning',
            'message' => 'Data tidak valid.',
        ];
    }

    header('Location: manage-validation.php');
    exit;
}

// Ambil data mahasiswa yang status validasinya pending
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE status_validasi = 'pending'");
    $stmt->execute();
    $pendaftar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Terjadi kesalahan database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Validasi Mahasiswa</h2>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="mb-4 p-4 rounded bg-<?php echo $_SESSION['flash']['type'] === 'success' ? 'green' : 'red'; ?>-100 text-<?php echo $_SESSION['flash']['type'] === 'success' ? 'green' : 'red'; ?>-800">
                <?php echo htmlspecialchars($_SESSION['flash']['message']); ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded-lg p-6">
            <?php if (!empty($pendaftar)): ?>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-blue-500 text-white">
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">NIM</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendaftar as $mahasiswa): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="py-3 px-4"><?php echo htmlspecialchars($mahasiswa['nama']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($mahasiswa['nim']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($mahasiswa['email']); ?></td>
                                <td class="py-3 px-4">
                                    <form method="POST" action="" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $mahasiswa['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="text-green-500 hover:text-green-700">Approve</button>
                                    </form>
                                    <span class="mx-2">|</span>
                                    <form method="POST" action="" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $mahasiswa['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="text-red-500 hover:text-red-700">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-600">Tidak ada mahasiswa yang perlu divalidasi.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
