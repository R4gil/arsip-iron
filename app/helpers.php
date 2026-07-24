<?php

if (!function_exists('sortUrl')) {
    function sortUrl(string $column, string $routeName = 'arsip.index'): string
    {
        $params = request()->all();
        $params['sort_by'] = $column;
        $params['sort_order'] = 'asc';

        // Toggle order if same column is clicked
        if ((request('sort_by') === $column || 
            (request('sort_by') === null && $column === 'arsip.id')) && 
            request('sort_order', 'desc') === 'asc') {
            $params['sort_order'] = 'desc';
        }

        return route($routeName, $params);
    }
}

if (!function_exists('sortIcon')) {
    function sortIcon(string $column): string
    {
        $currentSortBy = request('sort_by', 'arsip.id');
        $currentSortOrder = request('sort_order', 'desc');

        // Map short column to full
        $sortableColumns = [
            'nomor_surat', 'tanggal_arsip', 'nama_arsip', 'nama_jenis',
            'status', 'status_ketersediaan', 'masa_retensi', 'tanggal_retensi', 'status_retensi',
        ];

        // Normalize: if current is full column name like 'arsip.nomor_surat', derive short name
        $normalizedCurrent = $currentSortBy;
        foreach ($sortableColumns as $col) {
            if ($currentSortBy === 'arsip.' . $col || $currentSortBy === $col) {
                $normalizedCurrent = $col;
                break;
            }
        }

        if ($normalizedCurrent !== $column) {
            return ' <i class="fas fa-sort" style="opacity:0.3;font-size:0.65rem;"></i>';
        }

        if ($currentSortOrder === 'asc') {
            return ' <i class="fas fa-sort-up" style="opacity:0.8;font-size:0.65rem;"></i>';
        }

        return ' <i class="fas fa-sort-down" style="opacity:0.8;font-size:0.65rem;"></i>';
    }
}