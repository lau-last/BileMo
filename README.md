# BileMo
Project 7 of the **PHP/Symfony** training program at OpenClassrooms: Create a web service exposing an API

This project has been developed with PHP **8.2** and Symfony **6.3**.
## Installing the Project Locally
To install the project on your machine, follow these steps:
- Set up a PHP & MySQL environment (e.g., using [MAMP](https://www.mamp.info/en/downloads/)).
- Install [Composer](https://getcomposer.org/download/).
### 1) Clone the project and install dependencies:
> git clone https://github.com/lau-last/BileMo

> composer install
### 3) Update the environment variables in the **.env** file
Modify the database connection string:
>DATABASE_URL="mysql://**db_user**:**db_password**@127.0.0.1:3306/**db_name**?serverVersion=5.7&charset=utf8mb4"

Add
>JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem

>JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem

>JWT_PASSPHRASE=your-strong-password
### 4) Database and Sample Data
Create the database, initialize the schema, and load sample data:
>php bin/console doctrine:database:create

>php bin/console doctrine:schema:up --force --complete

>php bin/console doctrine:fixtures:load

### 5) Generate Private & Public Keys
Generate API keys with the passphrase being the value of the "JWT_PASSPHRASE" environment variable in the .env file:
>openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096

>openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

## Everything is Ready!
You can start the server:
>symfony server:start

Test user and admin accounts are as follows:
>user@bilemo.com / password

>admin@bilemo.com / password

You can now, using Postman for example, request a token from the API as a user:
>GET http://localhost:8000/api/login_check

In the request body, include:
>{\
"username": "admin@bilemo.com",\
"password": "password"\
}

Other routes and functionalities are available; refer to the API documentation for more information.
Default API documentation link:
> http://localhost:8000/api/doc