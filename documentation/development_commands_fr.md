# Commandes usuelles utilisées pour le développement

## 1 Côté serveur

### 1.1 Symfony

#### 1.1.1 La console de Symfony

La console est un petit utilitaire fourni avec Symfony pour effectuer plusieurs tâches. L'ensemble des tâches disponibles peuvent être visualisées grâce à la commande:

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **php app/console**

#### 1.1.1.1 Recharger les images et les css du bundle ogam

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **php app/console assets:install && php app/console assetic:dump**

#### 1.1.1.2 Création du cache

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **php app/console cache:warmup**

#### 1.1.1.3 Suppression du cache

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **php app/console cache:clear**

#### 1.1.1.4 Vérifier que le mapping de doctrine est correct

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **php app/console doctrine:mapping:info**

#### 1.1.2 Composer

#### 1.1.2.1 Générer les fichiers d'autoload optimisés (Pour améliorer les performances)

vagrant@ogam:/vagrant/ogam/website/htdocs/server/ogamServer$ **php composer.phar install -o**

#### 1.1.3 Passer en mode 'production'

- Ouvrir le fichier de conf apache './vagrant_config/conf/apache/httpd_ogam.conf',
- Remplacer le fichier 'app_dev.php' par le fichier 'app.php' à la ligne 66 et à la ligne 91,
- Relancer apache avec la commande: 'vagrant@ogam:/vagrant/ogam$ **sudo service apache2 restart**',
- Nettoyer le cache et le recréer.

**Note:** Le fichier 'httpd_ogam.conf' étant lié et non recopié dans la vm, il n'est pas nécessaire de le recopier à nouveau (voir ligne 97 du fichier './vagrant_config/scripts/install_apache.sh').

## 2 Côté client

### 2.1 Extjs

#### 2.1.1 Builder OgamDesktop pour le développement

vagrant@ogam:/vagrant/ogam/website/htdocs/client/ogamDesktop$ **sencha app build -c -e development**

**Note**: La commande est très lente dans la vm. Pour accélérer les performances lancer la commande directement dans Git Bash (Nécessite d'installer Sencha Cmd sur votre poste de travail).

#### 2.1.2 Builder OgamDesktop pour la production

vagrant@ogam:/vagrant/ogam/website/htdocs/client/ogamDesktop$ **sencha app build -c -e production**

**Note**: La commande est très lente dans la vm. Pour accélérer les performances lancer la commande directement dans Git Bash (Nécessite d'installer Sencha Cmd sur votre poste de travail).

## 3 Qualité

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

## 4 Divers

### 4.1 Vagrant

Plusieurs provisions ont été créées pour automatiser certaines tâches. Il est possible de lancer les provisions avec une commande du type (commande à lancer depuis la racine du projet):

**vagrant provision --provision-with 'nom_de_la_provision'**

**Note**: Les provisions en italic sont des provisions qui servent à construire la vm en environnement de développement. Les provisions en gras sont les provisions qui peuvent servir durant le développement.

#### 4.1.1 Installation Middleware

- *bootstrap*: Configuration du proxy, et mise à jour des packages debian,
- *install_java_tomcat*: Installation de tomcat,
- *install_apache*: Installation d'apache et de php,
- *install_mapserv*: Installation mapserver,
- *install_tilecache*: Installation de tilecache,
- *install_postgres*: Installation de postgres et postgis,
- *install_sencha_cmd_6*: Installation de Sencha Cmd,
- *install_dev_tools*: Installation de Ruby, JsDuck, Subversion (Nécessaire pour checkstyle),
- *fix_var_perf*: Amélioration des performances en limitant la synchronisation des répertoires de caches.

#### 4.1.2 Déploiement de l'application

- *install_gradle*: Installation de gradle,
- *install_composer_libraries*: Installation des librairies Composer,
- *move_vendor_dir*: Déplace le répertoire vendor afin de pouvoir le partager,
- *fix_vendor_perf*: Amélioration des performances en limitant la synchronisation du répertoire vendor,
- **install_db**: Installation de la base de données,
- **build_ogam_services**: Construit les services et les déploie,
- **build_ogam_desktop**: Construit OgamDesktop (Partie cliente du projet),
- **build_ogam_server**: Construit OgamServer (Partie serveur du projet).

#### 4.1.3 Documentation

- **run_phpdoc**: Lance la construction de la documentation PHP,
- **run_jsdoc**: Lance la construction de la documentation Javascript.

#### 4.1.4 Qualité du code

- **run_phpunit**: Lance les tests unitaires PHP, 
- **run_phpcheckstyle**: Lance les tests de formatage du code PHP.

#### 4.1.5 Développement

- **update_metadata**: Met à jour les métadonnées chargées dans la base de données.

### 4.2 Gradle

Les tâches gradle ont été créées pour servir de support aux provisions vagrant et à Jenkins pour l'intégration continue. Elles ne sont donc pas vouées à être utilisées directement en ligne de commande. Toutefois, pour les besoins du développement, il peut être pratique de les utiliser pour lancer uniquement certaines tâches gradle bien identifiées plutôt qu'un ensemble de tâches via vagrant.

#### 4.2.1 Voir toutes les tâches gradle disponibles depuis la racine du projet

vagrant@ogam:/vagrant/ogam$ **gradle tasks**

#### 4.2.2 Trouver toutes les sous-tâches gradle disponibles dans le projet

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

#### 4.2.2 Les sous-tâches gradle utilisées dans le répertoire '**client**'

**sencha_***: Importation des tâches de sencha Cmd

**jsduck**: Lance la génération de la doc javascript et stocke le résultat dans './build/docs/'

**deploy**: Copie les fichiers javascript dans le répertoire de déploiement:
- './build/deploy/public/', sur la VM de développement.
- '/var/www/html/public/', sur le serveur d'intégration continue.

#### 4.2.3 Les sous-tâches gradle utilisées dans le répertoire '**server**'

#### 4.2.4 Les sous-tâches gradle utilisées dans le répertoire '**server**'

#### 4.2.5 Les sous-tâches gradle utilisées dans le répertoire '**database**'

#### 4.2.6 Les sous-tâches gradle utilisées dans le répertoire '**service_common**'

#### 4.2.7 Les sous-tâches gradle utilisées dans le répertoire '**service_generation_rapport**'

#### 4.2.8 Les sous-tâches gradle utilisées dans le répertoire '**service_harmonization**'

#### 4.2.9 Les sous-tâches gradle utilisées dans le répertoire '**service_integration**'
