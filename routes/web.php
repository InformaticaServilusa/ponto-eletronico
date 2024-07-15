<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// //VIEW APENAS PARA TESTE!
// Route::get('teste', function () {
//     return view('nova_view');
// });

//ROUTES QUE JA ESTOU A UTILIZAR
Route::get('/', 'PontoEletronico\IndexController@index')->name('login');
Route::post('login', 'PontoEletronico\LoginPainelController@login');
Route::get('painel/dashboard/{ano_mes?}', 'PontoEletronico\DashboardPainelController@index')->name('painel.dashboard');
Route::get('painel/sair', 'PontoEletronico\LoginPainelController@sair')->name('painel.logout');
Route::post('painel/ponto/submeterTrabalho', 'PontoEletronico\PontoPainelController@submeterTrabalho')->name('painel.ponto.submit');
Route::post('painel/ponto/submeterAusencia', 'PontoEletronico\PontoPainelController@submeterAusencia')->name('painel.ausencia.submit');
Route::post('painel/ponto/submeterJustificacao', 'PontoEletronico\PontoPainelController@submeterJustificacao');
Route::post('painel/ponto/editar', 'PontoEletronico\PontoPainelController@editar')->name('painel.ponto.edit');
Route::post('painel/ausencia/editar', 'PontoEletronico\AusenciaPainelController@editar')->name('painel.ausencia.edit');
Route::get('painel/ponto/eliminar/{tipo}/{registo_id}', 'PontoEletronico\PontoPainelController@eliminar')->name('painel.ponto.delete');
Route::match(['get', 'post'], 'painel/coordenacao/rh/', 'PontoEletronico\AcompanhamentoController@dashboardRH')->name('painel.coordenacao.rh');
Route::match(['get', 'post'], 'painel/coordenacao/rh/{ano_mes?}', 'PontoEletronico\AcompanhamentoController@dashboardRH')->name('painel.coordenacao.rh.with_ano_mes');
Route::post('painel/coordenacao/changeValidation/', 'PontoEletronico\AcompanhamentoController@changeValidation')->name('painel.coordenacao.changeValidation');
Route::get('painel/coordenacao/{ano_mes}/{colaborador_id}', 'PontoEletronico\AcompanhamentoController@dashboardCoordenacao')->name('painel.coordenacao.utilizador');
Route::match(['get', 'post'], 'painel/coordenacao/{ano_mes?}', 'PontoEletronico\AcompanhamentoController@index')->name('painel.coordenacao');
//ROUTES ANTIGAS
// Route::group(['namespace' => 'PontoEletronico'], function()
// {
//   Route::post('/registrar', 'PontoController@registrar_validando');
//   //Route::post('/login', 'LoginController@login');
//   Route::get('/registrar', 'PontoController@registrar');
//   Route::get('/dashboard', 'DashboardController@index');

//   Route::get('/sair', 'LoginController@sair');


// });

// Route::group(['prefix' => 'painel', 'namespace' => 'PontoEletronico'], function()
// {
//   Route::post('/login', 'LoginPainelController@login');
// //   Route::get('/dashboard', 'DashboardPainelController@index');
//   Route::get('/', 'IndexPainelController@index');


//   Route::get('/usuarios', 'UsuarioController@index');
//   Route::get('/usuario/novo', 'UsuarioController@novo');
//   Route::get('/usuario/editar/{id}', 'UsuarioController@editar');
//   Route::get('/usuario/excluir/{id}', 'UsuarioController@excluir');
//   Route::get('/usuario/desabilitar/{id}', 'UsuarioController@desabilitar');
//   Route::get('/usuario/habilitar/{id}', 'UsuarioController@habilitar');
//   Route::post('/usuario/salvar', 'UsuarioController@salvar');

//   Route::post('/acompanhamento', 'AcompanhamentoController@index');
//   Route::post('/ponto/salvar', 'PontoPainelController@ajuste');
   //Route::post('/ponto/periodo/salvar', 'PontoAjusteController@salvar');
//   Route::get('/ajuste', 'PontoAjusteController@index');
//   Route::get('/ajuste/excluir/{id}', 'PontoAjusteController@delete');
//   Route::get('/certificacao', 'PontoAjusteController@index');
//   Route::post('/certificacao/salvar', 'PontoAjusteController@certificar');
//   Route::get('/excel-acompanhamento/{usuario}/{inicio}/{fim}', 'AcompanhamentoController@index_download');


// });
