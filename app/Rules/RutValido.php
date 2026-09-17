<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class RutValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // aquí quitamos puntos y guion para trabajar solo y recalco, solo con los numeros
        $rut = strtoupper(str_replace(['.', '-'], '', $value));

        if (!preg_match('/^\d{7,8}[0-9K]$/', $rut)) {
            $fail('El formato del rut no es valido.');
            return;
        }

        $cuerpo = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        $suma = 0;
        $multiplicador = 2;

        for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
            $suma += (int) $cuerpo[$i] * $multiplicador;
            $multiplicador = $multiplicador === 7 ? 2 : $multiplicador + 1;
        }

        $resto = 11 - ($suma % 11);
        $dvEsperado = match (true) {
            $resto === 11 => '0',
            $resto === 10 => 'K',
            default => (string) $resto,
        };

        if ($dv !== $dvEsperado) {
            $fail('El rut ingresado no es valido.');
        }
    }
}