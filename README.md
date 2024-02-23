# Sistema de Livro de Ponto em Laravel
Sistema de ponto eletrônico simplificado, desenvolvido em LARAVEL.


## 🔧 Funcionalidades

* Inclusão, exclusão e modificação de Funcionário
* Ponto de entrada
* Ponto de saída
* Justificação de falta
* Ajustes de ponto
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
php artisan migrate
```
* Caso nao tenha nenhuma servidor configurado para que possa aceder, aconselhasse o uso de de Laradock.
https://dev.to/moghwan/dockerize-your-laravel-project-with-laradock-2io1


## 🙏 Agradecimentos

https://github.com/ilab4 Pela disponibilização do esqueleto do projeto openSource
