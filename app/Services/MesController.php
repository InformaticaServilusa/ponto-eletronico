<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;

class MesController
{
    public static function get_horas_expect_mes($data_inicio, $data_fim)
    {
        $periodo = CarbonPeriod::create($data_inicio, $data_fim);
        return self::getWeekdays($periodo) * 8;
    }

    public static function get_feriados_mes($data_inicio, $data_fim)
    {
        //TODO:dividir os feriados em feriados ao fim de semana, e fora fim de semana
        //para facilitar os calculos
        $response = Http::get('https://date.nager.at/api/v3/publicholidays/2024/PT');
        $arr_feriados = [];
        if ($response->successful()) {
            $feriados = $response->json();
            foreach ($feriados as $feriado) {
                $arr_feriados[$feriado['date']] = $feriado['localName'];
            }
        }

        $feriados_mes = collect($arr_feriados)->filter(function ($nome_feriado, $data_feriado) use ($data_inicio, $data_fim) {
            $feriado = Carbon::parse($data_feriado);
            return $feriado->isSameDay($data_inicio) || $feriado->isSameDay($data_fim) || $feriado->between($data_inicio, $data_fim);
        });

        return $feriados_mes;
    }

    public static function get_weekend_mes($data_inicio, $data_fim)
    {
        $periodo = CarbonPeriod::create($data_inicio, $data_fim);
        return self::getWeekends($periodo);
    }

    private static function getWeekends($periodo)
    {
        $weekends = 0;
        foreach ($periodo as $day) {
            if ($day->isWeekend()) {
                $weekends++;
            }
        }
        return $weekends;
    }

    private static function getWeekdays($periodo)
    {
        $weekdays = 0;
        foreach ($periodo as $day) {
            if ($day->isWeekday()) {
                $weekdays++;
            }
        }
        return $weekdays;
    }
}
