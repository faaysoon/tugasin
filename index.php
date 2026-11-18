<?php
require 'koneksi.php';
$judul = 'Dashboard';
$n = fn($sql) => (int)$db->query($sql)->fetch_row()[0];
$jml_siswa = $n("SELECT COUNT(*) FROM siswa");
$jml_mapel = $n("SELECT COUNT(*) FROM mata_pelajaran");
$sudah     = $n("SELECT COUNT(*) FROM pengumpulan_tugas WHERE status='Sudah Dikumpulkan'");
$belum     = $n("SELECT COUNT(*) FROM pengumpulan_tugas WHERE status='Belum Dikumpulkan'");
$terbaru = $db->query("SELECT t.*, s.nama_siswa, m.nama_mapel FROM pengumpulan_tugas t
  JOIN siswa s ON s.id_siswa=t.id_siswa JOIN mata_pelajaran m ON m.id_mapel=t.id_mapel
  ORDER BY t.tanggal_pengumpulan DESC, t.id_pengumpulan DESC LIMIT 5");
include 'includes/header.php';
?>
<section class="stats">
  <div class="stat"><div class="ik"><i class="fa-solid fa-user-graduate"></i></div><div><b><?= $jml_siswa ?></b><span>Jumlah siswa</span></div></div>
  <div class="stat"><div class="ik"><i class="fa-solid fa-book-open"></i></div><div><b><?= $jml_mapel ?></b><span>Mata pelajaran</span></div></div>
  <div class="stat"><div class="ik"><i class="fa-solid fa-circle-check"></i></div><div><b><?= $sudah ?></b><span>Tugas dikumpulkan</span></div></div>
  <div class="stat"><div class="ik"><i class="fa-solid fa-hourglass-half"></i></div><div><b><?= $belum ?></b><span>Belum dikumpulkan</span></div></div>
</section>
<section class="card">
  <h2>Aktivitas tugas terbaru</h2>
  <div class="tabel-wrap"><table>
    <tr><th>Siswa</th><th>Mata pelajaran</th><th>Tugas</th><th>Tanggal</th><th>Status</th></tr>
    <?php if ($terbaru->num_rows === 0): ?><tr><td colspan="5" class="kosong">Belum ada data tugas.</td></tr><?php endif; ?>
    <?php while ($r = $terbaru->fetch_assoc()): ?>
    <tr><td><?= e($r['nama_siswa']) ?></td><td><?= e($r['nama_mapel']) ?></td><td><?= e($r['nama_tugas']) ?></td>
      <td><?= date('d/m/Y', strtotime($r['tanggal_pengumpulan'])) ?></td>
      <td><span class="badge <?= $r['status'] === 'Sudah Dikumpulkan' ? 'sudah' : 'belum' ?>"><?= e($r['status']) ?></span></td></tr>
    <?php endwhile; ?>
  </table></div>
</section>
<?php include 'includes/footer.php'; ?>
