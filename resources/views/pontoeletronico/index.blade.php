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
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d3cb21102b.js" crossorigin="anonymous"></script>
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ $url_base }}/adminlte/bower_components/iCheck/square/blue.css">

    <link href="{{ $url_base }}/adminlte/plugins/sweet-alert/sweet-alert.css" rel="stylesheet">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><img
                    src="{{ getenv('APP_URL') }}/img/Logo_NovaImagem_Servilusa_Fundo-transparente.jpg.png"
                    width="350px;"></a>
            <h2><?= Date('d/m/Y') ?> <?= Date('H:i') ?></h2>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Identifique-se e controle o seu Livro de Ponto</p>
            <form action="{{ getenv('APP_URL') }}/login" method="post">
                {{ csrf_field() }}
                <div class="form-group has-feedback">
                    <input type="text" name="accountname" class="form-control accountname"
                        placeholder="Nome utilizador" required>
                    <span class="fa fa-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="password" class="form-control" placeholder="Senha" required>
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-block green">Entrar</button>
                    </div>
                </div>
            </form>
        </div>
        <input type="hidden" id="url_base" value="{{ $url_base }}">
    </div>

    <!-- jQuery 2.2.3 -->
    <script src="{{ $url_base }}/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ $url_base }}/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="{{ $url_base }}/adminlte/bower_components/iCheck/icheck.min.js"></script>

    <script src="{{ $url_base }}/adminlte/plugins/sweet-alert/sweet-alert.min.js"></script>

    <!-- InputMask -->
    <script src="{{ $url_base }}/adminlte/bower_components/inputmask/jquery.mask.min.js"></script>
    <?php
    if (Session::has('status.msg')) {
        $error_msg = Session::get('status.msg');
        Session::forget('status.msg');

        if (isset($error_msg) and $error_msg != ''):
            echo "<script>swal(\"$error_msg\");</script>";
        endif;
    }
    ?>

    <?php
    if (isset($error_redirect) and $error_redirect != ''):
        header("location: $error_redirect");
    endif;
    ?>
</body>

</html>
