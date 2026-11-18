<?php
$menu = [
  'index.php'  => ['Dashboard', 'fa-gauge-high'],
  'siswa.php'  => ['Data Siswa', 'fa-user-graduate'],
  'mapel.php'  => ['Mata Pelajaran', 'fa-book-open'],
  'tugas.php'  => ['Pengumpulan Tugas', 'fa-clipboard-check'],
];
$aktif = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> – TugasIn</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<aside class="sidebar" id="sidebar">
  <div class="brand"><i class="fa-solid fa-pen-ruler"></i> TugasIn</div>
  <nav>
    <?php foreach ($menu as $file => [$label, $ikon]): ?>
      <a href="<?= $file ?>" class="<?= $aktif === $file ? 'aktif' : '' ?>"><i class="fa-solid <?= $ikon ?>"></i> <?= $label ?></a>
    <?php endforeach; ?>
  </nav>
  <small class="sb-foot">Pengumpulan tugas sekolah</small>
</aside>
<main class="konten">
  <header class="topbar">
    <button class="burger" id="burger" aria-label="Buka menu"><i class="fa-solid fa-bars"></i></button>
    <h1><?= e($judul) ?></h1>
  </header>
  <?php if ($f = flash()): ?>
    <div class="alert <?= $f[1] ?>" data-auto><i class="fa-solid <?= $f[1] === 'ok' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i> <?= e($f[0]) ?></div>
  <?php endif; ?>
