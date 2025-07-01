Para rodar o projeto é simples:

```
git clone https://github.com/0neda/Borae.git
cd Borae
composer install
npm run install
```

Efetuar as configurações do MySQL no .env conforme seu ambiente e executar os comandos:

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
