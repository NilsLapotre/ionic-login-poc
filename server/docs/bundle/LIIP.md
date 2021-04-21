# LIIP
* liip imagine permet d'optimisé des images, ces dernieres sont stockées dans le dossier public/media. Si l'image appelé n'a jamais été resizé liip_imagine resize l'image a la volée.


## config
* Le fichier de config se trouve dans /config/packages/liip_imagine.yaml, dans ce fichier il est possible de définir les differents formats des images que vous souhaitez

## utilisation
* Pour utiliser ce bundle, dans la vue twig concernée il suffit d'appeler "imgage_filter" dans la vue twig. 
* exemple  "{{ image | imagine_filter('filtreName') }}" ( filtreName etant le nom du filtre definit dans le fichier de configurationb )

