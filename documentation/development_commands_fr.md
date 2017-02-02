# Commandes usuelles utilisées pour le développement

## 1. Côté serveur

### 1.1. Symfony

#### 1.1.1. Recharger les images et les css du bundle ogam :

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ php app/console assets:install && php app/console assetic:dump

## 2. Côté client

### 2.1. Extjs

#### 2.1.1. Builder OgamDesktop pour le développement :

vagrant@ogam:/vagrant/ogam/website/htdocs/client/ogamDesktop$ sencha app build -c -e development

Note: La commande est très lente dans la vm. Pour accélérer les performances lancer la commande directement dans Git Bash (Nécessite d'installer Sencha Cmd sur votre poste de travail).

#### 2.1.2. Builder OgamDesktop pour la production :

vagrant@ogam:/vagrant/ogam/website/htdocs/client/ogamDesktop$ sencha app build -c -e production

Note: La commande est très lente dans la vm. Pour accélérer les performances lancer la commande directement dans Git Bash (Nécessite d'installer Sencha Cmd sur votre poste de travail).
