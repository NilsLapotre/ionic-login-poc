# SENTRY

## Configuration
### Créer un projet Sentry associé au projet
* Aller sur https://sentry.simplon.space et se connecter avec son compte gmail pro
* Créer un nouveau projet (`Add new Project`)
* Récupérer le DSN du projet (`Get your DSN`)
### Configurer le projet pour la partie symfony
* Renseigner la valeur du DSN dans les variables (SENTRY_DSN) des environnements que l'on veut monitorer (le fichier `.deploy/staging/env` par exemple)
* Préciser éventuellement la configuration de sentry en éditant le fichier `config/packages/sentry.yaml` notamment pour exclure certaines erreurs du monitoring (cf https://github.com/getsentry/sentry-symfony)
