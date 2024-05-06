 <!-- //TODO: Modal para editar um registo de ponto. Deixou de Funcionar -->
 <form method="post" action="{{ route('painel.' . $tipo . '.edit') }}" enctype="multipart/form-data">
     <div id="modal-edicao-{{ $tipo }}-{{ $registo->id ?? '' }}" class="modal fade" tabindex="-1" role="dialog"
         aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered modal-md">
             <div class="modal-content">
                 <div class="modal-header text-center justify-content-between">
                     <h2 class="modal-title">Editar registo de {{ $tipo }}</h2>
                     <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true" class="float-end">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     {{ csrf_field() }}
                     <input type='hidden' name='registo_id' value='{{ $registo->id ?? '' }}'>
                     <input type='hidden' name='registo_tipo' value='{{ $tipo ?? '' }}'>
                     <div class="row">
                         @if ($tipo === 'ponto')
                             <div class="col-md-4">
                                 <strong>Manhã</strong>
                                 <input type="time" name="entrada_manha" class="form-control"
                                     placeholder="Hora de Entrada"
                                     value='{{ old('entrada_manha', $registo->entrada_manha ?? '') }}'>
                                 <input type="time" name="saida_manha" class="form-control "
                                     placeholder="Hora de Saída" value="{{ $registo->saida_manha ?? '' }}">
                             </div>
                             <div class="col-md-4">
                                 <strong>Tarde</strong>
                                 <input type="time" name="entrada_tarde" class="form-control"
                                     placeholder="Hora de Entrada" value="{{ $registo->entrada_tarde ?? '' }}">
                                 <input type="time" name="saida_tarde" class="form-control"
                                     placeholder="Hora de Saída" value="{{ $registo->saida_tarde ?? '' }}">
                             </div>
                             <div class="col-md-4">
                                 <strong>Noite</strong>
                                 <input type="time" name="entrada_noite" class="form-control"
                                     placeholder="Hora de Entrada" value="{{ $registo->entrada_noite ?? '' }}">
                                 <input type="time" name="saida_noite" class="form-control"
                                     placeholder="Hora de Saída" value="{{ $registo->saida_noite ?? '' }}">
                             </div>
                         @elseif($tipo === 'ausencia')
                             <div class="col-md-4">
                                 <strong>Hora de Inicio</strong>
                                 <input type="time" name="hora_inicio" class="form-control"
                                     placeholder="Hora de Início"
                                     value='{{ old('hora_inicio', $registo->hora_inicio ?? '') }}'>
                                 <div class="mt-3">
                                     <strong>Hora de Fim</strong>
                                     <input type="time" name="hora_fim" class="form-control "
                                         placeholder="Hora de Fim"
                                         value='{{ old('hora_fim', $registo->hora_fim ?? '') }}'>
                                 </div>
                             </div>
                         @endif
                     </div>
                     <div class="mt-3">
                         <strong>Observações</strong>
                         <textarea id="obs_colab" name="colab_obs" class="form-control"
                             placeholder="Escreva aqui as suas observações. Por defeito: 'Correcão'" rows="3">{{ $registo->obs_colab ?? '' }}</textarea>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-default pull-left"
                             data-bs-dismiss="modal">Fechar</button>
                         <button type="submit" class="btn btn-success">Salvar</button>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </form>
