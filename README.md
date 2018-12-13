
# Authentication Wizard ( Laravel 5.7 )

A basic laravel package implementing authentication using email and password

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

What things you need to install the software and how to install them

```
PHP 7.2
Laravel 5.7 
```

### Installing

```
issue the following command in your laravel root directory :

composer require bahraminekoo/larauth

```

In laravel <=5.4  :

add the line below to the **providers** array of the config/app.php configuration file :

Bahraminekoo\Larauth\LarauthServiceProvider::class

In laravel >=5.5 this service provider will be automatically added to the providers array .

And then

```
run the following commands also in the laravel root directory respectively:

php artisan vendor:publish --tag=migrations

php artisan vendor:publish --tag=views

php artisan migrate

```

and then 

```
set up your laravel mail server configs inside config/mail.php
```

Now we have three REST API that we can use for authentication purposes :

1 - 

    * [POST] /auth/register
    
    headers :
    
      Accept : application/json
      Content-Type : application/json
      
    Request :
       
      {
      	"email": "email@gmail.com",
      	"password": "password"
      }

    Response :
    
    {
        "status": true,
        "message": [
            "register successful, but can not send verification email, you should set up mail configuration in your laravel application"
        ],
        "data": {
            "kind": "user",
            "id": 7,
            "email": "email@gmail.com",
            "isVerified": 0
        }
    }
    
2 - 

     this link should be inside the email inbox after registration
     
    *   [GET] /auth/verify-email/email@gmail.com/hash-string-value 
    
    headers : 
    
        Accept : application/json
        Content-Type : application/json
    
    Request :
    
        none
        
    Response :
    
        {
            "status": true,
            "message": [
                "activation successful, now you can log into your account"
            ],
            "data": {
                "kind": "user",
                "id": 7,
                "email": "email@gmail.com",
                "isVerified": 1
            }
        } 
        
3 - 

    * [POST] /auth/login
    
    headers : 
     
       Accept : application/json
       Content-Type: application/json
       
    Request :
    
        {
        	"email": "email@gmail.com",
        	"password": "password"
        } 
        
    Response : 
    
        {
            "status": true,
            "message": [
                "login successful"
            ],
            "data": {
                "kind": "user",
                "id": 7,
                "email": "email@gmail.com",
                "isVerified": 1
            }
        }                     