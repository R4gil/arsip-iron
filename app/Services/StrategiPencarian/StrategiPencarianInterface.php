<?php

namespace App\Services\StrategiPencarian;

interface StrategiPencarianInterface
{
    /**
     * Cari arsip berdasarkan query
     *
     * @param string $query
     * @param array $options
     * @return \Illuminate\Support\Collection
     */
    public function cari(string $query, array $options = []): \Illuminate\Support\Collection;

    /**
     * Dapatkan nama strategi
     *
     * @return string
     */
    public function dapatkanNama(): string;

    /**
     * Cek apakah strategi tersedia
     *
     * @return bool
     */
    public function cekKetersediaan(): bool;
}
