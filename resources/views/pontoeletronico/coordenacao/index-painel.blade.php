@php
    $url_base = getenv('URL_BASE');
    $periodo = Carbon\CarbonPeriod::create($data_inicio, $data_fim);
    $data_fim = Carbon\Carbon::createFromFormat('Y-m-d', $data_fim);
    $data_inicio = Carbon\Carbon::createFromFormat('Y-m-d', $data_inicio);
    $periodo_proc = Carbon\Carbon::createFromFormat('Y-m-d', $data_fim->copy()->format('Y-m-d'));
    $periodo_proc_extenso = ucfirst($data_fim->copy()->locale('pt')->monthName);
    $mes_extenso_seguinte = ucfirst($data_fim->copy()->addMonth()->locale('pt')->monthName);
    $mes_extenso_prev = ucfirst($data_fim->copy()->subMonth()->locale('pt')->monthName);
@endphp
@extends('pontoeletronico.painel')
@section('conteudo')
    <!-- Header -->
    <section class="content-header">
        <h1>
            Painel de Coordenação
        </h1>
    </section>
    <!-- /Header -->
    <!-- Caixa de Filtros -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- Cabeçalho da caixa de filtros -->
                    <div class="box-header with-border">
                        <h3 class="box-title">Filtro por Colaborador</h3>
                    </div>
                    <!-- /Cabeçalho da caixa de filtros -->
                    <!-- Pesquisa -->
                    <form name ="form_pesquisa" method="POST" id="form_pesquisa" class="valid"
                        action="{{ route('painel.coordenacao', ['ano_mes' => $periodo_proc->format('Y-m')]) }}">
                        @csrf
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Colaborador</label>
                                        <select class="form-select" name="utilizador" required>
                                            <option value="" disabled selected>Seleccione um colaborador</option>
                                            <option value="ALL">TODOS</option>
                                            @if ($all_coordenandos)
                                                @foreach ($all_coordenandos as $coordenando)
                                                    <option value="{{ $coordenando->id }}">{{ $coordenando->nome }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer with-border">
                            <button type="submit" class="btn btn-primary float-end"><span>Pesquisar</span></button>
                        </div>
                    </form>
                    <!-- /Pesquisa -->
                </div>
            </div>
        </div>
        <!-- dashboard informaçoes mes -->
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between">
                <div>
                    <h3 class="box-title">Informações do Mês</h3>
                </div>
                <div class="d-flex align-self-center">
                    <a href="{{ action('PontoEletronico\AcompanhamentoController@index', ['ano_mes' => $periodo_proc->copy()->subMonth()->format('Y-m')]) }}"
                        class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left"></i>
                        {{ $mes_extenso_prev }}
                    </a>
                </div>
                <div class="d-flex align-self-center">
                    <h3 class="box-title ">
                        <strong>
                            {{ $periodo_proc_extenso }}
                        </strong>
                    </h3>
                </div>
                <div class="d-flex align-self-center">
                    <a href="{{ action('PontoEletronico\AcompanhamentoController@index', ['ano_mes' => $periodo_proc->copy()->addMonth()->format('Y-m')]) }}"
                        class="btn btn-sm btn-primary">
                        {{ $mes_extenso_seguinte }}
                        <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
                <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- Horas Expetaveis de Trabalho! -->
            <div class="box-body">
                <!-- Dias Expetaveis de Trabalho! -->
                <div class="info-box">
                    <span class="info-box-icon bg-green">
                        <i class="fa fa-calendar-check"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Dias Expectaveis Trabalhados</span>
                        <span class="info-box-number">{{ $horas_expectaveis_trabalhadas_mes / 8 }} dias</span>
                    </div>
                </div>
                <!-- /Horas Expetaveis de Trabalho! -->
                <div class="info-box">
                    <span class="info-box-icon bg-green">
                        <i class="fa fa-clock-o"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Horas Expectaveis Trabalhadas</span>
                        <span class="info-box-number">{{ $horas_expectaveis_trabalhadas_mes }} horas</span>
                    </div>
                </div>
                <!-- /Horas Expetaveis de Trabalho! -->
                <!-- Faltas Expectaveis Mes -->
                <div class="info-box">
                    <span class="info-box-icon bg-yellow">
                        <i class="fa fa-calendar-times-o"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Numero de Folgas Expectaveis</span>
                        <span class="info-box-number">{{ $ausencias_expectaveis_mes }} dias</span>
                    </div>
                </div>
                <!-- /Faltas Expectaveis Mes -->
                <!-- Feriados no mês -->
                <div class="info-box">
                    <span class="info-box-icon bg-aqua">
                        <i class="fa fa-plane"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Numero de Feriados</span>
                        <span
                            class="info-box-number">{{ $numero_feriados > 1 ? $numero_feriados . ' dias' : $numero_feriados . ' dia' }}
                        </span>
                    </div>
                </div>
                <!-- /Feriados no mês -->
            </div>
            <!-- /dashboard informaçoes mes -->
        </div>
        <!-- Tabela de Registos -->
        <div class="row">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Registos de Coordenandos do<strong> Departamento de {{ $departamento }}</strong>
                        de <strong> {{ $data_inicio->format('Y-m-d') }} a {{ $data_fim->format('Y-m-d') }}</strong></h3>
                </div>
                <div class="box-body no-padding">
                    <table class="table nowrap" id="coordenadorTable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Local</th>
                                <th>Cargo</th>
                                <th>Dias Trabalhados</th>
                                <th>Folgas</th>
                                <th>Folgas Trabalhadas</th>
                                <th>Faltas</th>
                                <th>Férias</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($coordenandos)
                                @foreach ($coordenandos as $coordenando)
                                    <tr onclick="window.location='{{ route('painel.coordenacao.utilizador', ['colaborador_id' => $coordenando->id, 'ano_mes' => $periodo_proc->format('Y-m')]) }}'"
                                        style="cursor: pointer;">
                                        <td>{{ $coordenando->nome }}</td>
                                        <td>{{ $coordenando->local }}</td>
                                        <td>{{ $coordenando->cargo }}</td>
                                        @if ($coordenando->controlo_user_mes != null)
                                            <td>
                                                {{ $coordenando->controlo_user_mes->horas_trabalhadas / 8 }}
                                                @if (
                                                    $coordenando->controlo_user_mes->horas_trabalhadas / 8 + $coordenando->controlo_user_mes->ferias <
                                                        $horas_expectaveis_trabalhadas_mes / 8)
                                                    <i class="fa fa-warning bg-warning"></i>
                                                @else
                                                    <i class="fa fa-check bg-success"></i>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $coordenando->controlo_user_mes->horas_folga / 8 }}
                                                @if ($coordenando->controlo_user_mes->horas_folga / 8 != $ausencias_expectaveis_mes)
                                                    <i class="fa fa-warning bg-warning"></i>
                                                @else
                                                    <i class="fa fa-check bg-success"></i>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $coordenando->controlo_user_mes->folgas_trabalhadas }}
                                            </td>
                                            <td>
                                                {{ $coordenando->controlo_user_mes->horas_ausencia / 8 }}
                                                @if ($coordenando->controlo_user_mes->horas_ausencia / 8 > 0)
                                                    <i class="fa fa-warning bg-warning"></i>
                                                @else
                                                    <i class="fa fa-check bg-success"></i>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $coordenando->controlo_user_mes->ferias }}
                                            </td>
                                        @else
                                            <td> - </td>
                                            <td> - </td>
                                            <td> - </td>
                                            <td> - </td>
                                            <td> - </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /Tabela de Registos -->
    </section>
@endsection
