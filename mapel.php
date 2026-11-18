<?php
require 'koneksi.php';
$judul = 'Mata Pelajaran';
$err = [];
$data = ['id_mapel'=>0,'nama_mapel'=>'','nama_guru'=>'','kelas'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['aksi'] ?? '') === 'hapus') {
        $id = (int)$_POST['id'];
        $st = $db->prepare("DELETE FROM mata_pelajaran WHERE id_mapel=?"); $st->bind_param('i', $id); $st->execute();
        flash('Mata pelajaran berhasil dihapus.'); redirect('mapel.php');
    }
    $data = ['id_mapel'=>(int)($_POST['id_mapel'] ?? 0)];
    foreach (['nama_mapel','nama_guru','kelas'] as $k) $data[$k] = trim($_POST[$k] ?? '');
    if ($data['nama_mapel'] === '' || mb_strlen($data['nama_mapel']) > 100) $err[] = 'Nama mata pelajaran wajib diisi (maksimal 100 karakter).';
    if ($data['nama_guru'] === '' || mb_strlen($data['nama_guru']) > 100) $err[] = 'Nama guru wajib diisi (maksimal 100 karakter).';
    if ($data['kelas'] === '' || mb_strlen($data['kelas']) > 20) $err[] = 'Kelas wajib diisi (maksimal 20 karakter).';
    if (!$err) {
        if ($data['id_mapel']) {
            $st = $db->prepare("UPDATE mata_pelajaran SET nama_mapel=?,nama_guru=?,kelas=? WHERE id_mapel=?");
            $st->bind_param('sssi', $data['nama_mapel'], $data['nama_guru'], $data['kelas'], $data['id_mapel']);
            $pesan = 'Mata pelajaran berhasil diperbarui.';
        } else {
            $st = $db->prepare("INSERT INTO mata_pelajaran (nama_mapel,nama_guru,kelas) VALUES (?,?,?)");
            $st->bind_param('sss', $data['nama_mapel'], $data['nama_guru'], $data['kelas']);
            $pesan = 'Mata pelajaran berhasil ditambahkan.';
        }
        $st->execute(); flash($pesan); redirect('mapel.php');
    }
} elseif (isset($_GET['edit'])) {
    $st = $db->prepare("SELECT * FROM mata_pelajaran WHERE id_mapel=?"); $id = (int)$_GET['edit'];
    $st->bind_param('i', $id); $st->execute();
    $data = $st->get_result()->fetch_assoc() ?: $data;
}
$daftar = $db->query("SELECT * FROM mata_pelajaran ORDER BY nama_mapel");
include 'includes/header.php';
?>
<?php if ($err): ?><div class="alert err"><i class="fa-solid fa-circle-exclamation"></i><ul><?php foreach ($err as $m) echo '<li>'.e($m).'</li>'; ?></ul></div><?php endif; ?>
<section class="card">
  <h2><?= $data['id_mapel'] ? 'Edit mata pelajaran' : 'Tambah mata pelajaran' ?></h2>
  <form method="post" class="form">
    <input type="hidden" name="id_mapel" value="<?= (int)$data['id_mapel'] ?>">
    <label>Nama mata pelajaran<input type="text" name="nama_mapel" maxlength="100" required value="<?= e($data['nama_mapel']) ?>"></label>
    <label>Nama guru<input type="text" name="nama_guru" maxlength="100" required value="<?= e($data['nama_guru']) ?>"></label>
    <label>Kelas<input type="text" name="kelas" maxlength="20" required placeholder="X / XI / XII" value="<?= e($data['kelas']) ?>"></label>
    <div class="aksi-form">
      <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
      <?php if ($data['id_mapel']): ?><a href="mapel.php" class="btn abu"><i class="fa-solid fa-xmark"></i> Batal</a><?php endif; ?>
    </div>
  </form>
</section>
<section class="card">
  <h2>Daftar mata pelajaran</h2>
  <div class="tabel-wrap"><table>
    <tr><th>No</th><th>Mata pelajaran</th><th>Guru</th><th>Kelas</th><th>Aksi</th></tr>
    <?php if ($daftar->num_rows === 0): ?><tr><td colspan="5" class="kosong">Belum ada mata pelajaran.</td></tr><?php endif; ?>
    <?php $no = 1; while ($r = $daftar->fetch_assoc()): ?>
    <tr><td><?= $no++ ?></td><td><?= e($r['nama_mapel']) ?></td><td><?= e($r['nama_guru']) ?></td><td><?= e($r['kelas']) ?></td>
      <td class="aksi">
        <a class="btn kuning kecil" href="mapel.php?edit=<?= $r['id_mapel'] ?>"><i class="fa-solid fa-pen"></i> Edit</a>
        <form method="post" data-konfirmasi="Hapus mata pelajaran ini? Data tugas terkait juga ikut terhapus.">
          <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $r['id_mapel'] ?>">
          <button class="btn merah kecil"><i class="fa-solid fa-trash"></i> Hapus</button></form></td></tr>
    <?php endwhile; ?>
  </table></div>
</section>
<?php include 'includes/footer.php'; ?>
