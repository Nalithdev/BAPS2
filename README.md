# BAPS2


README pour Fid'Antony :

Fid'Antony est une application Web construite en utilisant le framework Symfony. Cette application est destinée à être utilisée par les commerçants et les clients pour gérer les programmes de fidélité.

Les routes suivantes sont disponibles dans l'application :

/api/login - Cette route permet à un utilisateur de se connecter à l'application en fournissant son adresse e-mail et son mot de passe. Si les identifiants sont valides, l'utilisateur est redirigé vers la route /api/auth1/{id}. Si les identifiants sont invalides, un message d'erreur est renvoyé au client.
/ - Cette route renvoie une page d'accueil par défaut.


/api/register - Cette route permet à un nouvel utilisateur de s'inscrire à l'application en fournissant son nom, son adresse e-mail, son mot de passe et une action (« shop » pour un commerçant, « customer » pour un client).
En fonction de l'action fournie, l'utilisateur sera enregistré avec un rôle approprié (ROLE_MERCHANT ou ROLE_USER). Si l'utilisateur est enregistré avec le rôle ROLE_MERCHANT, il doit également fournir son numéro SIREN.

Si l'enregistrement est réussi, un message de confirmation est renvoyé au client. Si les champs requis ne sont pas remplis, un message d'erreur est renvoyé au client.
/api/auth1/{id} - Cette route est utilisée pour générer un jeton d'authentification pour un utilisateur connecté. Si un jeton existe déjà pour l'utilisateur, ce jeton est renvoyé. Sinon, un nouveau jeton est créé et renvoyé. Le jeton est stocké dans la table Token pour l'utilisateur. Cette route renvoie également l'ID de l'utilisateur et son rôle.


/api/users - Cette route renvoie une liste d'utilisateurs. Seuls les utilisateurs avec le rôle ROLE_USER sont renvoyés. Les commerçants et les administrateurs ne sont pas inclus dans la liste. Les utilisateurs sont paginés en utilisant les paramètres d'offset et de limit fournis dans la requête.
Toutes les routes sont accessibles via l'API REST de l'application. Les réponses sont renvoyées au format JSON.

Pour exécuter l'application, vous devez d'abord installer toutes les dépendances en exécutant composer install. Vous devez également configurer votre base de données en modifiant le fichier .env pour inclure les informations d'identification de votre base de données. Enfin, vous pouvez lancer l'application en exécutant symfony server:start.


La route "/api/message" accepte des requêtes POST et permet à l'utilisateur d'envoyer un titre, une description et un identifiant. Si ces champs ne sont pas remplis, l'utilisateur recevra une réponse JSON avec un succès "false" et un message "Veuillez remplir tous les champs". Si les champs sont remplis correctement, le message sera enregistré dans la base de données et une réponse JSON avec un succès "true" et un message "Feed envoyé" sera renvoyée.

Pour utiliser cette route, un utilisateur doit d'abord se connecter et obtenir un jeton d'authentification valide. Ce jeton doit être inclus dans l'en-tête de la requête POST envoyée à la route "/api/message".

Cette route privée est accessible uniquement par les utilisateurs authentifiés. Elle retourne les messages dans un format JSON. Voici une brève description de son fonctionnement :

La route est aussi accessible via la méthode GET et doit être appelée avec l'URL "/api/message".
Elle prend deux paramètres facultatifs dans la query string : "offset" et "limit". L'offset permet de spécifier le nombre de messages à sauter avant de commencer à retourner les messages, tandis que le limit spécifie le nombre maximum de messages à retourner. Si ces paramètres ne sont pas fournis, la valeur par défaut de 50 sera utilisée pour le limit et 0 pour l'offset.
La méthode utilise les services FeedRepository et UserRepository pour récupérer les données nécessaires à la construction de la réponse. FeedRepository est utilisé pour récupérer les messages, tandis que UserRepository est utilisé pour récupérer les informations utilisateur.
Les messages sont triés par ordre décroissant de leur ID, ce qui signifie que les messages les plus récents apparaissent en premier.
La méthode construit un tableau de données à partir des messages récupérés. Le tableau contient l'ID du message et une URL qui peut être utilisée pour récupérer des informations plus détaillées sur le message.
La méthode retourne une réponse JSON avec un tableau de données construit à partir des messages récupérés.
Notez que cette route est privée et nécessite une authentification. Si un utilisateur non authentifié essaie d'accéder à cette route, il recevra une erreur 401 Unauthorized.

La route privée "/api/message/{id}" est accessible uniquement par les utilisateurs authentifiés et permet de récupérer les détails d'un message spécifique. Voici une brève description de son fonctionnement :

La route est accessible via la méthode GET et doit être appelée avec l'URL "/message/{id}", où "{id}" est l'ID du message que vous souhaitez récupérer.
La méthode utilise les services FeedRepository, UserRepository et CommerceRepository pour récupérer les données nécessaires à la construction de la réponse. FeedRepository est utilisé pour récupérer le message correspondant à l'ID fourni, UserRepository est utilisé pour récupérer les informations utilisateur associées au message, tandis que CommerceRepository est utilisé pour récupérer les informations sur la boutique associée à l'utilisateur.
La méthode construit un tableau de données à partir des informations récupérées. Le tableau contient l'ID du message, son titre, sa description, les informations sur la boutique associée, ainsi que la date à laquelle le message a été créé.
Les informations sur la boutique incluent l'ID, le nom et une URL qui peut être utilisée pour récupérer des informations plus détaillées sur la boutique.
La méthode retourne une réponse JSON avec le tableau de données construit à partir des informations récupérées.


La route privée "/api/shop" est accessible uniquement par les utilisateurs authentifiés avec le rôle "ROLE_MERCHANT". Elle permet aux utilisateurs de créer une nouvelle page de commerce. Voici une brève description de son fonctionnement :

La route est accessible via la méthode POST et doit être appelée avec l'URL "/api/shop".
La méthode vérifie d'abord que l'utilisateur authentifié a le rôle "ROLE_MERCHANT". Si l'utilisateur n'a pas ce rôle, la méthode retourne une réponse JSON avec un message d'erreur.
Si l'utilisateur a le rôle "ROLE_MERCHANT", la méthode crée une nouvelle instance de Commerce et définit ses propriétés "name" et "description" avec les valeurs fournies dans la requête POST.
La méthode utilise le service ManagerRegistry pour persister l'instance de Commerce dans la base de données et mettre à jour l'utilisateur authentifié avec la nouvelle page de commerce.
La méthode retourne une réponse JSON avec un message de succès indiquant que la page de commerce a été créée avec succès.



La route privée "/api/product" est accessible uniquement par les utilisateurs authentifiés avec le rôle "ROLE_MERCHANT". Elle permet aux utilisateurs de créer un nouveau produit associé à leur page de commerce. Voici une brève description de son fonctionnement :

La route est accessible via la méthode POST et doit être appelée avec l'URL "/api/product".
La méthode vérifie d'abord que l'utilisateur authentifié a le rôle "ROLE_MERCHANT". Si l'utilisateur n'a pas ce rôle, la méthode retourne une réponse JSON avec un message d'erreur.
Si l'utilisateur a le rôle "ROLE_MERCHANT", la méthode récupère l'instance de Commerce associée à l'utilisateur authentifié à partir de la base de données en utilisant CommerceRepository.
La méthode crée une nouvelle instance de Product et définit ses propriétés "name", "description", "price" et "stock" avec les valeurs fournies dans la requête POST. Elle associe également le produit à la page de commerce en utilisant l'instance de Commerce récupérée précédemment.
La méthode utilise le service ManagerRegistry pour persister l'instance de Product dans la base de données.
La méthode retourne une réponse JSON avec un message de succès indiquant que le produit a été créé avec succès, ainsi que les détails du produit lui-même.
Notez que cette route est privée et nécessite une authentification avec le rôle "ROLE_MERCHANT". Si un utilisateur non authentifié ou un utilisateur authentifié sans le rôle "ROLE_MERCHANT" essaie d'accéder à cette route, il recevra une erreur 401 Unauthorized ou un message d'erreur indiquant qu'il n'a pas les droits requis.



La route publique "/api/shop/{id}" permet aux utilisateurs de récupérer les détails d'un commerce spécifique, ainsi que la liste de ses produits associés. Voici une brève description de son fonctionnement :

La route est accessible via la méthode GET et doit être appelée avec l'URL "/api/shop/{id}", où "{id}" est l'ID du commerce que vous souhaitez récupérer.
La méthode utilise le service CommerceRepository pour récupérer l'instance de Commerce associée à l'ID fourni. Si aucun commerce n'est trouvé avec l'ID fourni, la méthode retourne une réponse JSON avec un message d'erreur.
Si une instance de Commerce est trouvée, la méthode utilise le service ProductRepository pour récupérer la liste des produits associés à ce commerce.
La méthode construit un tableau de données contenant les détails du commerce, y compris son ID, son nom, sa description, ainsi qu'une liste de ses produits associés. Le tableau de produits contient les détails de chaque produit, y compris son ID, son nom, sa description, son prix et son stock.
La méthode retourne une réponse JSON avec le tableau de données construit à partir des informations récupérées.


La route privée "/api/user/{id}" permet aux utilisateurs ayant le rôle "ROLE_ADMIN" ou l'utilisateur propriétaire du compte de récupérer les détails d'un utilisateur spécifique en fournissant son ID. Voici une brève description de son fonctionnement :

La route est accessible via la méthode GET et doit être appelée avec l'URL "/api/user/{id}", où "{id}" est l'ID de l'utilisateur que vous souhaitez récupérer.
La méthode utilise le service UserRepository pour récupérer l'instance de User associée à l'ID fourni.
La méthode vérifie d'abord si l'utilisateur authentifié a le rôle "ROLE_ADMIN" ou si l'ID de l'utilisateur authentifié correspond à l'ID de l'utilisateur que vous souhaitez récupérer. Si l'utilisateur n'a pas les droits requis, la méthode retourne une réponse JSON avec un message d'erreur.
Si l'utilisateur a les droits requis, la méthode construit un tableau de données contenant les détails de l'utilisateur, y compris son ID, son prénom, son nom, son e-mail et son rôle.
La méthode retourne une réponse JSON avec le tableau de données construit à partir des informations récupérées.


La route privée "/api/reserved/" permet aux utilisateurs authentifiés de créer une nouvelle réservation pour un produit spécifique. Voici une brève description de son fonctionnement :

La route est accessible via la méthode POST et doit être appelée avec l'URL "/api/reserved/".
La méthode utilise le service UserRepository pour récupérer l'instance de User associée à l'utilisateur authentifié.
La méthode crée une nouvelle instance de Reservation et définit sa propriété "user" avec l'instance de User récupérée précédemment.
La méthode définit également les propriétés "product", "quantity", "cdate" et "status" de la réservation avec les valeurs fournies dans la requête POST. La propriété "product" est définie avec l'ID du produit que l'utilisateur souhaite réserver.
La méthode utilise le service ManagerRegistry pour persister l'instance de Reservation dans la base de données.
La méthode retourne une réponse JSON avec un message de succès indiquant que la réservation a été créée avec succès.


La route privée "/api/point/{id}/add" permet aux utilisateurs authentifiés d'ajouter des points de fidélité à leur compte. Voici une brève description de son fonctionnement :

La route est accessible via la méthode POST et doit être appelée avec l'URL "/api/point/{id}/add", où "{id}" est l'ID de l'utilisateur authentifié.
La méthode utilise le service UserRepository pour récupérer l'instance de User associée à l'utilisateur authentifié.
La méthode récupère la quantité de points de fidélité à ajouter à partir des données fournies dans la requête POST.
La méthode ajoute la quantité de points de fidélité à la propriété "loyaltyPoints" de l'instance de User récupérée précédemment.
La méthode utilise le service ManagerRegistry pour persister l'instance de User dans la base de données.
La méthode retourne une réponse JSON avec un message de succès indiquant que les points de fidélité ont été ajoutés avec succès, ainsi que le nouveau total de points de fidélité de l'utilisateur.


La route privée "/api/point/{id}/remove" permet aux utilisateurs authentifiés de supprimer des points de fidélité de leur compte. Voici une brève description de son fonctionnement :

La route est accessible via la méthode POST et doit être appelée avec l'URL "/api/point/{id}/remove", où "{id}" est l'ID de l'utilisateur authentifié.
La méthode utilise le service UserRepository pour récupérer l'instance de User associée à l'utilisateur authentifié.
La méthode récupère la quantité de points de fidélité à supprimer à partir des données fournies dans la requête POST.
La méthode soustrait la quantité de points de fidélité de la propriété "loyaltyPoints" de l'instance de User récupérée précédemment.
Si le résultat est inférieur à zéro, la méthode définit la propriété "loyaltyPoints" à zéro.
La méthode utilise le service ManagerRegistry pour persister l'instance de User dans la base de données.
La méthode retourne une réponse JSON avec un message de succès indiquant que les points de fidélité ont été supprimés avec succès, ainsi que le nouveau total de points de fidélité de l'utilisateur.
