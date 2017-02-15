# Commandes usuelles utilisées pour le développement

## 1. Côté serveur

### 1.1. Symfony

#### 1.1.1. Recharger les images et les css du bundle ogam

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **php app/console assets:install && php app/console assetic:dump**

#### 1.1.2. Générer les fichiers d'autoload optimisés (Pour améliorer les performances)

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **php composer.phar install -o**

## 2. Côté client

### 2.1. Extjs

#### 2.1.1. Builder OgamDesktop pour le développement

vagrant@ogam:/vagrant/ogam/website/htdocs/client/ogamDesktop$ **sencha app build -c -e development**

**Note**: La commande est très lente dans la vm. Pour accélérer les performances lancer la commande directement dans Git Bash (Nécessite d'installer Sencha Cmd sur votre poste de travail).

#### 2.1.2. Builder OgamDesktop pour la production

vagrant@ogam:/vagrant/ogam/website/htdocs/client/ogamDesktop$ **sencha app build -c -e production**

**Note**: La commande est très lente dans la vm. Pour accélérer les performances lancer la commande directement dans Git Bash (Nécessite d'installer Sencha Cmd sur votre poste de travail).

## 3. Qualité

### 3.1 Procédure à suivre sur son poste de travail pour réaliser une recette

Ouvrir un Git Bash et se placer à la racine du projet (Répertoire contenant le dossier caché .git).

Lancer successivement les commandes suivantes :
- vagrant halt -f
- vagrant destroy 
- git fetch origin
- git checkout Release_Vx.x.x
- git pull
- git reset --hard
- git clean -fxd
- vagrant up

**Note**: Dans le cas de l'erreur 'Timed out while waiting for the machine to boot...' au niveau de l'authentification ssh, il faut arrêter la vm et la supprimer via virtualbox... Et ensuite relancer un 'vagrant up'.

**Note**: Dans le cas de l'erreur '...Please verify that these guest additions are properly installed in the guest...', il y a deux solutions:

- Installer le plugin VirtualBox 'guest additions',
- Changer dans le fichier vagrantfile le nom de la box de "debian/jessie64" pour "debian/contrib-jessie64" (box avec vbguest déjà installé)

### 3.2 Lancer un test unitaire rapide (sans couverture) sur un contrôleur

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **./vendor/phpunit/phpunit/phpunit --no-coverage --configuration ./phpunit.xml ./src/Ign/Bundle/OGAMBundle/Tests/Controller/QueryControllerTest.php**

**Note**: Pour ajouter la couverture, retirer l'option '--no-coverage' (Attention cela nuit gravement aux performances). Les résultats de couverture sont consultables via la page web "OGAM/website/htdocs/server/build/test-results/phpunit/coverage/index.html"

### 3.3 Générer la documentation

#### 3.3.1 Générer la documentation Javascript avec JSDuck

vagrant@ogam:/vagrant/ogam/website/htdocs/client$ **gradle jsduck**

## 4. Divers

### 4.1 Gradle

#### 4.1.1 Voir toutes les tâches gradle disponibles depuis la racine du projet

vagrant@ogam:/vagrant/ogam$ **gradle tasks**

#### 4.1.2 Trouver toutes les sous-tâches gradle disponibles dans le projet

vagrant@ogam:/vagrant/ogam/database$ **locate build.gradle**

	/vagrant/ogam/build.gradle
	/vagrant/ogam/database/build.gradle
	/vagrant/ogam/service_common/build.gradle
	/vagrant/ogam/service_generation_rapport/build.gradle
	/vagrant/ogam/service_harmonization/build.gradle
	/vagrant/ogam/service_integration/build.gradle
	/vagrant/ogam/website/htdocs/client/build.gradle
	/vagrant/ogam/website/htdocs/server/build.gradle

Se placer dans un répertoire contenant un fichier 'build.gradle' et lancer la commande 'gradle tasks'.

Exemple: vagrant@ogam:/vagrant/ogam/website/htdocs/client$ **gradle tasks**

#### 4.1.2 Les sous-tâches gradle utilisées dans le répertoire client

**sencha_***: Importation des tâches de sencha Cmd

**jsduck**: Lance la génération de la doc javascript et stocke le résultat dans './build/docs/'

**deploy**: Copie les fichiers javascript dans le répertoire de déploiement:
- './build/deploy/public/', sur la VM de développement.
- '/var/www/html/public/', sur le serveur d'intégration continue.

### 4.1 Vagrant

...