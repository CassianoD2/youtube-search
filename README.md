# Teste para vaga de Backend

####Tecnologias utilizadas:

 - PHP 7.3 - Laravel 7.x
 - MySQL/MariaDB
 
 
##### Como rodar o código:
 1 - Clonar o repositório.
 
 2 - Renomear o arquivo .env.example para .env
    
    cp .env.example .env
 3 - Executar os comando:
 
    composer install
    npm install && npm run production
    
 4 - Configurar os acessos ao MySQL e adicionar a chave para o Youtube no *.env*
 
 5 - Executar o comando:
 
    php artisan migrate:fresh
    
 6 - Dependendo de como for feito o deploy pode ser acessado "CAMINHODOTESTE/public/"
 
 Para visualizar online o teste acesse: https://cassianomesquita.dev/youtubesearch
