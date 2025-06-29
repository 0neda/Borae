<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Alerta
{
    public string $type = 'info';
    public string $message;

    public function getIcon(): string
    {
        return match ($this->type) {
            'success' => 'ph-check-circle',
            'warning' => 'ph-warning',
            'error' => 'ph-warning',
            'info' => 'ph-info',
            default => 'ph-dots-three-circle',
        };
    }

    public function getClass(): string
    {
        return match ($this->type) {
            'success' => 'alert-success',
            'warning' => 'alert-warning',
            'error' => 'alert-error',
            'info' => 'alert-info',
            default => 'alert-info',
        };
    }
}
