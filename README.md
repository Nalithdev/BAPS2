# BAPS2


README for Fid'Antony :


1. Routes without token.


* /api/register: for registration 

* for user 

   | POST | Champs |                          
   |---|---|
   |  | firstname |
   |  | lastname |
   |  | email |
   |  | password |
   
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
    ```{
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
