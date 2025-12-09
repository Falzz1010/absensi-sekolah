# 📊 Dashboard Widgets - Quick Reference

## Widget List (6 Total)

| # | Widget | Deskripsi | Sort |
|---|--------|-----------|------|
| 1 | StatsOverview | Statistik hari ini (Total, Hadir, Sakit, Izin, Alfa) | 1 |
| 2 | AbsensiChart | Grafik line 7 hari terakhir (4 status) | 2 |
| 3 | RekapMingguan | Statistik minggu ini dengan breakdown | 3 |
| 4 | RekapBulanan | Statistik bulan ini dengan breakdown | 4 |
| 5 | RankingKehadiranKelas | Top kelas dengan medali 🥇🥈🥉 | 5 |
| 6 | RekapAbsensiKelas | Tabel rekap per kelas hari ini | 6 |

## Files

```
app/Filament/Widgets/
├── StatsOverview.php
├── AbsensiChart.php
├── RekapMingguan.php
├── RekapBulanan.php
├── RankingKehadiranKelas.php
└── RekapAbsensiKelas.php

resources/views/filament/widgets/
├── rekap-absensi-kelas.blade.php
└── ranking-kehadiran-kelas.blade.php
```

## Status

✅ All 6 widgets implemented and working
✅ Auto-discovered by Filament
✅ Responsive design
✅ Real-time data
