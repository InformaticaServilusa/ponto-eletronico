<div class="modal fade" id="display-registo-modal-{{ $tipo }}-{{ $registo->id ?? '' }}" tabindex="-1"
    role="dialog" aria-hidden="true" aria-labelledby="display-registoModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="display-registoModal">Detalhes registo de {{ $registo->data }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if ($tipo === 'ponto')
                        <div class="mt-2 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    {{ $registo->is_folga() ? 'checked' : '' }} name="was_folga" id="was_folga"
                                    disabled>
                                <label class="form-check-label" for="was_folga">
                                    <strong>Era dia de folga?</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <strong>Manhã</strong>
                            <input type="time" name="entrada_manha" class="form-control"
                                placeholder="Hora de Entrada" value='{{ $registo->entrada_manha ?? '' }}' disabled>
                            <input type="time" name="saida_manha" class="form-control " placeholder="Hora de Saída"
                                value="{{ $registo->saida_manha ?? '' }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <strong>Tarde</strong>
                            <input type="time" name="entrada_tarde" class="form-control"
                                placeholder="Hora de Entrada" value="{{ $registo->entrada_tarde ?? '' }}" disabled>
                            <input type="time" name="saida_tarde" class="form-control" placeholder="Hora de Saída"
                                value="{{ $registo->saida_tarde ?? '' }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <strong>Noite</strong>
                            <input type="time" name="entrada_noite" class="form-control"
                                placeholder="Hora de Entrada" value="{{ $registo->entrada_noite ?? '' }}" disabled>
                            <input type="time" name="saida_noite" class="form-control" placeholder="Hora de Saída"
                                value="{{ $registo->saida_noite ?? '' }}" disabled>
                        </div>
                        <div class="mt-3">
                            <strong>Observações do Colaborador</strong>
                            <textarea name="obs_colab" class="form-control" placeholder="Sem observações do colaborador" rows="3" disabled>{{ $registo->obs_colab ?? '' }}</textarea>
                        </div>
                        @if (isset($registo->obs_coord) && $registo->obs_coord != null && $registo->obs_coord != '')
                            <div class="mt-3">
                                <strong>Observações do Coordenador</strong>
                                <textarea name="obs_coord" class="form-control" placeholder="Sem observações do coordenador" rows="3" disabled>{{ $registo->obs_coord ?? '' }}</textarea>
                            </div>
                        @endif
                    @elseif($tipo === 'ausencia')
                        <div class="row mt-2">
                            <fieldset>
                                <strong class="form-label ml-3">Tipo de ausência</strong>
                                <select class="form-control" id="tipo_entrada" name="tipo_ausencia_id" disabled>
                                    <option class="form-control" value="" disabled>Selecione o tipo
                                        de ausência</option>
                                    <option name="tipo_ausencia_id" value="{{ $registo->tipo_ausencia_id }}" selected>
                                        {{ $registo->get_tipo_ausencia() }}
                                    </option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="row mt-2">
                            <fieldset>
                                <strong class="form-label ml-3">Justificação do colaborador</strong>
                                <div class="col-md-12">
                                    <textarea class="form-control" name="obs_colab" placeholder="Sem observações do colaborador" id="obs_colab"
                                        rows="3" disabled>{{ $registo->obs_colab ?? '' }}</textarea>
                                </div>
                            </fieldset>
                            @if (isset($registo->obs_coord) && $registo->obs_coord != null && $registo->obs_coord != '')
                                <div class="mt-3">
                                    <strong>Observações do Coordenador</strong>
                                    <textarea name="obs_coord" class="form-control" placeholder="Sem observações do coordenador" rows="3" disabled>{{ $registo->obs_coord ?? '' }}</textarea>
                                </div>
                            @endif
                        </div>
                        <div class="row mt-2">
                            <fieldset>
                                <strong class="form-label ml-3">Documento justificativo</strong>
                                @if (isset($registo->anexo) && $registo->anexo != null && $registo->anexo != '')
                                    <div class="mt-2">
                                        <a href="{{ Storage::url($registo->anexo) }}" target="_blank">Visualizar
                                            documento</a>
                                    </div>
                                @else
                                    <span class="text-danger">Sem documento justificativo</span>
                                @endif
                            </fieldset>
                        </div>
                        <div class="row mt-2">
                            <fieldset>
                                <strong class="form-label ml-3">Horário</strong>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col">
                                            <label for="hora_inicio" class="form-label">Hora de Início</label>
                                            <input type="time" placeholder="Hora de Início" class="form-control"
                                                id="hora_inicio" value='{{ $registo->hora_inicio ?? '' }}'
                                                name="hora_inicio" disabled>
                                        </div>
                                        <div class="col">
                                            <label for="hora_fim" class="form-label">Hora de Fim</label>
                                            <input type="time" placeholder="Hora de Fim" class="form-control"
                                                id="hora_fim" name ="hora_fim"
                                                value='{{ $registo->hora_fim ?? '' }}' disabled>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    @endif
                    <div class="col">
                        <div class="mt-3">
                            <strong>Validada por Coordenador</strong>
                            <input type="checkbox" id="registo_validado" class="form-check-input" {{ $registo->status == 1 ? 'checked' : '' }}
                                disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
