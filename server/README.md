# Starter kit
Ce kit est une base pour un projet en Symfony.

## Sommaire
- [Environnement de dev](#Installation de l'environnement de dev en local)
- [Installation du projet](#Installation du projet)
- [Compilation des fichiers js et sass](#Compilation des fichier js)
- [Tests avec Codeception](#Tests avec Codeception)
- [Failles de sécurité dans les dépendances](#Vérification des failles de sécurité)
- [Qualité du code](#Qualité du code)
- [Monitoring](#Audit des erreurs)

## Installation de l'environnement de dev en local
### Installation de DDEV (si besoin) 
Se référer à ce dépôt: https://gitlab.ecedi.fr/simplonprod/devops/ddev
### Configuration du projet pour ddev
éditer le fichier .ddev/config.yaml et changer au minimum le nom du projet
```bash
APIVersion: v1.12.0
name: symfony-starter-kit
type: php
docroot: public
php_version: "7.2"
webserver_type: nginx-fpm
router_http_port: "80"
router_https_port: "443"
xdebug_enabled: false
additional_hostnames: []
additional_fqdns: []
nfs_mount_enabled: false
provider: default
use_dns_when_possible: true
timezone: ""
```
### Lancer les différents services
```bash
  $ ddev start
```
Vous pouvez voir normalement:
- la home du projet: https:{nom du projet}.ddev.site
- le phpMyAdmin: http:{nom du projet}.ddev.site:8036
- Le MailHog: http:{nom du projet}.ddev.site:8025

### Exécuter des commandes dans les containers
```bash
  $ ddev composer     # exécuter composer
  $ ddev exec php     # exécuter une commande php
```


## Installation du projet

```bash
  $ ddev composer install
  $ ddev doctrine:migrations:migrate --force
  $ ddev npm install
```

## Compilation des fichier js && sass

### Executé une seule fois

```bash
  $ ddev npm run dev
```

### Executé à chaque modification des fichiers CSS/JS
```bash
  $ ddev npm run dev-server
```

## Tests avec Codeception

### Lancer les tests
```bash
  $ php vendor/bin/codecept run                                 # lance tous les tests
  $ php vendor/bin/codecept run unit                            # lance la suite des tests unitaires
  $ php vendor/bin/codecept run tests/acceptance/FirstCest.php  # lance un test en particulier  
```

### Paramétrages
#### Configurer les tests d'acceptance
Il faut un server qui tourne localement et renseigner l'url de l'application dans le fichier: tests/acceptance.suite.yml
```bash
actor: AcceptanceTester
modules:
    enabled:
        - PhpBrowser:
            url: {L'URL DE L'APPLICATION}
        - \Helper\Acceptance
```

## Vérification des failles de sécurité (dans les dépendances)
Installer l'exécutable symfony: https://symfony.com/download puis vérifier les dépendances en lançant régulièrement la commande suivante:
```bash
  $ symfony check:security
```

## Qualité du code
- [SonarQube](docs/SONARQUBE.md)

## Audit des erreurs
- [Sentry](docs/SENTRY.md)


  