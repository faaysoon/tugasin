<?php
require 'koneksi.php';
$judul = 'Pengumpulan Tugas';
$err = [];
$STATUS = ['Sudah Dikumpulkan', 'Belum Dikumpulkan'];
$data = ['id_pengumpulan'=>0,'id_siswa'=>0,'id_mapel'=>0,'nama_tugas'=>'','tanggal_pengumpulan'=>date('Y-m-d'),'status'=>'Belum Dikumpulkan'];

$siswa = $db->query("SELECT id_siswa, nama_siswa, kelas FROM siswa ORDER BY nama_siswa")->fetch_all(MYSQLI_ASSOC);
$mapel = $db->query("SELECT id_mapel, nama_mapel, kelas FROM mata_pelajaran ORDER BY nama_mapel")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['aksi'] ?? '') === 'hapus') {
        $id = (int)$_POST['id'];
        $st = $db->prepare("DELETE FROM pengumpulan_tugas WHERE id_pengumpulan=?"); $st->bind_param('i', $id); $st->execute();
        flash('Data pengumpulan tugas berhasil dihapus.'); redirect('tugas.php');
    }
    $data = [
        'id_pengumpulan' => (int)($_POST['id_pengumpulan'] ?? 0),
        'id_siswa' => (int)($_POST['id_siswa'] ?? 0),
        'id_mapel' => (int)($_POST['id_mapel'] ?? 0),
        'nama_tugas' => trim($_POST['nama_tugas'] ?? ''),
        'tanggal_pengumpulan' => trim($_POST['tanggal_pengumpulan'] ?? ''),
        'status' => $_POST['status'] ?? '',
    ];
    if (!in_array($data['id_siswa'], array_column($siswa, 'id_siswa'))) $err[] = 'Pilih siswa dari daftar yang tersedia.';
    if (!in_array($data['id_mapel'], array_column($mapel, 'id_mapel'))) $err[] = 'Pilih mata pelajaran dari daftar yang tersedia.';
    if ($data['nama_tugas'] === '' || mb_strlen($data['nama_tugas']) > 100) $err[] = 'Nama tugas wajib diisi (maksimal 100 karakter).';
    $d = DateTime::createFromFormat('Y-m-d', $data['tanggal_pengumpulan']);
    if (!$d || $d->format('Y-m-d') !== $data['tanggal_pengumpulan']) $err[] = 'Tanggal pengumpulan tidak valid.';
    if (!in_array($data['status'], $STATUS, true)) $err[] = 'Status tugas tidak valid.';
    if (!$err) {
        if ($data['id_pengumpulan']) {
            $st = $db->prepare("UPDATE pengumpulan_tugas SET id_siswa=?,id_mapel=?,nama_tugas=?,tanggal_pengumpulan=?,status=? WHERE id_pengumpulan=?");
            $st->bind_param('iisssi', $data['id_siswa'], $data['id_mapel'], $data['nama_tugas'], $data['tanggal_pengumpulan'], $data['status'], $data['id_pengumpulan']);
            $pesan = 'Data pengumpulan tugas berhasil diperbarui.';
        } else {
            $st = $db->prepare("INSERT INTO pengumpulan_tugas (id_siswa,id_mapel,nama_tugas,tanggal_pengumpulan,status) VALUES (?,?,?,?,?)");
            $st->bind_param('iisss', $data['id_siswa'], $data['id_mapel'], $data['nama_tugas'], $data['tanggal_pengumpulan'], $data['status']);
            $pesan = 'Data pengumpulan tugas berhasil ditambahkan.';
        }
        $st->execute(); flash($pesan); redirect('tugas.php');
    }
} elseif (isset($_GET['edit'])) {
    $st = $db->prepare("SELECT * FROM pengumpulan_tugas WHERE id_pengumpulan=?"); $id = (int)$_GET['edit'];
    $st->bind_param('i', $id); $st->execute();
    $data = $st->get_result()->fetch_assoc() ?: $data;
}
$daftar = $db->query("SELECT t.*, s.nama_siswa, m.nama_mapel FROM pengumpulan_tugas t
  JOIN siswa s ON s.id_siswa=t.id_siswa JOIN mata_pelajaran m ON m.id_mapel=t.id_mapel
  ORDER BY t.tanggal_pengumpulan DESC, t.id_pengumpulan DESC");
include 'includes/header.php';
?>
<?php if ($err): ?><div class="alert err"><i class="fa-solid fa-circle-exclamation"></i><ul><?php foreach ($err as $m) echo '<li>'.e($m).'</li>'; ?></ul></div><?php endif; ?>
<section class="card">
  <h2><?= $data['id_pengumpulan'] ? 'Edit pengumpulan tugas' : 'Tambah pengumpulan tugas' ?></h2>
  <?php if (!$siswa || !$mapel): ?>
    <p class="kosong">Tambahkan <a href="siswa.php">data siswa</a> dan <a href="mapel.php">mata pelajaran</a> terlebih dahulu.</p>
  <?php else: ?>
  <form method="post" class="form">
    <input type="hidden" name="id_pengumpulan" value="<?= (int)$data['id_pengumpulan'] ?>">
    <label>Siswa<select name="id_siswa" required><option value="">– Pilih siswa –</option>
      <?php foreach ($siswa as $s): ?><option value="<?= $s['id_siswa'] ?>" <?= $s['id_siswa'] == $data['id_siswa'] ? 'selected' : '' ?>><?= e($s['nama_siswa'].' ('.$s['kelas'].')') ?></option><?php endforeach; ?></select></label>
    <label>Mata pelajaran<select name="id_mapel" required><option value="">– Pilih mata pelajaran –</option>
      <?php foreach ($mapel as $m): ?><option value="<?= $m['id_mapel'] ?>" <?= $m['id_mapel'] == $data['id_mapel'] ? 'selected' : '' ?>><?= e($m['nama_mapel'].' (Kelas '.$m['kelas'].')') ?></option><?php endforeach; ?></select></label>
    <label>Nama tugas<input type="text" name="nama_tugas" maxlength="100" required value="<?= e($data['nama_tugas']) ?>"></label>
    <label>Tanggal pengumpulan<input type="date" name="tanggal_pengumpulan" required value="<?= e($data['tanggal_pengumpulan']) ?>"></label>
    <label>Status<select name="status" required>
      <?php foreach ($STATUS as $s): ?><option <?= $s === $data['status'] ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></label>
    <div class="aksi-form">
      <button class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
      <?php if ($data['id_pengumpulan']): ?><a href="tugas.php" class="btn abu"><i class="fa-solid fa-xmark"></i> Batal</a><?php endif; ?>
    </div>
  </form>
  <?php endif; ?>
</section>
<section class="card">
  <h2>Daftar pengumpulan tugas</h2>
  <div class="tabel-wrap"><table>
    <tr><th>No</th><th>Siswa</th><th>Mata pelajaran</th><th>Tugas</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
    <?php if ($daftar->num_rows === 0): ?><tr><td colspan="7" class="kosong">Belum ada data pengumpulan tugas.</td></tr><?php endif; ?>
    <?php $no = 1; while ($r = $daftar->fetch_assoc()): ?>
    <tr><td><?= $no++ ?></td><td><?= e($r['nama_siswa']) ?></td><td><?= e($r['nama_mapel']) ?></td><td><?= e($r['nama_tugas']) ?></td>
      <td><?= date('d/m/Y', strtotime($r['tanggal_pengumpulan'])) ?></td>
      <td><span class="badge <?= $r['status'] === 'Sudah Dikumpulkan' ? 'sudah' : 'belum' ?>"><?= e($r['status']) ?></span></td>
      <td class="aksi">
        <a class="btn kuning kecil" href="tugas.php?edit=<?= $r['id_pengumpulan'] ?>"><i class="fa-solid fa-pen"></i> Edit</a>
        <form method="post" data-konfirmasi="Hapus data pengumpulan tugas ini?">
          <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $r['id_pengumpulan'] ?>">
          <button class="btn merah kecil"><i class="fa-solid fa-trash"></i> Hapus</button></form></td></tr>
    <?php endwhile; ?>
  </table></div>
</section>
<?php include 'includes/footer.php'; ?>
