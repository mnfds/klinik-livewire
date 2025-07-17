<?php

namespace App;

enum TipeMutasiBarang: string
{
    case MASUK = 'masuk';
    case KELUAR = 'keluar';

    public function labels(): string
    {
        return match($this) {
            self::MASUK => 'masuk',
            self::KELUAR => 'keluar',
        };
    }

    public function labelPowergridFilter(): string
    {
        return $this->labels();
    }
}

