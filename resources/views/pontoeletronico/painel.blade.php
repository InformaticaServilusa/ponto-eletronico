<?php
$url_base = getenv('APP_URL');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ getenv('APP_NAME') }} | Livro de Ponto Digital</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    {{-- <link rel="stylesheet" href="{{ $url_base }}/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css"> --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d3cb21102b.js" crossorigin="anonymous"></script>
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/bower_components/Ionicons/css/ionicons.min.css">
    <!-- DataTables -->
    {{-- <link rel="stylesheet" href="{{ $url_base }}/adminlte/bower_components/datatables/dataTables.bootstrap.min.css"> --}}
    <link
        href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.7/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/datatables.min.css"
        rel="stylesheet">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/dist/css/skins/_all-skins.min.css">
    <!-- Date Picker -->
    <link rel="stylesheet"
        href="{{ $url_base }}/adminlte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link href="{{ $url_base }}/adminlte/plugins/sweet-alert/sweet-alert.css" rel="stylesheet">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/bower_components/iCheck/all.css">
    <link href="{{ $url_base }}/adminlte/plugins/fileuploader/font/font-fileuploader.css" rel="stylesheet">
    <link href="{{ $url_base }}/adminlte/plugins/fileuploader/css/jquery.fileuploader.min.css" media="all"
        rel="stylesheet">
    <link href="{{ $url_base }}/adminlte/plugins/fileuploader/css/jquery.fileuploader-theme-thumbnails.css"
        media="all" rel="stylesheet">
    <!-- fullCalendar -->
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/bower_components/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet"
        href="{{ $url_base }}/adminlte/bower_components/fullcalendar/dist/fullcalendar.print.min.css"
        media="print">
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">

    @livewireStyles
</head>

<body class="hold-transition skin-black sidebar-mini">
    @livewireScripts
    <!-- Site wrapper -->
    <div class="wrapper">
        <header class="main-header">
            <!-- Logo -->
            <a href="#" class="logo" style="text-align: left !important;">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><img src="{{ $url_base }}/img/flor-fundotransparente-1-192x185.jpg"></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><img
                        src="{{ $url_base }}/img/Logo_NovaImagem_Servilusa_Fundo-transparente.jpg.png"></span>
            </a>
            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
            </nav>
        </header>


        <!-- =============================================== -->
        <!-- Left side column. contains the sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                {{-- <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ $url_base }}/img/avatar.png" class="img-circle" alt="User Image">
                    </div>
                    <div class="info">
                        <p>{{ $utilizador->nome }}</p>
                        <!-- Status -->
                        <a href="#"><i class="fa fa-circle text-success"></i>
                            {{ $utilizador->getRole() }}</a>
                    </div>
                </div> --}}
                <nav class="mt-2">
                    <ul class="sidebar-menu tree" data-widget="tree">
                        <li>
                            <a href="{{ route('painel.dashboard') }}" style="text-decoration: none;">
                                <i class="nav-icon fa fa-dashboard"></i>
                                <span>Registo Mensal</span>
                            </a>
                        </li>
                        @if ($utilizador->is_coordenador())
                            <li class="treeview nav-link">
                                <a href="#" style="text-decoration: none;">
                                    <i class="fa fa-clock-o"></i>
                                    <span>Coordenação</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="{{ route('painel.coordenacao') }}" style="text-decoration: none;">
                                            <i class="fa fa-circle-o"></i> Registos Gerais
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" style="text-decoration: none;">
                                            <i class="fa fa-circle-o"></i> Registos Detalhados
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif


                        @if ($utilizador->_dep_rh == 1)
                            <li>
                                <a href="{{ route('painel.coordenacao.rh') }}" style="text-decoration: none;">
                                    <i class="nav-icon fa fa-users"></i>
                                    <span>Gestão RH</span>
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('painel.logout') }}" style="text-decoration: none;">
                                <i class="fa fa-sign-out"></i>
                                <span>Sair</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- sidebar menu: : style can be found in sidebar.less -->
            </section>
        </aside>
        <!-- /.sidebar -->
        <!-- =============================================== -->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('conteudo')
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ Date('Y') }} <a
                    href="https://www.servilusa.pt/">{{ getenv('APP_NAME') }}</a>.</strong> Todos os direitos
            reservados.
        </footer>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
        <input type="hidden" id="url_base" value="{{ $url_base }}">
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 2.2.3 -->

    <script src="{{ $url_base }}/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js" type="text/javascript"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ mix('js/app.js') }}"></script>
    <!-- DataTables -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script
        src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.7/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/datatables.min.js">
    </script>

    <!-- datepicker -->
    <script src="{{ $url_base }}/adminlte/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js">
    </script>
    <script
        src="{{ $url_base }}/adminlte/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.pt-BR.min.js">
    </script>
    <!-- Slimscroll -->
    <script src="{{ $url_base }}/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="{{ $url_base }}/adminlte/bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ $url_base }}/adminlte/dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ $url_base }}/adminlte/plugins/sweet-alert/sweet-alert.min.js"></script>

    <script src="{{ $url_base }}/adminlte/plugins/ckeditor/ckeditor.js"></script>

    <!-- iCheck -->
    <script src="{{ $url_base }}/adminlte/bower_components/iCheck/icheck.min.js"></script>

    <!-- ChartJS -->
    <script src="{{ $url_base }}/adminlte/bower_components/chart.js/Chart.js"></script>

    <script src="{{ $url_base }}/adminlte/dist/js/jquery.mask.min.js"></script>
    <script src="{{ $url_base }}/adminlte/dist/js/site.js"></script>

    <script src="{{ $url_base }}/adminlte/plugins/fileuploader/js/jquery.fileuploader.min.js" type="text/javascript">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js">
    </script>

    <script src="{{ $url_base }}/adminlte/plugins/fileuploader/js/custom.js?id=1" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        function setaDadosModal(id, tipo, data_ajuste, hora) {

            var id = id;
            var tipo = tipo;
            var data_ajuste = data_ajuste;
            var hora = hora;

            if (tipo == 'entrada') {
                $('#tipo-entrada-' + id).prop("checked", true);
                $('#tipo-saida-' + id).prop("checked", false);
                $('#tipo-periodo-' + id).prop("checked", false);
            } else if (tipo == 'saida') {
                $('#tipo-saida-' + id).prop("checked", true);
                $('#tipo-entrada-' + id).prop("checked", false);
                $('#tipo-periodo-' + id).prop("checked", false);
            }

            $("#data-" + id).val(data_ajuste);
            $("#hora-" + id).val(hora);

        }
    </script>

    <script>
        $(document).on('click', '.btnExluir', function(e) {

            event.preventDefault();

            var url = $(this).data('url');
            var msg = $(this).data('msg');

            swal({
                    title: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sim",
                    cancelButtonText: "Não",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = url;
                    }
                });

        });
    </script>


    <script>
        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
            radioClass: 'iradio_minimal-red'
        });
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
    </script>
    <script>
        $(function() {
            var mes_atual = $("#mes_atual strong").text();
            var username = $(".info p").text();
            var ano_mes_atual = $("#ano_mes_atual").data("value");
            $('#registosTable').DataTable({
                "responsive": true,
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "pageLength": 31,
                columnDefs: [{
                    targets: [0],
                    className: 'dt-left'
                }, ],
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'excel',
                                className: 'btn btn-primary',
                                title: `Registos de ${username} referentes a ${mes_atual}`,
                                filename: `LivroPonto_${username}_${ano_mes_atual}`,
                                init: function(api, node, config) {
                                    $(node).removeClass('dt-button buttons-excel buttons-html5')
                                },
                                exportOptions: {
                                    columns: ':not(:last-child)'
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn btn-primary',
                                title: `Registos de ${username} referentes a ${mes_atual}`,
                                filename: `LivroPonto_${username}_${ano_mes_atual}`,
                                init: function(api, node, config) {
                                    $(node).removeClass('dt-button buttons-excel buttons-html5')
                                },
                                exportOptions: {
                                    columns: ':not(:last-child)'
                                }
                            },
                            {
                                extend: 'print',
                                className: 'btn btn-primary',
                                title: `Registos de ${username} referentes a ${mes_atual}`,
                                filename: `LivroPonto_${username}_${ano_mes_atual}`,
                                init: function(api, node, config) {
                                    $(node).removeClass('dt-button buttons-excel buttons-html5')
                                },
                                exportOptions: {
                                    columns: ':not(:last-child)'
                                }
                            }
                        ]
                    }
                },
                "oLanguage": {
                    "sLengthMenu": "Mostrar _MENU_ registos por página",
                    "sZeroRecords": "Nenhum registo encontrado",
                    "sInfo": "_START_ / _END_ de _TOTAL_ registo(s)",
                    "sInfoEmpty": "Sem registos",
                    "sInfoFiltered": "(filtrado de _MAX_ registos)",
                    "sSearch": "Pesquisar: ",
                    "oPaginate": {
                        "sFirst": "Início",
                        "sPrevious": "Anterior",
                        "sNext": "Próximo",
                        "sLast": "Último"
                    }
                }
            });


            $('#coordenadorTable').DataTable({
                "responsive": true,
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": true,
                "pageLength": 31,
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'excel',
                                className: 'btn btn-primary',
                                title: `Registos de Coordenandos referentes a ${mes_atual}`,
                                filename: `LivroPontoCoord_${username}_${ano_mes_atual}`,
                                init: function(api, node, config) {
                                    $(node).removeClass('dt-button buttons-excel buttons-html5')
                                },
                                exportOptions: {
                                    columns: ':not(:last-child)'
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn btn-primary',
                                title: `Registos de Coordenação ${username} referentes a ${mes_atual}`,
                                filename: `LivroPontoCoord_${username}_${ano_mes_atual}`,
                                init: function(api, node, config) {
                                    $(node).removeClass('dt-button buttons-excel buttons-html5')
                                },
                                exportOptions: {
                                    columns: ':not(:last-child)'
                                }
                            },
                            {
                                extend: 'print',
                                className: 'btn btn-primary',
                                title: `Registos de Coordenação ${username} referentes a ${mes_atual}`,
                                filename: `LivroPontoCoord_${username}_${ano_mes_atual}`,
                                init: function(api, node, config) {
                                    $(node).removeClass('dt-button buttons-excel buttons-html5')
                                },
                                exportOptions: {
                                    columns: ':not(:last-child)'
                                }
                            }
                        ]
                    }
                },
                "oLanguage": {
                    "sLengthMenu": "Mostrar _MENU_ registos por página",
                    "sZeroRecords": "Nenhum registo encontrado",
                    "sInfo": "_START_ / _END_ de _TOTAL_ registo(s)",
                    "sInfoEmpty": "Sem registos",
                    "sInfoFiltered": "(filtrado de _MAX_ registos)",
                    "sSearch": "Pesquisar: ",
                    "oPaginate": {
                        "sFirst": "Início",
                        "sPrevious": "Anterior",
                        "sNext": "Próximo",
                        "sLast": "Último"
                    }
                }
            });
        });

        $('#rhTable').DataTable({
            "responsive": true,
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": false,
            "info": false,
            "autoWidth": false,
            "pageLength": 31,
            "scrollX": true,
            layout: {
                topStart: {
                    buttons: [{
                            extend: 'excel',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            className: 'btn btn-primary',
                            title: `Registos de Coordenandos referentes a {{ $ano_mes }}`,
                            filename: `LivroPontoCoord_{{ $ano_mes }}`,
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button buttons-excel buttons-html5')
                            },
                        },
                        {
                            extend: 'pdf',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            className: 'btn btn-primary',
                            title: `Registos de Coordenandos referentes a {{ $ano_mes }}`,
                            filename: `LivroPontoCoord_{{ $ano_mes }}`,
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button buttons-excel buttons-html5')
                            },
                        },
                        {
                            extend: 'print',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            className: 'btn btn-primary',
                            title: `Registos de Coordenandos referentes a {{ $ano_mes }}`,
                            filename: `LivroPontoCoord_{{ $ano_mes }}`,
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button buttons-excel buttons-html5')
                            },
                        }
                    ]
                }
            },
        });
    </script>
    <script>
        $(function() {

            // Date picker
            // var datepicker = $.fn.datepicker.noConflict();
            // $.fn.bootstrapDP = datepicker;

            // $(".datepicker").bootstrapDP({
            //     autoclose: true,
            //     format: "yyyy-mm-dd",
            //     language: "pt-BR",
            //     showOtherMonths: true,
            //     selectOtherMonths: true,
            //     todayHighlight: true,
            //     nextText: "Próximo",
            //     prevText: "Anterior",
            //     orientation: "bottom"
            // });
            // $('.daterange').daterangepicker({
            //     opens: 'left',
            //     locale: {
            //         format: 'YYYY-MM-DD',
            //         applyLabel: 'Aplicar',
            //         cancelLabel: 'Cancelar',
            //         fromLabel: 'De',
            //         toLabel: 'Até',
            //         "separator": " até ",
            //         daysOfWeek: [
            //             "Dom",
            //             "Seg",
            //             "Ter",
            //             "Qua",
            //             "Qui",
            //             "Sex",
            //             "Sáb"
            //         ],
            //         "monthNames": [
            //             "Janeiro",
            //             "Fevereiro",
            //             "Março",
            //             "Abri",
            //             "Maio",
            //             "Junho",
            //             "Juho",
            //             "Agosto",
            //             "Setembro",
            //             "Outubro",
            //             "Novembro",
            //             "Dezembro"
            //         ],
            //     }
            // }, function(start, end, label) {
            //     console.log("Nova selecção de: " + start.format('YYYY-MM-DD') + ' a ' + end
            //         .format('YYYY-MM-DD'));
            // });

            //   $("#datepicker").bootstrapDP({
            //       autoclose: true,
            //       format: "dd/mm/yyyy",
            //       language: "pt-BR",
            //       showOtherMonths: true,
            //       selectOtherMonths: true,
            //       todayHighlight: true,
            //       nextText: "Próximo",
            //       prevText: "Anterior"
            //   });

        });
        $(document).on('click', '.btnExluir', function(e) {
            event.preventDefault();
            var url = $(this).data('url');
            var msg = $(this).data('msg');
            swal({
                    title: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sim",
                    cancelButtonText: "Não",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = url;
                    }
                });
        });
    </script>

    @if (Session::has('status.msg'))
        @php
            $error_msg = Session::get('status.msg');
            Session::forget('status.msg');
        @endphp
        @if (!empty($error_msg) && $error_msg != '')
            <script>
                swal("{!! $error_msg !!}");
            </script>
        @endif
    @endif
    @if (isset($error_redirect) && !empty($error_redirect))
        <script>
            window.location.href = "{{ $error_redirect }}";
        </script>
    @endif
</body>

</html>
