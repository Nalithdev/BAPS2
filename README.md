# BAPS2


README for Fid'Antony :


1. Route without token.


* /api/register:
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


* /api/login:

   | POST | Champs |
   |---|---|
   |  | email |
   |  | password |
   
* Route with token 


* /api/message

   | POST | Fields |                          
   |---|---|
   | String | title |
   | String | description |
   | Int | user id |
  
* /api/shop
   | POST | Fields |                          
   |---|---|
   | String | Name |
   | String | Description |
   | String | Adresse |
   

* /api/product

   | POST | Fields |
   |---|---|
   | String | Name |
   | String | Description |
   | Int | Stock |
   | Float | Price |
   
   * /api/product/{id}/modify

   | POST | Fields |                          
   |---|---|
   | Int | Stock |
  
* /api/reserved/
   | POST | Fields |                          
   |---|---|
   | Int | Product_id |
   | Int | Quantity |

* /api//shop/reservation/{id}/modify

   | POST | Fields |                          
   |---|---|
   | Date | Date |
   | String | Status |
   | Int | Shop_id |

* /api/point/{id}/add

   | POST | Fields |                          
   |---|---|
   | Int | Point |
  
* /api/point/{id}/remove
   | POST | Fields |                          
   |---|---|
   | Int | Point |
   

* /api/user/modify

   | POST | Fields |
   |---|---|
   | String | Email |
   | String | Firstname |
   | String | Lastname |

* /api/user/MDPmodify

   | POST | Fields |                          
   |---|---|
   | String | Password |

  
* /api/message
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
* /api/message/{id}

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
* /api/shop/{id}
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
* /api/shops
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
