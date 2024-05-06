<div class="modal fade" id="create-ponto-atipico" tabindex="-1" aria-labelledby="create-ponto-atipico" aria-hidden="true"
    wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit.prevent="save">
                {{-- TODO: PASSAR O TIPO DE INPUT PARA DENTRO DO CONTROLADOR --}}
                <div class="modal-header justify-content-between">
                    {{ csrf_field() }}
                    <h5 class="modal-title fs-5" id="create-ponto-atipico">Selecionar data e horário</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div>
                        <fieldset>
                            <legend class="form-label ml-3">Data</legend>
                            <input type="text" class="form-control" id="data_submissao"
                                wire:model.defer="data_submissao"
                                onchange="this.dispatchEvent(new InputEvent('input'))">
                            <input type="hidden" class="form-control" id="tipo_entrada"
                                wire:model.defer="tipo_entrada">
                        </fieldset>
                        @error('data_submissao')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    @if ($tipo_entrada == 'ausencia')
                        <div>
                            <fieldset>
                                <legend class="form-label ml-3">Tipo de entrada</legend>
                                <select class="form-control" id="tipo_entrada" wire:model.defer="tipo_ausencia">
                                    <option value='' disabled selected>Selecione o tipo de ausência</option>
                                    @foreach ($tipos_ausencia as $tipo_ausencia)
                                        <option value="{{ $tipo_ausencia->id }}">{{ $tipo_ausencia->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="row mt-4">
                            <fieldset>
                                <legend class="form-label ml-3">Horário</legend>
                                <div class="col-md-6">
                                    <label for="hora_inicio" class="form-label">Hora de Início</label>
                                    <input type="time" class="form-control" id="hora_inicio"
                                        wire:model.defer="hora_inicio">
                                    @error('hora_inicio')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="hora_fim" class="form-label">Hora de Fim</label>
                                    <input type="time" class="form-control" id="hora_fim"
                                        wire:model.defer="hora_fim">
                                    @error('hora_fim')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </fieldset>
                        </div>
                    @else
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <fieldset>
                                    <legend class="form-label ml-3">Manhã</legend>
                                    <label for="entrada_manha" class="form-label">Entrada</label>
                                    <input type="time" class="form-control" id="entrada_manha"
                                        wire:model.defer="entrada_manha">
                                    @error('entrada_manha')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="saida_manha" class="form-label">Saída</label>
                                    <input type="time" class="form-control" id="saida_manha"
                                        wire:model.defer="saida_manha">
                                    @error('saida_manha')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </fieldset>
                            </div>
                            <div class="col-md-4">
                                <fieldset>
                                    <legend class="form-label ml-3">Tarde</legend>
                                    <label for="entrada_tarde" class="form-label">Entrada</label>
                                    <input type="time" class="form-control" id="entrada_tarde"
                                        wire:model.defer="entrada_tarde">
                                    @error('entrada_tarde')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="saida_tarde" class="form-label">Saída</label>
                                    <input type="time" class="form-control" id="saida_tarde"
                                        wire:model.defer="saida_tarde">
                                    @error('saida_tarde')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </fieldset>
                            </div>
                            <div class="col-md-4">
                                <fieldset>
                                    <legend class="form-label ml-3">Noite</legend>
                                    <label for="entrada_noite" class="form-label">Entrada</label>
                                    <input type="time" class="form-control" id="entrada_noite"
                                        wire:model.defer="entrada_noite">
                                    @error('entrada_noite')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="saida_noite" class="form-label">Saída</label>
                                    <input type="time" class="form-control" id="saida_noite"
                                        wire:model.defer="saida_noite">
                                    @error('saida_noite')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </fieldset>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Submeter</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:load', function() {
            $('#data_submissao').datepicker({
                format: 'yyyy-mm-dd',
                language: 'pt-BR',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom',
                multidate: true,
                multidateSeparator: ' até ',
                daysOfWeekHighlighted: '0,6',
            });
        })
    </script>
</div>
