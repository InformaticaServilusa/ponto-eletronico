<div>
    <div class="modal fade" id="tipoAusenciaModal" tabindex="-1" role='dialog' aria-labelledby="tipoAusenciaModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role='document'>
            <div class="modal-content">
                <div class="modal-header justify-content-between">
                    <h1 class="modal-title" id="tipoAusenciaModalLabel">Selecionar Tipo Ausencia</h1>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formAusencia" wire:submit.prevent="submitAusencia">
                    <div class="modal-body">
                        <h4>Seleccione o tipo de ausência a submeter</h4>
                        <div class='row justifify-content-evenly'>
                            @foreach ($tipos_ausencia as $tipo_ausencia)
                                <div class="col">
                                    <button type="submit" wire:click="selectTipoAusencia({{ $tipo_ausencia->id }})"
                                        class="btn btn-outline-primary mr-3">{{ $tipo_ausencia->descricao }}</button>
                                </div>
                            @endforeach
                        </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        Livewire.on('submitFormAusencia', () => {
            document.getElementbyId('formAusencia').submit();
            $('#tipoAusenciaModal').modal('hide');
        });
    </script>
</div>
