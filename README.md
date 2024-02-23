# personal-financial
Open source web application to track financial and inspired from Bluecoins app (https://www.bluecoinsapp.com/)

## Local development

Requirement:
- Docker
- PHP 8.2 with composer 2.5.8
- NodeJS 18.17.1 (npm 9.6.7)

After clone, follow these step
1. ```composer install```
1. ```npm install```
1. ```cp .env.example .env```
1. ```php artisan key:generate```
1. ```docker compose -f docker-compose.dev.yml up -V -d```
1. ```docker exec -it pf_php bash```
1. ```php artisan migrate --seed```

---

## To run test locally

1. ```docker exec -it pf_php bash```
1. ```php artisan migrate:fresh --env=testing --seed```
1. ```php artisan test```