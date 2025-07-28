<?php

namespace App;

enum StatusAntrian: string
{
    case MASUK = 'masuk';
    case PANGGIL = 'dipanggil';

    public function labels(): string
    {
        return match($this) {
            self::MASUK => 'masuk',
            self::PANGGIL => 'dipanggil',
        };
    }

    public function labelPowergridFilter(): string
    {
        return $this->labels();
    }
}
