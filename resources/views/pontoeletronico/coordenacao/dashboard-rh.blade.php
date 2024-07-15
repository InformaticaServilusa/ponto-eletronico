@extends('pontoeletronico.painel')
@php
    $ano_mes = \Carbon\Carbon::create($ano_mes);
    $mes_anterior = $ano_mes->copy()->subMonth();
    $mes_seguinte = $ano_mes->copy()->addMonth();
@endphp
@section('conteudo')
    <!-- Header -->
    <section class="content-header">
        <h1>
            Painel de Coordenação Recursos Humanos
        </h1>
    </section>
    <!-- /Header -->
    <!-- Caixa de Filtros -->
    <section class="content">
        <div class="box box-primary">
            <!-- Cabeçalho da caixa de filtros -->
            <div class="box-header with-border">
                <h3 class="box-title">Filtros</h3>
                <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /Cabeçalho da caixa de filtros -->
            <!-- Pesquisa -->
            <div class="box-body">
                <div class="row">
                    <!-- Filtro por Colabroador -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <form name ="form_pesquisa" method="POST" id="form_pesquisa" class="valid"
                                action="{{ route('painel.coordenacao.rh.with_ano_mes', ['ano_mes' => $ano_mes->format('Y-m')]) }}">
                                @csrf
                                <label>Colaborador</label>
                                <select class="form-select" name="colaborador">
                                    <option value="" disabled selected>Seleccione um colaborador</option>
                                    <option value="ALL">TODOS</option>
                                    @if ($all_utilizadores)
                                        @foreach ($all_utilizadores as $colaboradores)
                                            <option value="{{ $colaboradores->id }}">{{ $colaboradores->nome }}</option>
                                        @endforeach
                                    @endif
                                </select>
                        </div>
                    </div>
                    <!-- Filtro por Departamento -->
                    <div class="col">
                        <div class="form-group">
                            <label>Departamento</label>
                            <select class="form-select" name="departamento">
                                <option value="" disabled selected>Seleccione um departamento</option>
                                @if ($all_departamentos)
                                    @foreach ($all_departamentos as $departamento)
                                        <option value="{{ $departamento }}">{{ $departamento }}</option>
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
        <div class="box box-primary">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <div class="box-title d-flex align-items-center">
                    <div style="margin-right: 2rem;">
                        <a href="{{ route('painel.coordenacao.rh.with_ano_mes', ['ano_mes' => $mes_anterior->format('Y-m')]) }}"
                            class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i>
                            {{ ucfirst($mes_anterior->locale('pt')->monthName) }}</a>
                    </div>
                    <div>
                        <h3>Informações do Mês de <strong>{{ ucfirst($ano_mes->copy()->locale('pt')->monthName) }}</strong>
                        </h3>
                    </div>
                    <div>
                        <div style="margin-left: 2rem;">
                            <a href="{{ route('painel.coordenacao.rh.with_ano_mes', ['ano_mes' => $mes_seguinte->format('Y-m')]) }}"
                                class="btn btn-sm btn-primary"><i class="fa fa-arrow-right"></i>
                                {{ ucfirst($mes_seguinte->locale('pt')->monthName) }}</a>
                        </div>
                    </div>
                    <div class="box-tools ml-auto">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table" id="rhTable">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Departamento</th>
                            @php
                                $periodo = \Carbon\CarbonPeriod::create($data_inicio, $data_fim);
                            @endphp
                            @foreach ($periodo as $diasPeriodo)
                                <th class="@if ($diasPeriodo->isWeekend()) bg-primary @endif">
                                    {{ $diasPeriodo->format('d') }}</th>
                            @endforeach
                            <th>Dias Trabalhados</th>
                            <th>Feriados Trabalhados</th>
                            <th>Folgas Trabalhadas</th>
                            <th>Férias</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($display_colaboradores))
                            @foreach ($display_colaboradores as $colaborador)
                                <tr>
                                    <td>{{ $colaborador->nome }}</td>
                                    <td>{{ $colaborador->departamento }}</td>
                                    @foreach ($periodo as $diasPeriodo)
                                        @php
                                            $registo = $colaborador->registos[$diasPeriodo->format('Y-m-d')] ?? null;
                                            $tipo = null;
                                            if (isset($registo)) {
                                                $tipo = $registo->get_abreviatura_tipo();
                                                if (in_array($tipo, ['T', 'FT'])) {
                                                    $tipo = 'ponto';
                                                } else {
                                                    $tipo = 'ausencia';
                                                }
                                            }
                                        @endphp
                                        <td
                                            @if ($registo) data-bs-toggle="modal"
                                        data-bs-target="#display-registo-modal-{{ $tipo }}-{{ $registo->id ?? '' }}" @endif>
                                            @if ($registo)
                                                <strong>
                                                    <p
                                                        @if ($registo->status == 1) style = "color:green;" @else style = "color:red;" @endif>
                                                        {{ $registo->get_abreviatura_tipo() }}</p>
                                                </strong>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        {{ $colaborador->controlo_user_mes ? $colaborador->controlo_user_mes->horas_trabalhadas / 8 : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $colaborador->controlo_user_mes ? $colaborador->controlo_user_mes->feriados_trabalhados : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $colaborador->controlo_user_mes ? $colaborador->controlo_user_mes->folgas_trabalhadas : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $colaborador->controlo_user_mes ? $colaborador->controlo_user_mes->ferias : '-' }}
                                    </td>

                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                {{-- Modals Section --}}
                @if (isset($display_colaboradores))
                    @foreach ($display_colaboradores as $colaborador)
                        @foreach ($periodo as $diasPeriodo)
                            @php
                                $registo = $colaborador->registos[$diasPeriodo->format('Y-m-d')] ?? null;
                                $tipo = null;
                                if (isset($registo)) {
                                    $tipo = $registo->get_abreviatura_tipo();
                                    if (in_array($tipo, ['T', 'FT'])) {
                                        $tipo = 'ponto';
                                    } else {
                                        $tipo = 'ausencia';
                                    }
                                }
                            @endphp
                            @if ($registo)
                                @include('pontoeletronico.dashboard.modal.display-registo', [
                                    'tipo' => $tipo,
                                    'registo' => $registo,
                                ])
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
    </section>
@endsection
