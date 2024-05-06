@php
    $url_base = getenv('URL_BASE');
@endphp
@extends('pontoeletronico.painel')
@section('conteudo')
    @php

    @endphp
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
                        action="/painel/coordenacao/{{ $ano_mes->format('Y-m') }}">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Colaborador</label>
                                        <select class="form-control" name="utilizador" required>
                                            <option value="" disabled selected>Seleccione um colaborador</option>
                                            <option value="ALL">TODOS</option>
                                            @if ($coordenandos)
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
                    <a href="{{ action('PontoEletronico\AcompanhamentoController@index', ['ano_mes' => $ano_mes->copy()->subMonth()->format('Y-m')]) }}"
                        class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left"></i>
                        {{ ucfirst($ano_mes->copy()->subMonth()->locale('pt')->monthName) }}
                    </a>
                </div>
                <div class="d-flex align-self-center">
                    <h3 class="box-title ">
                        <strong>
                            {{ ucfirst($ano_mes->locale('pt')->monthName) }}
                        </strong>
                    </h3>
                </div>
                <div class="d-flex align-self-center">
                    <a href="{{ action('PontoEletronico\AcompanhamentoController@index', ['ano_mes' => $ano_mes->copy()->addMonth()->format('Y-m')]) }}"
                        class="btn btn-sm btn-primary">
                        {{ ucfirst($ano_mes->copy()->addMonth()->locale('pt')->monthName) }}
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
                <div class="info-box">
                    <span class="info-box-icon bg-green">
                        <i class="fa fa-clock-o"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Horas Expectaveis Trabalhadas</span>
                        <span class="info-box-number">{{ $horas_expectaveis_trabalhadas_mes }}</span>
                    </div>
                </div>
                <!-- /Horas Expetaveis de Trabalho! -->
                <!-- Faltas Expectaveis Mes -->
                <div class="info-box">
                    <span class="info-box-icon bg-yellow">
                        <i class="fa fa-plane"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Numero de Ausencias Expectaveis</span>
                        <span class="info-box-number">{{ $ausencias_expectaveis_mes }}</span>
                    </div>
                </div>
                <!-- /Faltas Expectaveis Mes -->
                <!-- Feriados no mês -->
                <div class="info-box">
                    <span class="info-box-icon bg-aqua">
                        <i class="fa fa-calendar-times-o"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Numero de Feriados</span>
                        <span class="info-box-number">{{ $numero_feriados }}</span>
                    </div>
                </div>
                <!-- /Feriados no mês -->
            </div>
            <!-- /dashboard informaçoes mes -->
        </div>
        <!-- Tabela de Registos -->
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Registos dos Colaboradores</h3>
                    <div class="box-tools">
                        <div class="input-group input-group-sm hidden-xs">
                            <input type="text" name="table_search" class="form-control float-end"
                                placeholder="Pesquisar...">
                            <div class="input-group-btn">
                                <button type='submit' class='btn btn-default'>
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Cargo</th>
                                <th>Nome</th>
                                <th>Horas trabalhadas</th>
                                <th>Numero Ausências</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($coordenandos)
                                @foreach ($coordenandos as $coordenando)
                                    <tr onclick="window.location='{{ route('painel.coordenacao.utilizador', ['colaborador_id' => $coordenando->id, 'ano_mes' => $ano_mes->format('Y-m')]) }}'"
                                        style="cursor: pointer;">
                                        <td>{{ $coordenando['local'] }}</td>
                                        <td>{{ $coordenando->cargo }}</td>
                                        <td>{{ $coordenando->nome }}</td>
                                        <td>
                                            {{ $coordenando->getHorasMes() }}
                                            @if ($coordenando->getHorasMes() < $horas_expectaveis_trabalhadas_mes)
                                                <i class="fa fa-warning bg-warning"></i>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $coordenando->getFaltasMes() }}
                                            @if ($coordenando->getFaltasMes() != $ausencias_expectaveis_mes)
                                                <i class="fa fa-warning bg-warning"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /Tabela de Registos -->
    </section>
@endsection
