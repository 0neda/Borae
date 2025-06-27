<?php

namespace App\Enums;

enum CategoriaEnum: string
{
    case CULTURAL = 'cultural';
    case ESPORTIVO = 'esportivo';
    case MUSICAL = 'musical';
    case TECNOLOGICO = 'tecnologico';
    case EDUCATIVO = 'educativo';
    case SOCIAL = 'social';
    case OUTROS = 'outros';

    public function getCategoria(): string
    {
        return match ($this) {
            self::CULTURAL => 'Cultural',
            self::ESPORTIVO => 'Esportivo',
            self::MUSICAL => 'Musical',
            self::TECNOLOGICO => 'Tecnológico',
            self::EDUCATIVO => 'Educativo',
            self::SOCIAL => 'Social',
            self::OUTROS => 'Outros',
        };
    }
}
