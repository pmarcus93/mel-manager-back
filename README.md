# MEL - Gerenciador de Eventos

## Sobre 
O MEL é um gerenciador de eventos com funcionalidades básicas para auxílio na tomada de decisões.
Este repositório se refere ao back end do projeto, escrito em PHP utilizando o framework Laravel.
O front end, desenvolvido em React pode ser encontrado aqui.

## Requisitos
- Servidor PHP >= 7.2.0
- Banco de dados baseado em SQL
- Composer 

## Instalação
- Faça o clone do projeto.
- Acesse um terminal, navegue até a raíz do projeto e execute ```composer install```.
- Crie um banco de dados vazio para armazenar os dados da aplicação.
- Na raíz do projeto, gere uma chave de encriptação da aplicação usando o comando ```php artisan key:generate```.
- Faça uma cópia do arquivo ```.env.example``` e renomeie para ```.env```.
- No arquivo ```.env```, entre com as credenciais do banco de dados recém criado.
- Na raíz do projeto, execute ```php artisan migrate```. Este comando cria todas as
tabelas e relações necessárias para a execução da aplicação.
- Para executá-lo localmente, execute ```php artisan serve```.

