// Menu mobile
document.getElementById('burger').addEventListener('click', () =>
  document.getElementById('sidebar').classList.toggle('buka'));

// Konfirmasi hapus
document.querySelectorAll('form[data-konfirmasi]').forEach(f =>
  f.addEventListener('submit', e => { if (!confirm(f.dataset.konfirmasi)) e.preventDefault(); }));

// Notifikasi hilang otomatis
document.querySelectorAll('.alert[data-auto]').forEach(a =>
  setTimeout(() => a.remove(), 4000));

// Validasi form sisi klien: hapus spasi berlebih, tampilkan pesan per kolom
document.querySelectorAll('form.form').forEach(form => {
  form.addEventListener('submit', e => {
    form.querySelectorAll('input[type=text],input[type=email]').forEach(i => i.value = i.value.trim());
    if (!form.checkValidity()) { e.preventDefault(); form.reportValidity(); }
  });
});
