# ionic-login-poc

### Démarrer la partie serveur : 

A partir de la racine du projet 

```
cd ./server

ddev start 

ddev composer install 

ddev npm i 

ddev npm run build
```



#### Générer les clés JWT : 

Attention ! Il faut bien mémoriser la passphrase que vous choisissez lors de cette étape.

```
mkdir config/jwt

openssl genrsa -out config/jwt/private.pem -aes256 4096

openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```



Dans le .env, modifier la ligne 

```
JWT_PASSPHRASE=33e81feefc11404025f1f20a22c50eff
```

En remplaçant la valeur par la passphrase que vous avez choisie lors de l'étape précédente.



Toujours dans le .env, modifier la ligne 

```
# MAILER_DSN=smtp://localhost
```

Décommenter et ajouter le port 1025 à l'adresse 

```
MAILER_DSN=smtp://localhost:1025
```



### Lancer l'application : 

A partir de la racine du projet 

```
cd ionic-login-app/

npm i
```

Lancer sur le navigateur 

```
ionic serve
```

Lancer via android studio (nécessite android studio installé, avec un virtual device configuré)

```
ionic cap add android
ionic cap run android 
```



##### Mails de récupération de mot de passe : 

Les mails envoyés par le serveur pour réinitialiser un mot de passe sont interceptés par MailHog.