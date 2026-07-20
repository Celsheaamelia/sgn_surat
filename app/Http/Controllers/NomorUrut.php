<?php

namespace App\Http\Controllers;

use App\Models\RiwayatSurat;

trait NomorUrut
{
    protected function usedNumbersForDate(string $tanggal): array
    {
        return RiwayatSurat::whereDate('tanggal', $tanggal)
            ->pluck('nomor_surat')
            ->map(function ($nomorSurat) {
                $parts = explode('.', $nomorSurat);
                return (int) end($parts);
            })
            ->unique()
            ->values()
            ->all();
    }

    protected function nextAvailableSequence(string $tanggal): int
    {
        $used = $this->usedNumbersForDate($tanggal);

        return $used ? max($used) + 1 : 1;
    }

    /**
     * Peta nomor urut -> status, untuk tanggal tertentu.
     * Dipakai buat bedain "sudah dipakai jadi surat" vs "masih di-keep".
     */
    protected function numberStatusMapForDate(string $tanggal): array
    {
        return RiwayatSurat::whereDate('tanggal', $tanggal)
            ->get(['nomor_surat', 'status'])
            ->mapWithKeys(function ($row) {
                $parts = explode('.', $row->nomor_surat);
                $seq = (int) end($parts);
                return [$seq => $row->status];
            })
            ->all();
    }

    /**
     * Kelompokkan nomor yang sudah terpakai di tanggal tertentu jadi 2 grup:
     * - terpakai: sudah jadi surat definitif (Terupload / Belum Terupload)
     * - direservasi: masih di-keep, belum jadi surat final
     */
    protected function groupedUsedNumbersForDate(string $tanggal): array
    {
        $map = $this->numberStatusMapForDate($tanggal);

        $terpakai = [];
        $direservasi = [];

        foreach ($map as $nomor => $status) {
            if ($status === 'Direservasi') {
                $direservasi[] = $nomor;
            } else {
                $terpakai[] = $nomor;
            }
        }

        sort($terpakai);
        sort($direservasi);

        return [
            'terpakai'    => $terpakai,
            'direservasi' => $direservasi,
        ];
    }
}
