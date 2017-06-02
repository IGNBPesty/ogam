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
- Nettoyer le cache et le recréer (Voir chapitres: "1.1.1.3 Suppression du cache" et "1.1.1.2 Création du cache"),
- Builder OgamDesktop pour la production (Voir chapitre: "2.1.2 Builder OgamDesktop pour la production").

**Note:** Le fichier 'httpd_ogam.conf' étant lié et non recopié dans la vm, il n'est pas nécessaire de le recopier à nouveau (voir ligne 97 du fichier './vagrant_config/scripts/install_apache.sh').

#### 1.1.4 Les proxys

Il y a trois proxys différents dans OGAM:

- ProxyController : Contrôleur Symfony contenant une seule action permettant d'accéder aux rapports de soumission,
- mapserverProxy : Script php permettant d'accéder à mapserver directement sans passer par Symfony,
- tilecacheProxy : Script php permettant d'accéder à tilecache directement sans passer par Symfony.

##### 1.1.4.1 Débuguer une image vide via le proxy mapserver

##### 1.1.4.1.1 Solution 1 : via les logs de mapserver

vagrant@ogam:/vagrant/ogam/website/htdocs/logs$ **tail -n 100 mapserver_ogam.log**

##### 1.1.4.1.2 Solution 2 : via le navigateur

- Dans le fichier 'http_ogam.conf' mettre en commentaire les sept premières lignes pour autoriser l'accés à mapserver depuis le poste de développment:

```
	<Directory  "/usr/lib/cgi-bin">
	    Options ExecCGI
	    Order Deny,Allow
		Deny from all
		Allow from localhost 127.0.0.1
	</Directory>
```

- Relancer apache avec la commande: ‘vagrant@ogam:/vagrant/ogam$ sudo service apache2 restart’,
- Dans le fichier 'mapserverProxy.php':
	- Mettre en commentaire les headers nécessaire en fonction du cas à tester (ex: header('Content-Type: image/png');),
	- Décommenter la ligne 78 ('echo $uri;exit;') pour afficher la requête locale générée par le proxy,
- Relancer la requête qui renvoie une image vide,
- Copier-coller l'url générée dans un nouvel onglet du navigateur, remplacer 'localhost' par l'IP de la VM (par défaut: 192.168.50.4) et supprimer le paramètre 'EXCEPTIONS=BLANK' de l'url.
- Ouvrir le fichier 'mapserv-ogam' téléchargé par le navigateur avec un editeur de text et consulter les logs d'erreur.

## 2 Côté client

### 2.1 Extjs

#### 2.1.1 Builder OgamDesktop pour le développement

vagrant@ogam:/vagrant/ogam/website/htdocs/client/ogamDesktop$ **sencha app build -e development**

**Note**: La commande est très lente dans la vm. Pour accélérer les performances lancer la commande directement dans Git Bash (Nécessite d'installer Sencha Cmd (version identique à celle de la VM) sur votre poste de travail).

**Note**: L'utilisation de l'option de build '-c' provoque la disparition des fichiers des builds précédents dans le cas d'un enchaînement de build. Un enchaînement de build est provoqué par la déclaration de plusieurs langues via le paramètre "locales" (ex: ["fr","en"]) du fichier "app.json".

#### 2.1.2 Builder OgamDesktop pour la production

vagrant@ogam:/vagrant/ogam/website/htdocs/client/ogamDesktop$ **sencha app build -e production**

**Note**: La commande est très lente dans la vm. Pour accélérer les performances lancer la commande directement dans Git Bash (Nécessite d'installer Sencha Cmd (version identique à celle de la VM) sur votre poste de travail).

**Note**: L'utilisation de l'option de build '-c' provoque la disparition des fichiers des builds précédents dans le cas d'un enchaînement de build. Un enchaînement de build est provoqué par la déclaration de plusieurs langues via le paramètre "locales" (ex: ["fr","en"]) du fichier "app.json".

## 3 Qualité

### 3.1 Procédure à suivre pour réaliser une recette

#### 3.1.1 Création de la branche de recette

- Ouvrir la page : 'http://gitlab.dockerforge.ign.fr/ogam/ogam/branches',
- Cliquer sur le bouton 'New Branch',
- Dans le champ 'Name for new branch', entrer le nom de la nouvelle branche (ex: Release_Vx.x.x),
- Dans le champ 'Create from', entrer 'develop' pour créer la branche à partir du dernier commit de la branche 'develop'.

#### 3.1.2 Création de la VM depuis zéro

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

#### 3.1.3 Passer en mode 'production'

Voir chapitre: "1.1.3 Passer en mode 'production'".

#### 3.1.4 Cahier de tests et corrections

- Copier le dernier fichier de recette: OGAM\documentation\4 - Intégration et recette\Dossier de tests Vx.x.x.doc,
- Créer un nouveau fichier de recette en respectant la convention de nommage,
- Lorqu'un nouveau bug est trouvé:
	- Créer un ticket dans JIRA en indiquant la page et le numéro de ligne du test dans les commentaires,
	- Reporter le numéro de bug et le libellé dans le dossier de tests,
	- Corriger le bug et le commiter sur la branche de recette.
- Une fois les tests finis commiter le dossier de tests.

#### 3.1.5 Merge de la branche de recette

Une fois tous les bugs corrigés et que l'application est jugée suffisamment stable, il faut merger la branche sur la branche 'master' et sur la branche 'develop'.

#### 3.1.5.1 Merge sur master

Jouer les lignes de commande suivantes:

- git fetch origin
- git checkout Release_Vx.x.x
- git pull
- git checkout master
- git pull
- git merge --no-ff -m "Merge version x.x.x" Release_Vx.x.x
- git tag -a vx.x.x -m "Version x.x.x" HEAD
- git push --tags origin master

Vérifier que tout est bien passé sur [gitlab](http://gitlab.dockerforge.ign.fr/ogam/ogam/network/master).

#### 3.1.5.2 Merge sur develop

Jouer les lignes de commande suivantes:

- git fetch origin
- git checkout Release_Vx.x.x
- git pull
- git checkout develop
- git pull
- git merge --no-ff -m "Merge version x.x.x" Release_Vx.x.x
- git push origin develop

Vérifier que tout est bien passé sur [gitlab](http://gitlab.dockerforge.ign.fr/ogam/ogam/network/develop).

#### 3.1.6 Clôturer la version dans Jira

Pour les tâches de la version:

- Déplacer dans une version postérieure les tâches non cloturées si nécessaire,
- Sélectionner toutes les tâches clôturées et les fermer via un traitement par lot,
- Publier la version.

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

#### 4.2.3 Les tâches gradle déclarées dans le répertoire '**root**'

Aucunes tâches n'est déclarées dans le fichier de build, mais on peut y trouver de la configuration pour les sous-tâches.

#### 4.2.4 Les sous-tâches gradle déclarées dans le répertoire '**client**'

**sencha_***: Importation des tâches de sencha Cmd.

**jsduck**: Lance la génération de la doc javascript et stocke le résultat dans './build/docs/'.

**deploy**: Copie les fichiers javascript dans le répertoire de déploiement:
- './build/deploy/public/', sur la VM de développement.
- '/var/www/html/public/', sur le serveur d'intégration continue.

#### 4.2.5 Les sous-tâches gradle déclarées dans le répertoire '**server**'

**installComposer**: Télécharge et installe Composer.

**installLibraries**: Télécharge et installe les librairies nécessaires au projet via Composer.

**phpdoc**: Lance la génération de la documentation php via PHPDoc.

**phpcheckstyle**: Lance les analyses statistiques via PHPCheckstyle.

**phpunit**: Lance les tests unitaires via PHPUnit.

**cleanDeploy**: Nettoie de dossier de déploiement.

**deploy**: Copie les fichiers PHP dans le répertoire de déploiement.

**setBuildNumber**: Met à jour le numéro de déploiement.

**clearCache**: Supprime le cache de symfony.

**installAssets**: Installe les assets (images, css...).

**asseticDump**: Recopie les assets (images, css...).

**buildAll**: Construit l'application (clearCache, asseticDump, installAssets).

#### 4.2.6 Les sous-tâches gradle déclarées dans le répertoire '**database**'

Aucunes tâches n'est déclarées dans le fichier de build. Toutefois les tâches de **Liquibase** sont importées.

#### 4.2.7 Les sous-tâches gradle déclarées dans le répertoire '**service_common**'

Aucunes tâches n'est déclarées dans le fichier de build, mais on peut y trouver de la configuration pour les sous-tâches (checkstyle).

#### 4.2.8 Les sous-tâches gradle déclarées dans le répertoire '**service_generation_rapport**'

**cargo***: Importation des tâches de déploiement de Cargo

**addReports**: Ajoute les rapports à l'archive war.

**deploy**: Déploie l'archive war dans le Tomcat local.

#### 4.2.9 Les sous-tâches gradle déclarées dans le répertoire '**service_harmonization**'

**cargo***: Importation des tâches de déploiement de Cargo

**deployConfig**: Copie les fichiers de configuration dans le Tomcat local.

**deploy**: Déploie l'archive war dans le Tomcat local.

#### 4.2.10 Les sous-tâches gradle déclarées dans le répertoire '**service_integration**'

**cargo***: Importation des tâches de déploiement de Cargo

**deployConfig**: Copie les fichiers de configuration dans le Tomcat local.

**deploy**: Déploie l'archive war dans le Tomcat local.

