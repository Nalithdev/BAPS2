# BAPS2


README pour Fid'Antony :

Fid'Antony est une application Web construite en utilisant le framework Symfony. Cette application est destinée à être utilisée par les commerçants et les clients pour gérer les programmes de fidélité.

Les routes suivantes sont disponibles dans l'application :

/api/login - Cette route permet à un utilisateur de se connecter à l'application en fournissant son adresse e-mail et son mot de passe. Si les identifiants sont valides, l'utilisateur est redirigé vers la route /api/auth1/{id}. Si les identifiants sont invalides, un message d'erreur est renvoyé au client.
/ - Cette route renvoie une page d'accueil par défaut.
/api/register - Cette route permet à un nouvel utilisateur de s'inscrire à l'application en fournissant son nom, son adresse e-mail, son mot de passe et une action (« shop » pour un commerçant, « customer » pour un client). En fonction de l'action fournie, l'utilisateur sera enregistré avec un rôle approprié (ROLE_MERCHANT ou ROLE_USER). Si l'utilisateur est enregistré avec le rôle ROLE_MERCHANT, il doit également fournir son numéro SIREN. Si l'enregistrement est réussi, un message de confirmation est renvoyé au client. Si les champs requis ne sont pas remplis, un message d'erreur est renvoyé au client.
/api/auth1/{id} - Cette route est utilisée pour générer un jeton d'authentification pour un utilisateur connecté. Si un jeton existe déjà pour l'utilisateur, ce jeton est renvoyé. Sinon, un nouveau jeton est créé et renvoyé. Le jeton est stocké dans la table Token pour l'utilisateur. Cette route renvoie également l'ID de l'utilisateur et son rôle.
/api/users - Cette route renvoie une liste d'utilisateurs. Seuls les utilisateurs avec le rôle ROLE_USER sont renvoyés. Les commerçants et les administrateurs ne sont pas inclus dans la liste. Les utilisateurs sont paginés en utilisant les paramètres d'offset et de limit fournis dans la requête.
Toutes les routes sont accessibles via l'API REST de l'application. Les réponses sont renvoyées au format JSON.

Pour exécuter l'application, vous devez d'abord installer toutes les dépendances en exécutant composer install. Vous devez également configurer votre base de données en modifiant le fichier .env pour inclure les informations d'identification de votre base de données. Enfin, vous pouvez lancer l'application en exécutant symfony server:start.
