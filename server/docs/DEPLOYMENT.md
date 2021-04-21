# Procédures de déploiement sous gitlab

## Mise en place des instructions pour gitlab
* Copier le fichier gitlab-ci.yml.dist dans gitlab-ci.yml
* Compléter celui-ci notamment pour les étapes de:
    * lint: vérification du respect du code selon des règles et des normes d'écritures
    * quality: vérification de la qualité du code, cf [SonarQube](SOARQUBE.md)
    * tests: exécutions de tests
* Ne sont présentes dans ce fichier que les instructions de déploiement sur les environnements intermédiaires:
    * branche "dev" => environnement de "development" sur simplon.space
    * branche "staging" => environnement de "staging" sur simplon.space
* Prévoir la procédure de déploiement de la branche "prod" sur l'environnement de production en fonction des spécificités du projet

## Configuration des instructions de déploiement sur simplon.space
### les variables d'environnement
* Editer le fichier `.deploy/staging/env`
* Adapter les variables au projet et à l'environnement
### les instructions de déploiement
* Editer le fichier `.deploy/staging/deploy.yml`
* Adapter les instructions d'installation au projet
* Editer le fichier `.deploy/staging/nginx.conf` et modifier si besoin la config du serveur http pour les environnements de "development" et de "staging"
