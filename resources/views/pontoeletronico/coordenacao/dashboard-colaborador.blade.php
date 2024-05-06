@php
    $url_base = getenv('URL_BASE');
    use Carbon\Carbon;
    use Carbon\CarbonPeriod;
    $ano_mes = Carbon::parse($ano_mes);
    $inicio_periodo = Carbon::parse($inicio_periodo);
    $fim_periodo = Carbon::parse($fim_periodo);
    $periodo = CarbonPeriod::create($inicio_periodo, $fim_periodo);
@endphp
@extends('pontoeletronico.painel')
@section('conteudo')
    <!-- HEADER -->
    <section class="content-header">
        <h1>
            Painel de Coordenação do Colaborado {{ $colaborador->nome }}
        </h1>
        <!-- /HEADER -->
        <!-- Dashboard -->
        <section class="content">
            <div class="box">
                <!-- Dashboard HEADER -->
                <div class="box-header with-border d-flex justify-content-between">
                    <div>
                        <h3 class="box-title">Informações do Mês</h3>
                    </div>
                    <div class="d-flex align-self-center">
                        <a href="{{ action('PontoEletronico\AcompanhamentoController@dashboardCoordenacao', [
                            'ano_mes' => $ano_mes->copy()->subMonth()->format('Y-m'),
                            'colaborador_id' => $colaborador->id,
                        ]) }}"
                            class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i>
                            {{ ucfirst($ano_mes->copy()->subMonth()->locale('pt')->monthName) }}</a>
                    </div>
                    <div class="d-flex align-self-center">
                        <h3 class="box-title">
                            <strong>
                                {{ ucfirst($ano_mes->locale('pt')->monthName) }} de {{ $ano_mes->year }}
                            </strong>
                        </h3>
                    </div>
                    <div class="d-flex align-self-center">
                        <a href="{{ action('PontoEletronico\AcompanhamentoController@dashboardCoordenacao', [
                            'ano_mes' => $ano_mes->copy()->addMonth()->format('Y-m'),
                            'colaborador_id' => $colaborador->id,
                        ]) }}"
                            class="btn btn-sm btn-primary">{{ ucfirst($ano_mes->copy()->addMonth()->locale('pt')->monthName) }}
                            <i class="fa fa-arrow-right"></i></a>
                    </div>
                    <div class="box-tools">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /Dashboard HEADER -->
                <!-- Dashboard Info -->
                <!-- Informaçao Horas Trabalhadas-->
                <div class="box-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-green">
                            <i class="fa fa-clock-o"></i>

                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Horas trabalhadas</span>
                            <span class="info-box-number">{{ $horas_trabalhadas }}</span>
                        </div>
                    </div>
                </div>
                <!-- /Informaçao Horas Trabalhadas-->
                <!-- Informaçao dias de ausencias-->
                <div class="box-body">
                    <span class="info-box-icon bg-yellow">
                        <i class="fa fa-plane"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Numero de Ausencias</span>
                        <span class="info-box-number">{{ $numero_ausencias }}</span>
                    </div>
                </div>
                <!-- /Informaçao dias de ausencias-->
            </div>
            <!-- /Dashboard Info -->
            <!-- Tabela Registos de Presenças/Ausenças -->
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Registos referentes ao periodo de {{ $inicio_periodo->format('Y-m-d') }} a
                        {{ $fim_periodo->format('Y-m-d') }}</h3>
                </div>
                <div class="box-body no-padding">
                    <table class="table text-center">
                        <thead class='thead-dark'>
                            <tr>
                                <th scope='col'></th>
                                <th scope='col'></th>
                                <th colspan=2 scope='col'>Manhã</th>
                                <th colspan=2 scope='col'>Tarde</th>
                                <th colspan=2 scope='col'>Noite</th>
                            </tr>
                            <tr>
                                <th>Data</th>
                                <th>Dia da Semana</th>
                                <th>Entrada</th>
                                <th>Saida</th>
                                <th>Entrada</th>
                                <th>Saida</th>
                                <th>Entrada</th>
                                <th>Saida</th>
                                <th>Validada</th>
                                <th>Tipo</th>
                                <th>Acção</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periodo as $dia)
                                @php
                                    $registo = isset($pontos[$dia->format('Y-m-d')])
                                        ? $pontos[$dia->format('Y-m-d')]
                                        : $ausencias[$dia->format('Y-m-d')] ?? null;
                                @endphp
                                <tr
                                    class="
                             @if (isset($pontos[$dia->format('Y-m-d')])) table-success
                             @elseif (isset($ausencias[$dia->format('Y-m-d')]))
                                 table-warning
                             @elseif ($dia->isWeekend()))
                                 table-info @endif
                                 ">
                                    <td>{{ $dia->format('Y-m-d') }}</td>
                                    <td>{{ ucfirst($dia->locale('pt')->dayName) }}</td>
                                    <td>{{ $registo ? $registo->entrada_manha ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->saida_manha ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->entrada_tarde ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->saida_tarde ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->entrada_noite ?? '--:--' : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->saida_noite ?? '--:--' : '--:--' }}</td>
                                    @if (isset($registo))
                                        @if ($registo->status == 1)
                                            <td>
                                                <a
                                                    href="{{ action('PontoEletronico\AcompanhamentoController@changeValidation', [
                                                        'registo_id' => $registo->id,
                                                        'changeTo' => 0,
                                                    ]) }}">✅</a>
                                            </td>
                                        @else
                                            <td>
                                                <a
                                                    href="{{ action('PontoEletronico\AcompanhamentoController@changeValidation', [
                                                        'registo_id' => $registo->id,
                                                        'changeTo' => 1,
                                                    ]) }}">❌</a>
                                            </td>
                                        @endif
                                    @endif
                                    <td>{{ $registo ? $registo->tipo_ponto->descricao ?? $registo->tipo_ausencia->descricao : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /Tabela Registos de Presenças/Ausenças -->
        </section>
    @endsection
