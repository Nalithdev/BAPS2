# BAPS2


README for Fid'Antony :

Initialize the project :

```
composer install 
```

## Routes without token.

### Register

`POST /api/register`

* User : 

   | Type | Body |                          
   |---|---|
   || firstname |
   || lastname |
   || email |
   || password |
   
* for merchant

   | POST | Champs |                          
   |---|---|
   || firstname |
   || lastname |
   || email |
   || password |
   || siren |


* /api/login: for connection

   | POST | Champs |
   |---|---|
   |  | email |
   |  | password |
   
* Route with token 


* /api/message : for consulting the newsfeed

   | POST | Fields |                          
   |---|---|
   | String | title |
   | String | description |
   | Int | user id |
  
* /api/shop : 
   | POST | Fields |                          
   |---|---|
   | String | Name |
   | String | Description |
   | String | Adresse |
   

* /api/product : for consulting a product

   | POST | Fields |
   |---|---|
   | String | Name |
   | String | Description |
   | Int | Stock |
   | Float | Price |
   
   * /api/product/{id}/modify : for modifying a product

   | POST | Fields |                          
   |---|---|
   | Int | Stock |
  
* /api/reserved/ : for reserving a product
   | POST | Fields |                          
   |---|---|
   | Int | Product_id |
   | Int | Quantity |

* /api//shop/reservation/{id}/modify : for modifying a reserved product

   | POST | Fields |                          
   |---|---|
   | Date | Date |
   | String | Status |
   | Int | Shop_id |

* /api/point/{id}/add : for adding points 

   | POST | Fields |                          
   |---|---|
   | Int | Point |
  
* /api/point/{id}/remove : for deleting points
   | POST | Fields |                          
   |---|---|
   | Int | Point |
   

* /api/user/modify : for modifying user informations

   | POST | Fields |
   |---|---|
   | String | Email |
   | String | Firstname |
   | String | Lastname |

* /api/user/MDPmodify : for modifying user password 

   | POST | Fields |                          
   |---|---|
   | String | Password |

  
* /api/message : for consulting feeds
   | GET |                          
   |---|
   | Response |
    ```json
    {
     "success": true,
    "data": [
        {
            "id": ,
            "url": "/api/message/{id}"
        },

* /api/message/{id} : for consulting one feed
  | GET |                          
   |---|
   | Response |
    ```{
    "success": true,
    "data": {
        "id": ,
        "title": "",
        "description": "",
        "shop": {
            "id": ,
            "name": "",
            "url": "/api/shop/id"
        },
        "date": "2023-04-24 07:48:12"
      }
  }

* /api/shop/{id} : for consulting a shop
  | Get |
  |---|
  | Response |
  ```
  {
     "success": true,
     "message": "Envoie du commerce et de leur produit au client",
     "shop": {
         "id": 1,
         "name": "Philippe Lafont",
         "description": "description du commerce",
         "product": [
             {
                 "id": Id of product,
                 "name": " Name of Product",
                 "description": "Description of produit ",
                 "price": Price in Int,
                 "stock": Stock in Int
             },
         "adresse": "adresse du commerce"
     }
  }

* /api/shops : for consulting all the shops
  | GET |
  |---|
  | Response |
  ```
  {
    "success": true,
    "message": "Vous pouvez consulter les commerces",
    "commerces": [
        {
            "id": id of Shop ,
            "name": " name of shop",
            "description": " description of shop",
            "adresse": " adresse of shop"
         }
     ]
  }

* /api/auth1/{id} : for generating tokens
  | GET |
  |---|
  | Response |
  
  ```
  {
    "success": true,
    
    "Token": $token,
    
    "commerces": $id,
    
    "role": $role[0],     
  }

* /api/users : for collect users informations
  | GET |
  |---|
  | Response |
  ```
  {
    "success": true,
    "data": $data    
  }
*/api/reservation
   | GET |
   |---|
   | Response |
   ```
   {
    "success": true,
      "message": "Voici vos réservations",
      "reservation": []
   }
   ```
   
   
*/api/shop/{id}/reservations
| GET |
|---|
| Response | 
```
{
    "success": true,
    "message": "Voici les réservations de vos clients",
    "reservation": [
        {
            "id": 21,
            "product": 32,
            "quantity": 62,
            "date": "2023-04-27T09:55:16+00:00"
        },
     }
   ```
*/api/getuser/{token}
| GET |
|---|
| Response |
```
{
    "success": true,
    "message": "Envoie des réservations au client",
    "user": {
        "id": 8,
        "firstname": "Philippe",
        "lastname": "Lafont",
        "email": "philippe.lafont@gmail.com"
    }
}
```
*/api/map
| GET |
|---|
| Reponse |
```json
{
    "success": true,
    "adresse": [
        {
            "id": 2,
            "address": "adresse du commerce"
        }
    ]
}
```

*/api/categories
| GET |
|---|
| Response |
```json
{
    "success": true,
    "message": "Vous pouvez consulter les catégories",
    "0": [
        {
            "id": 31,
            "name": "category 1",
            "description": "description de la category 1",
            "url": "/api/categories/31"
        },

```

## Route

| Méthode HTTP | URI              |
|--------------|------------------|
| GET          | /categories/{id} |

## Paramètres 

| Paramètre | Type   | Description                                      |
|-----------|--------|--------------------------------------------------|
| id        | string | L'identifiant de la catégorie à récupérer        |

## Réponse HTTP

### Succès

| Status code | Description                                                                            |
|-------------|----------------------------------------------------------------------------------------|
| 200         | Renvoie les informations de la catégorie avec l'identifiant spécifié sous forme de JSON si l'utilisateur est un administrateur. |

```json
{
    "success": true,
    "message": "Vous pouvez consulter les catégories",
    "categories": [
        {
            "id": 1,
            "name": "Nourriture",
            "description": "Catégorie pour les produits alimentaires."
        }
    ]
}
```

| Méthode HTTP | URI           |
|--------------|---------------|
| GET          | /user/{id}    |

## Paramètres 

| Paramètre | Type   | Description                                      |
|-----------|--------|--------------------------------------------------|
| id        | string | L'identifiant de l'utilisateur à récupérer       |

## Réponse HTTP

### Succès

| Status code | Description                                                                                                        |
|-------------|--------------------------------------------------------------------------------------------------------------------|
| 200         | Renvoie les informations de l'utilisateur avec l'identifiant spécifié sous forme de JSON si l'utilisateur est l'administrateur ou l'utilisateur lui-même. |

```json
{
    "success": true,
    "user": {
        "id": 1,
        "firstname": "John",
        "lastname": "Doe",
        "email": "johndoe@example.com",
        "role": "ROLE_USER"
    }
}
