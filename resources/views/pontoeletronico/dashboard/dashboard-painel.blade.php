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

    $mes[1] = 'Janeiro';
    $mes[2] = 'Fevereiro';
    $mes[3] = 'Março';
    $mes[4] = 'Abril';
    $mes[5] = 'Maio';
    $mes[6] = 'Junho';
    $mes[7] = 'Julho';
    $mes[8] = 'Agosto';
    $mes[9] = 'Setembro';
    $mes[10] = 'Outubro';
    $mes[11] = 'Novembro';
    $mes[12] = 'Dezembro';

    $dia_extenso = $dia_da_semana[Date('w')];
    $mes_extenso = $mes[Date('n')];

    $hora = Date('H:i');
    ?>
    <section class='content'>
        <div class="row">
            <div class="col-md-12">
                <!-- Info Colaborador -->
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
            <div class='col-md-6'>
                <!-- Caixa com informação a que mês corresponde a visualição
                                                                    TODO: Adicionar um botão para mudar o mês (Nao permitir mês para a frente (para já))
                                                                    TODO: Permissão de introdução de vários dias consecutivos.
                                                                -->
                <div class="box box-primary">
                    <div class="box-header with-border text-center">
                        <h3 class="box-title "><strong>{{ $mes_extenso }}</strong></h3>
                    </div>
                    <div class="box-body box-profile text-center">
                        <i class="far fa-clock fa-5x"></i>
                        <h3 class="profile-username text-center"><?= $dia_extenso ?>, <?= Date('d') ?> de
                            <?= $mes_extenso ?> de <?= Date('Y') ?></h3>
                        <p class="text-muted text-center" style='font-size: 50px;'><?= Date('H:i') ?></p>
                        <div class="row">

                            <!-- Este vai ser o botão para registar uma entrada no livro de ponto -->
                            <div class='col-md-6 col-xs-6'>
                                <a href='#modal-registo-ponto' data-toggle="modal" class="btn btn-md btn-success"><i
                                        class="fa fa-plus"></i> Registar no livro de ponto</a>
                                {{-- <form method="post" action="" name="form-entrada">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="area" value="entrada">
                                    <input type="hidden" name="hora" value="<?= $hora ?>">
                                    <input type='submit' value='Registar no livro de ponto' class="btn btn-success"
                                        style="width: 100%;">
                                </form> --}}
                            </div>

                            <!-- Este vai ser o botão para registar uma justificação -->
                            <div class='col-md-6 col-xs-6'>
                                <a href='#modal-registo-ponto' data-toggle="modal" class="btn btn-md btn-success"><i
                                        class="fa fa-plus"></i> Submeter justificação de falta</a>


                                {{-- <form method="post" action="" name="form-saida">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="area" value="saida">
                                    <input type="hidden" name="hora" value="<?= $hora ?>">
                                    <input type='submit' value='Submeter justificação de falta' class="btn btn-danger"
                                        style="width: 100%;">
                                </form> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Aqui vao aparecer os registos do mês currente
                                                    TODO: ITERAR NOS MESES!-->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Livre de Ponto de < <strong>{{ $mes_extenso }} > </strong></h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped text-center">
                            <thead>
                                <tr>
                                    <th colspan=2>Manhã</th>
                                    <th colspan=2>Tarde</th>
                                </tr>
                                <tr>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                    <th>Validada</th>
                                    <th>Justificação</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($registos as $registo)
                                <tr>
                                    <td>{{ $registo->entrada_manha }}</td>
                                    <td>{{ $registo->saida_manha }}</td>
                                    <td>{{ $registo->entrada_tarde }}</td>
                                    <td>{{ $registo->saida_tarde }}</td>
                                    <td>{{ $registo->status }}</td>
                                    <td>{{ $registo->justificacao }}</td>
                                </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- Modal para incluir um novo registo de ponto -->
    <div id="modal-registo-ponto" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h2 class="modal-title">Registo de ponto</h2>
                </div>
                <form method="post" action="" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <!-- tipo de registo -->
                        <input type="hidden" name="tipo" value="ponto">
                        <div class="form-group">
                            <fieldset>
                                <legend>Data</legend>
                                <div class="col-md-4">
                                    <input type="text" name="data" class="form-control datepicker" placeholder="Data"
                                        value="" required>
                                </div>
                            </fieldset>
                        </div>
                        <div class="form-group">
                            <fieldset>
                                <legend>Manhã</legend>
                                <div class="col-md-4">
                                    <input type="text" name="hora_entrada" class="form-control time"
                                        placeholder="Hora de Entrada" value="">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="hora_saida" class="form-control time"
                                        placeholder="Hora de Saída" value="">
                                </div>
                        </div>
                        </fieldset>
                        <div class="form-group">
                            <fieldset>
                                <legend>Tarde</legend>
                                <div class="col-md-4">
                                    <input type="text" name="hora_entrada" class="form-control time"
                                        placeholder="Hora de Entrada" value="">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="hora_saida" class="form-control time"
                                        placeholder="Hora de Saída" value="">
                                </div>
                            </fieldset>
                        </div>
                        <div class="form-group">
                            <fieldset>
                                <legend>Observações</legend>
                                    <textarea id="colab_obs" name="colab_obs" class="form-control" placeholder="Escreva aqui as suas observações"
                                        rows="3"></textarea>
                            </fieldset>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>

                </form>
                <br>
            </div>
        </div>
    </div>
@endsection
