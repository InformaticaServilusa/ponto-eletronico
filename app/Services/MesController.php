<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;

class MesController
{
    public static function getHorasExpectaveisMes($ano_mes)
    {
        $inicio_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes . '-16')->subMonth();
        $fim_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes . '-15');
        $periodo = CarbonPeriod::create($inicio_periodo, $fim_periodo);
        return self::getWeekdays($periodo) * 8;
    }

    public static function getFeriadosMes($ano_mes)
    {
        $response = Http::get('https://date.nager.at/api/v3/publicholidays/2024/PT');
        $arr_feriados = [];
        $inicio_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes . '-16')->subMonth();
        $fim_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes . '-15');
        if ($response->successful()) {
            $feriados = $response->json();
            foreach ($feriados as $feriado) {
                $arr_feriados[$feriado['date']] = $feriado['localName'];
            }
        }

        $feriados_mes = collect($arr_feriados)->filter(function ($nome_feriado, $data_feriado) use ($inicio_periodo, $fim_periodo) {
            $feriado = Carbon::parse($data_feriado);
            return $feriado->isSameDay($inicio_periodo) || $feriado->isSameDay($fim_periodo) || $feriado->between($inicio_periodo, $fim_periodo);
        });

        return $feriados_mes;
    }

    public static function getWeekendsMes($ano_mes)
    {
        $inicio_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes . '-16')->subMonth();
        $fim_periodo = Carbon::createFromFormat('Y-m-d', $ano_mes . '-15');
        $periodo = CarbonPeriod::create($inicio_periodo, $fim_periodo);
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
