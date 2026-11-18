<?php
require 'koneksi.php';
$judul = 'Data Siswa';
$err = [];
$data = ['id_siswa'=>0,'nama_siswa'=>'','kelas'=>'','jurusan'=>'','email'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['aksi'] ?? '') === 'hapus') {
        $id = (int)$_POST['id'];
        $st = $db->prepare("DELETE FROM siswa WHERE id_siswa=?"); $st->bind_param('i', $id); $st->execute();
        flash('Data siswa berhasil dihapus.'); redirect('siswa.php');
    }
    $data = ['id_siswa'=>(int)($_POST['id_siswa'] ?? 0)];
    foreach (['nama_siswa','kelas','jurusan','email'] as $k) $data[$k] = trim($_POST[$k] ?? '');
    if ($data['nama_siswa'] === '' || mb_strlen($data['nama_siswa']) > 100) $err[] = 'Nama siswa wajib diisi (maksimal 100 karakter).';
    if ($data['kelas'] === '' || mb_strlen($data['kelas']) > 20) $err[] = 'Kelas wajib diisi (maksimal 20 karakter).';
    if ($data['jurusan'] === '' || mb_strlen($data['jurusan']) > 50) $err[] = 'Jurusan wajib diisi (maksimal 50 karakter).';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($data['email']) > 100) $err[] = 'Format email tidak valid.';
    if (!$err) {
        if ($data['id_siswa']) {
            $st = $db->prepare("UPDATE siswa SET nama_siswa=?,kelas=?,jurusan=?,email=? WHERE id_siswa=?");
            $st->bind_param('ssssi', $data['nama_siswa'], $data['kelas'], $data['jurusan'], $data['email'], $data['id_siswa']);
            $pesan = 'Data siswa berhasil diperbarui.';
        } else {
            $st = $db->prepare("INSERT INTO siswa (nama_siswa,kelas,jurusan,email) VALUES (?,?,?,?)");
            $st->bind_param('ssss', $data['nama_siswa'], $data['kelas'], $data['jurusan'], $data['email']);
            $pesan = 'Data siswa berhasil ditambahkan.';
        }
        $st->execute(); flash($pesan); redirect('siswa.php');
    }
} elseif (isset($_GET['edit'])) {
    $st = $db->prepare("SELECT * FROM siswa WHERE id_siswa=?"); $id = (int)$_GET['edit'];
    $st->bind_param('i', $id); $st->execute();
    $data = $st->get_result()->fetch_assoc() ?: $data;
}

$q = trim($_GET['q'] ?? ''); $like = "%$q%";
$st = $db->prepare("SELECT * FROM siswa WHERE nama_siswa LIKE ? OR kelas LIKE ? OR jurusan LIKE ? OR email LIKE ? ORDER BY nama_siswa");
$st->bind_param('ssss', $like, $like, $like, $like); $st->execute();
$daftar = $st->get_result();
include 'includes/header.php';
?>
<?php if ($err): ?><div class="alert err"><i class="fa-solid fa-circle-exclamation"></i><ul><?php foreach ($err as $m) echo '<li>'.e($m).'</li>'; ?></ul></div><?php endif; ?>
<section class="card">
  <h2><?= $data['id_siswa'] ? 'Edit siswa' : 'Tambah siswa' ?></h2>
  <form method="post" class="form">
    <input type="hidden" name="id_siswa" value="<?= (int)$data['id_siswa'] ?>">
    <label>Nama siswa<input type="text" name="nama_siswa" maxlength="100" required value="<?= e($data['nama_siswa']) ?>"></label>
    <label>Kelas<input type="text" name="kelas" maxlength="20" required placeholder="X / XI / XII" value="<?= e($data['kelas']) ?>"></label>
    <label>Jurusan<input type="text" name="jurusan" maxlength="50" required list="jur" value="<?= e($data['jurusan']) ?>">
      <datalist id="jur"><option>IPA</option><option>IPS</option><option>Bahasa</option></datalist></label>
    <label>Email<input type="email" name="email" maxlength="100" required value="<?= e($data['email']) ?>"></label>
    <div class="aksi-form">
      <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
      <?php if ($data['id_siswa']): ?><a href="siswa.php" class="btn abu"><i class="fa-solid fa-xmark"></i> Batal</a><?php endif; ?>
    </div>
  </form>
</section>
<section class="card">
  <h2>Daftar siswa</h2>
  <form class="cari" method="get">
    <input type="search" name="q" placeholder="Cari nama, kelas, jurusan, atau email…" value="<?= e($q) ?>">
    <button class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
    <?php if ($q !== ''): ?><a href="siswa.php" class="btn abu">Reset</a><?php endif; ?>
  </form>
  <div class="tabel-wrap"><table>
    <tr><th>No</th><th>Nama</th><th>Kelas</th><th>Jurusan</th><th>Email</th><th>Aksi</th></tr>
    <?php if ($daftar->num_rows === 0): ?><tr><td colspan="6" class="kosong">Data siswa tidak ditemukan.</td></tr><?php endif; ?>
    <?php $no = 1; while ($r = $daftar->fetch_assoc()): ?>
    <tr><td><?= $no++ ?></td><td><?= e($r['nama_siswa']) ?></td><td><?= e($r['kelas']) ?></td><td><?= e($r['jurusan']) ?></td><td><?= e($r['email']) ?></td>
      <td class="aksi">
        <a class="btn kuning kecil" href="siswa.php?edit=<?= $r['id_siswa'] ?>"><i class="fa-solid fa-pen"></i> Edit</a>
        <form method="post" data-konfirmasi="Hapus siswa ini? Data tugasnya juga ikut terhapus.">
          <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $r['id_siswa'] ?>">
          <button class="btn merah kecil"><i class="fa-solid fa-trash"></i> Hapus</button></form></td></tr>
    <?php endwhile; ?>
  </table></div>
</section>
<?php include 'includes/footer.php'; ?>
