# Sistema de Livro de Ponto em Laravel
Sistema de ponto eletrônico simplificado, desenvolvido em LARAVEL.


## 🔧 Funcionalidades
* Login através de LDAP
* Introdução de Ponto (presença) diário, ou numa range de datas
* Introdução de Ausencias com justificaçõ
* Aprovação de ponto
* Relatório de ponto mensal por Funcionário com percentagem de presença, dias de faltas e justificações.


## 📇Tarefas Possiveis

* Funcionário: Registo do ponto (Entrada e Saída), correções de registos e justificações.
* Coordenador: Aprovação/Rejeição de ponto, Relatórios de presença e horas


## 🚀 Instalação
    
Desenvolvido em Laravel Laravel 7.0
Configurar .env as conexões com o mySQL.
No seu cmd ou terminal, na pasta do projeto, inicie a instalação do sistema.
```
composer install
```
```
php artisan key:generate
```
```
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';
php artisan migrate --seed
```

## 🙏 Agradecimentos

https://github.com/ilab4 Pela disponibilização do esqueleto do projeto openSource
