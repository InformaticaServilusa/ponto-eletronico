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
                 <form method="post" action="{{ route('painel.' . $tipo . '.edit') }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <input type='hidden' name='registo_id' value='{{ $registo->id ?? '' }}'>
                     <input type='hidden' name='registo_tipo' value='{{ $tipo ?? '' }}'>
                     <div class="row">
                         @if ($tipo === 'ponto')
                             <div class="mt-2 mb-2">
                                 <div class="form-check">
                                     <input class="form-check-input" type="checkbox"
                                         {{ $registo->is_folga() ? 'checked' : '' }} name="was_folga"
                                         id="was_folga">
                                     <label class="form-check-label" for="was_folga">
                                         <strong>Era dia de folga?</strong>
                                     </label>
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <strong>Manhã</strong>
                                 <input type="time" name="entrada_manha" class="form-control"
                                     placeholder="Hora de Entrada" value='{{ $registo->entrada_manha ?? '' }}'>
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
                             <div class="mt-3">
                                 <strong>Observações</strong>
                                 <textarea name="obs_colab" class="form-control" placeholder="Escreva aqui as suas observações. Por defeito: 'Correcão'"
                                     rows="3">{{ $registo->obs_colab ?? '' }}</textarea>
                             </div>
                         @elseif($tipo === 'ausencia')
                             <div class="row mt-2">
                                 <fieldset>
                                     <legend class="form-label ml-3">Tipo de ausência</legend>
                                     <select class="form-control" id="tipo_entrada" name="tipo_ausencia_id" required>
                                         <option class="form-control" value="" disabled>Selecione o tipo
                                             de ausência</option>
                                         @foreach ($tipoAusencias as $tipo_ausencia)
                                             <option name="tipo_ausencia_id" value="{{ $tipo_ausencia->id }}"
                                                 {{ isset($registo->tipo_ausencia_id) && $registo->tipo_ausencia_id == $tipo_ausencia->id ? 'selected' : '' }}>
                                                 {{ $tipo_ausencia->descricao }}
                                             </option>
                                         @endforeach
                                     </select>
                                 </fieldset>
                             </div>
                             <div class="row mt-2">
                                 <fieldset>
                                     <legend class="form-label ml-3">Justificação do colaborador</legend>
                                     <div class="col-md-12">
                                         <textarea class="form-control" name="obs_colab" placeholder="Valor por defeito será 'Correcção'" id="obs_colab"
                                             rows="3">{{ $registo->obs_colab ?? '' }}</textarea>
                                         @error('obs_colab')
                                             <span class="text-danger">{{ $message }}</span>
                                         @enderror
                                 </fieldset>
                             </div>
                             <div class="row mt-2">
                                 <fieldset>
                                     <legend class="form-label ml-3">Documento justificativo</legend>
                                     <input class="form-control" type="file" name="anexo">
                                     @error('anexo')
                                         <span class="text-danger">{{ $message }}</span>
                                     @enderror
                                     @if (isset($registo->anexo) && $registo->anexo != null && $registo->anexo != '')
                                         <div class="mt-2">
                                             <a href="{{ Storage::url($registo->anexo) }}" target="_blank">Visualizar
                                                 documento</a>
                                         </div>
                                     @endif
                                 </fieldset>
                             </div>
                             <div class="row mt-2">
                                 <fieldset>
                                     <legend class="form-label ml-3">Horário</legend>
                                     <div class="col-md-6">
                                         <div class="row">
                                             <div class="col">
                                                 <label for="hora_inicio" class="form-label">Hora de Início</label>
                                                 <input type="time" placeholder="Hora de Início"
                                                     class="form-control" id="hora_inicio"
                                                     value='{{ $registo->hora_inicio ?? '' }}' name="hora_inicio">
                                                 @error('hora_inicio')
                                                     <span class="text-danger">{{ $message }}</span>
                                                 @enderror
                                             </div>
                                             <div class="col">
                                                 <label for="hora_fim" class="form-label">Hora de Fim</label>
                                                 <input type="time" placeholder="Hora de Fim" class="form-control"
                                                     id="hora_fim" name ="hora_fim"
                                                     value='{{ $registo->hora_fim ?? '' }}'>
                                                 @error('hora_fim')
                                                     <span class="text-danger">{{ $message }}</span>
                                                 @enderror
                                             </div>
                                         </div>
                                     </div>
                                 </fieldset>
                             </div>
                         @endif
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
