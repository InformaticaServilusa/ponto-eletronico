@extends('pontoeletronico.painel')

@section('conteudo')
    <?php

    $dia_da_semana[0] = 'Domingo';
    $dia_da_semana[1] = 'Segunda-feira';
    $dia_da_semana[2] = 'Terça-feira';
    $dia_da_semana[3] = 'Quarta-feira';
    $dia_da_semana[4] = 'Quinta-feira';
    $dia_da_semana[5] = 'Sexta-feira';
    $dia_da_semana[6] = 'Sábado';
    $mes = [
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Março',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro',
    ];

    $dia_extenso = $dia_da_semana[Date('w')];
    $mes_extenso_atual = $mes[$mes_atual];
    $mes_extenso_seguinte = $mes[$prox_mes];

    $hora = Date('H:i');
    ?>
    <script>
        function registarDiasTrabalho() {
            var data = [];
            var checkboxes = document.getElementsByName('checked_data[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (checkboxes[i].checked) {
                    data.push(checkboxes[i].value);
                }
            }
            if (data.length > 0) {
                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'data';
                hiddenInput.value = JSON.stringify(data);
                document.querySelector('form[name="form-entrada-trabalho"]').appendChild(hiddenInput);
                document.querySelector('form[name="form-entrada-trabalho"]').submit();
            } else {
                //TODO: Melhor mensagem de erro.
                alert('Selecione pelo menos um dia para registar');
            }

        }

        function registarDiasFolga() {
            var data = [];
            var checkboxes = document.getElementsByName('checked_data[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (checkboxes[i].checked) {
                    data.push(checkboxes[i].value);
                }
            }
            if (data.length > 0) {
                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'data';
                hiddenInput.value = JSON.stringify(data);
                document.querySelector('form[name="form-entrada-folga"]').appendChild(hiddenInput);
                document.querySelector('form[name="form-entrada-folga"]').submit();
            } else {
                //TODO: Melhor mensagem de erro.
                alert('Selecione pelo menos um dia para registar');
            }
        }

        function registarJustificacao() {
            var data = [];
            var checkboxes = document.getElementsByName('checked_data[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                if (checkboxes[i].checked) {
                    data.push(checkboxes[i].value);
                }
            }
            if (data.length > 0) {
                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'data';
                hiddenInput.value = JSON.stringify(data);
                document.querySelector('form[name="form-entrada-justificacao"]').appendChild(hiddenInput);
                document.querySelector('form[name="form-entrada-justificacao"]').submit();
            } else {
                //TODO: Melhorar mensagem de erro
                alert('Selecione pelo menos um dia para registar');
            }
        }

        function handleEdit() {
            alert('Editar');
        }

        function handleRemove() {
            alert('Remover');
        }
    </script>
    <section class='content'>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ $utilizador->nome }}</h3>
                    </div>
                    <div class="box-body">
                        <strong><i class="fas fa-user-tie margin-r-5"></i> Cargo:</strong>
                        {{ $utilizador->cargo ?? 'Não definido' }} <br>
                        <strong><i class="fas fa-user-secret margin-r-5"></i> Coordenador:</strong>
                        {{ $utilizador->coordenador->nome ?? 'Não definido' }}<br>
                        <strong><i class="fas fa-map-marker-alt margin-r-5"></i> Local:</strong>
                        {{ $utilizador->local ?? 'Não definido' }}
                    </div>
                </div>
            </div>
        </div>
        <div class='row'>
            {{-- Caixa com informação a que mês corresponde a visualição
                    TODO: POSSO UTILIZAR AQUI ESTA PASSAGEM PARA LEVAR O CONTROLO MAISMES MENOSMES e ASSIM EM PHP AFETO O MES.
                    MAIS CONTROLO SOBRE O ANO --}}
            <div class="box box-primary">
                <div class="row">
                    <div class="box-header with-border text-center">
                        <div class="col-md-4 text-right">
                            <a href="{{ action('PontoEletronico\DashboardPainelController@index', ['ano_atual' => $ano_atual, 'mes_atual' => sprintf('%02d', $mes_atual - 1)]) }}"
                                class="btn btn-sm btn-primary"><i class="fa fa-arrow-left"></i> Mês
                                Anterior</a>
                        </div>
                        <div class="col-md-4">
                            <h3 class="box-title "><strong>{{ $mes_extenso_atual }}</strong></h3>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ action('PontoEletronico\DashboardPainelController@index', ['ano_atual' => $ano_atual, 'mes_atual' => sprintf('%02d', $mes_atual + 1)]) }}"
                                class="btn btn-sm btn-primary">Mês Seguinte <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div style="display:flex;">
                    <div class="info-box" style="background-color: #4cc768;">
                        <span class="info-box-icon"><i class="fa fa-briefcase"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><strong>Total de horas trabalhadas em
                                    {{ $mes_extenso_atual }}</strong></span>
                            <span class="info-box-number">{{ $horas_mes ?? 'X' }} horas</span>
                            {{-- <div class="progress">
                                <div class="progress-bar" style="width: 70%"></div>
                            </div>
                            <span class="progress-description">
                                70% Increase in 30 Days
                            </span> --}}
                        </div>
                    </div>
                    <div class="info-box" style="background-color: #eec95cfd;">
                        <span class="info-box-icon"><i class="fa fa-bed"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><strong>Total de folgas em
                                    {{ $mes_extenso_atual }}</strong></span>
                            <span class="info-box-number">{{ $folgas_mes ?? 'X' }} dias</span>
                            {{-- <div class="progress">
                                <div class="progress-bar" style="width: 70%"></div>
                            </div>
                            <span class="progress-description">
                                70% Increase in 30 Days
                            </span> --}}
                        </div>
                    </div>
                </div>
                <div class="box-body box-profile text-center">
                    {{-- <i class="far fa-clock fa-5x"></i>
                    <h3 class="profile-username text-center">
                        {{ $dia_extenso }} , {{ Date('d') }} de {{ $mes[Date('m')] }} de {{ Date('Y') }} </h3>
                    <p class="text-muted text-center" style='font-size: 50px;'>
                        {{ Date('H:i') }}
                    </p> --}}
                    <div class="row">
                        <a href="javascript:void(0)" onClick="registarDiasTrabalho()" class="btn btn-md btn-success"><i
                                class="fa fa-plus"></i> Registar dia de Trabalho</a>
                        <a href="javascript:void(0)" onClick="registarDiasFolga()" class="btn btn-md btn-warning"><i
                                class="fa fa-plus"></i> Registar folga</a>
                        {{-- <a href="javascript:void(0)" onClick="registarJustificacao()" class="btn btn-md btn-danger"><i
                                class="fa fa-plus"></i> Registar justificação de falta</a> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Livre de Ponto de <strong>16 de {{ $mes_extenso_atual }} a 15
                            {{ $mes_extenso_seguinte }} </strong></h3>
                </div>
                <div class="box-body no-padding">
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th colspan=2>Manhã</th>
                                <th colspan=2>Tarde</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Data</th>
                                <th>Dia da Semana</th>
                                <th>Entrada</th>
                                <th>Saída</th>
                                <th>Entrada</th>
                                <th>Saída</th>
                                <th>Validada</th>
                                <th>Tipo</th>
                                <th>Accao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $startDateString = sprintf('%04d-%02d-%02d', $ano_atual, $mes_atual, 16);
                                $startDate = new DateTime($startDateString);
                                $endDate = new DateTime($startDateString);
                                $endDate->modify('+1 month');
                                $periodo = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);
                            @endphp
                            @foreach ($periodo as $dia)
                                @php
                                    $dia = $dia->format('Y-m-d');
                                    $numero_dia_semana = date('w', strtotime($dia));
                                    $registo = isset($registos_ponto[$dia]) ? $registos_ponto[$dia] : null;
                                @endphp

                                <tr
                                    class="
                                @if (($numero_dia_semana == 0 || $numero_dia_semana == 6) && !isset($registos_ponto[$dia])) bg-danger
                                @elseif (isset($registos_ponto[$dia]) && $registos_ponto[$dia]->status == 1 && $registos_ponto[$dia]->tipo_ponto->id == 1)
                                    bg-success
                                @elseif (isset($registos_ponto[$dia]) && $registos_ponto[$dia]->status == 1 && $registos_ponto[$dia]->tipo_ponto->id == 2)
                                    bg-warning @endif
                                    ">
                                    <td>
                                        @if ((isset($registo->status) && $registo->status !== 1) || !isset($registo))
                                            <input type="checkbox" id="checked_data" name="checked_data[]"
                                                value="{{ $dia }}">
                                        @endif
                                    </td>
                                    <td> {{ $dia }} </td>
                                    <td>{{ (string) $dia_da_semana[date('w', strtotime($dia))] }}</td>
                                    <td>{{ $registo ? $registo->entrada_manha : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->saida_manha : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->entrada_tarde : '--:--' }}</td>
                                    <td>{{ $registo ? $registo->saida_tarde : '--:--' }}</td>
                                    @if (isset($registo->status) && $registo->status == 1)
                                        <td>✅</td>
                                    @else
                                        <td>❌</td>
                                    @endif
                                    <td>{{ $registo ? $registo->tipo_ponto->descricao : '' }}</td>
                                    @if (isset($registo))
                                        <td>
                                            <a href="#modal-edicao-ponto-{{ $registo->id }}" data-toggle="modal">
                                                <button class="action-button"><i class="fas fa-edit"></i></button>
                                            </a>
                                            <a href='#' data-url="/painel/ponto/eliminar/{{ $registo->id }}"
                                                data-msg="Deseja eliminar este registo?"
                                                class="btn btn-xs btn-danger btnExluir">
                                                <i class="fa fa-trash"></i></a>
                                        </td>
                                    @else
                                        <td>
                                            <div style="display:flex; justify-content: center;">
                                                <form method='post' action='/painel/ponto/submeterTrabalho'
                                                    name='form-entrada-trabalho'>
                                                    {{ csrf_field() }}
                                                    <input type='hidden' name='data' value='{{ json_encode($dia) }}'>
                                                    <button type="submit" title='Inserir dia de trabalho completo'
                                                        class="btn btn-xs btn-success">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </form>
                                                <form method='post' action='/painel/ponto/submeterFolga'
                                                    name='form-entrada-folga'>
                                                    {{ csrf_field() }}
                                                    <input type='hidden' name='data' value='{{ json_encode($dia) }}'>
                                                    <button type="submit" title='Inserir folga'
                                                        class="btn btn-xs btn-warning">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </form>
                                                {{-- <form method='post' action='/painel/ponto/submeterJustificacao'
                                                    name='form-entrada-justificacao'>
                                                    {{ csrf_field() }}
                                                    <input type='hidden' name='data' value='{{ json_encode($dia) }}'>
                                                    <button type="submit" title='Inserir justificação de falta'
                                                        class="btn btn-xs btn-danger">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </form> --}}
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                <!-- Modal para incluir um novo registo de ponto -->
                                <div id="modal-edicao-ponto-{{ $registo->id ?? '' }}" class="modal fade" tabindex="-1"
                                    role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header text-center">
                                                <h2 class="modal-title">Editar Registo de ponto</h2>
                                            </div>
                                            <form method="post" action="/painel/ponto/editar/"
                                                enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <input type='hidden' name='registo_id'
                                                        value='{{ $registo->id ?? '' }}'>
                                                    <div class="form-group">
                                                        <fieldset>
                                                            <legend>Manhã</legend>
                                                            <div class="col-md-4">
                                                                <input type="text" name="entrada_manha"
                                                                    class="form-control time"
                                                                    placeholder="Hora de Entrada"
                                                                    value="{{ $registo->entrada_manha ?? '' }}">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" name="saida_manha"
                                                                    class="form-control time" placeholder="Hora de Saída"
                                                                    value="{{ $registo->saida_manha ?? '' }}">
                                                            </div>
                                                    </div>
                                                    </fieldset>
                                                    <div class="form-group">
                                                        <fieldset>
                                                            <legend>Tarde</legend>
                                                            <div class="col-md-4">
                                                                <input type="text" name="entrada_tarde"
                                                                    class="form-control time"
                                                                    placeholder="Hora de Entrada"
                                                                    value="{{ $registo->entrada_tarde ?? '' }}">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" name="saida_tarde"
                                                                    class="form-control time" placeholder="Hora de Saída"
                                                                    value="{{ $registo->saida_tarde ?? '' }}">
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="form-group">
                                                        <fieldset>
                                                            <legend>Observações</legend>
                                                            <textarea id="colab_obs" name="colab_obs" class="form-control" placeholder="Escreva aqui as suas observações"
                                                                rows="3" value="{{ $registo->obs_colab ?? '' }}"></textarea>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default pull-left"
                                                        data-dismiss="modal">Fechar</button>
                                                    <button type="submit" class="btn btn-success">Salvar</button>
                                                </div>
                                            </form>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
