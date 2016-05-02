# Installation de l'intégration continue pour le projet OGAM sur une machine Debian.

VM Debian 8.0 nue avec un compte "admin" ayant les droits root via sudo.

## Configuration de l'intégration continue

### Installation de Java

> sudo apt-get install openjdk-7-jdk

Test : java -version

### Installation de Jenkins

cf [https://wiki.jenkins-ci.org/display/JENKINS/Installing+Jenkins+on+Ubuntu](https://wiki.jenkins-ci.org/display/JENKINS/Installing+Jenkins+on+Ubuntu)

Note :
Pour une machine dans le réseau interne IGN il faut déclarer le proxy IGN au niveau du wget 
dans le fichier /etc/wgetrc 

> use_proxy=on	
> https_proxy = https://proxy.ign.fr:3128/   
> http_proxy = http://proxy.ign.fr:3128/	

Dans /etc/apt/apt.conf.d/proxy mettre : 

> Acquire::http::Proxy "http://proxy.ign.fr:3128";	
> Acquire::ftp::Proxy "http://proxy.ign.fr:3128";	
	
Dans /etc/environment mettre :
> HTTP_PROXY="http://proxy.ign.fr:3128/";	
> HTTPS_PROXY="https://proxy.ign.fr:3128/";		
> FTP_PROXY="http://proxy.ign.fr:3128/";	

Ajout du paquet jenkins à apt-get

> wget -q -O - https://jenkins-ci.org/debian/jenkins-ci.org.key | sudo apt-key add -	
> sudo sh -c 'echo deb http://pkg.jenkins-ci.org/debian binary/ > /etc/apt/sources.list.d/jenkins.list'		
> sudo apt-get update	
> sudo apt-get install jenkins	


Dans /etc/default/jenkins il faut aussi ajouter le proxy sinon Jenkins ne voit pas ses plugins.

> JAVA_ARGS="-Dhttp.proxyHost=proxy.ign.fr -Dhttp.proxyPort=3128 -Dhttps.proxyHost=proxy.ign.fr -Dhttps.proxyPort=3128"


Modification du port par défaut pour Jenkins
cf https://wiki.jenkins-ci.org/display/JENKINS/Installing+Jenkins+on+Ubuntu

> sudo nano /etc/default/jenkins 

Remplacer HTTP_PORT=8080 par HTTP_PORT=8081

Modifier la dernière ligne
JENKINS_ARGS="--webroot=/var/cache/$NAME/war --httpPort=$HTTP_PORT --ajp13Port=$AJP_PORT --prefix=/jenkins"

L'URL devient 
http://ogam-integration.ign.fr:8081/jenkins/



### Installation de ANT

> sudo apt-get install ant





	
### Installation de GIT

cf http://www.mon-code.net/article/42/installation-et-configuration-de-git-sur-debian-et-initialisation-dun-depot-git

> sudo apt-get install git

Création d'un nouveau compte "ogam-ci" dans GitLab.





### Configuration de Jenkins

Dans la partie "Administrer Jenkins" du site, on ajoute les plugins Git 

Cf la config sur le site : http://ogam-integration.ign.fr:8080/job/OGAM_Website/configure

Ajout des librairies nécessaires "en dur" dans le workspace (en attendant Maven ou gradle).

> cd /var/lib/jenkins/workspace/OGAM_Website/
> sudo mkdir libraries
> sudo chown jenkins:jenkins libraries/
> sudo chmod 775 libraries/

Ajout de l'utilisateur admin dans le groupe "jenkins"
> sudo usermod -G jenkins -a admin

Ajout de l'utilisateur jenkins dans le groupe "www-data"
> sudo usermod -G www-data -a jenkins


Recopie de "libs_php" et "libs_java" dans ce répertoire.

> sudo chown -R jenkins:jenkins libraries/
> sudo chmod -R 775 libraries/



	
	
### Installation de Apache

> sudo apt-get install apache2

Test : Afficher la page "http://ogam-integration.ign.fr/" dans un navigateur

Ajout de l'utilisateur admin dans le groupe "wwww-data"
> sudo usermod -G www-data -a admin

Vérif que ça a fonctionné
> id admin

On donne les droits sur le répertoire /var/www/html à admin et à Apache.
> cd /var/www/	
> sudo chgrp www-data /var/www/html/	
> sudo chmod 775 html/	

Test : Copier un fichier dans /var/www/html


### Mise en place d'un proxy apache pour Jenkins

cf https://wiki.jenkins-ci.org/display/JENKINS/Installing+Jenkins+on+Ubuntu

> sudo a2enmod proxy
> sudo a2enmod proxy_http

Modification du fichier ogam.conf (voir plus bas) pour ajouter les commandes 
 		# Proxy pour Jenkins
        ProxyPass               /jenkins        http://ogam-integration.ign.fr:8081/jenkins/
        ProxyPassReverse        /jenkins        http://ogam-integration.ign.fr:8081/jenkins/


> sudo /etc/init.d/apache2 restart


### Installation de PHP

> sudo apt-get install php5	
> sudo /etc/init.d/apache2 restart	

Test : 
Copier un fichier "info.php" dans /var/www/html, avec la commande php_info();
Afficher la page "http://ogam-integration.ign.fr/info.php"

Ajout du driver postgresql pour PHP

> sudo apt-get install php5-pgsql

Ajout de XDebug pour avoir le coverage sur les tests unitaires

> sudo apt-get install php5-xdebug

Ajout de GD pour l'export PDF de la fiche de détail

> sudo apt-get install php5-gd



### Installation de PostgreSQL

cf https://wiki.debian.org/PostgreSql

> sudo apt-get install postgresql 	
> sudo apt-get install postgresql-9.4-postgis-2.1 	
> sudo apt-get install postgresql-contrib 	

Edition du pg_hba.conf
> sudo nano /etc/postgresql/9.4/main/pg_hba.conf

pour autoriser les connexions en local
local   all             all                                  trust	
pour l'utilisateur ogam 	
host    ogam     ogam                   0.0.0.0/0       md5	

Et ajout des accès externes avec le compte ogam


Edition du postgresql.conf
> sudo nano /etc/postgresql/9.4/main/postgresql.conf	

listen_addresses = '*' 

> sudo /etc/init.d/postgresql reload	


Connection à la base pour créer un utilisateur
> sudo -u postgres psql	

> CREATE USER ogam WITH PASSWORD 'ogam';	
> CREATE DATABASE ogam OWNER ogam;	
> \q

> sudo -u postgres psql	-d ogam
> CREATE EXTENSION adminpack;
> CREATE EXTENSION postgis;
> CREATE EXTENSION postgis_topology;
> \q





### Installation de Gradle

> sudo apt-get install gradle

==> Pb : c'est la version 1.5 qui est sur le repository debian par défaut, il nous faut une 2.x.



Ajout d'un repository PPA
> sudo apt-get install software-properties-common
> sudo su
> export https_proxy=http://proxy.ign.fr:3128/
> export http_proxy=http://proxy.ign.fr:3128/
> add-apt-repository ppa:cwchien/gradle

> sudo nano /etc/apt/sources.list.d/cwchien-gradle-jessie.list              remplacer jessy par wily

> sudo apt-get update
> sudo apt-get install gradle




Mise à jour des droits
> cd /var/lib/jenkins/workspace
> sudo chmod 775 OGAM_Common/
> sudo chmod 775 OGAM_Harmonization/
> sudo chmod 775 OGAM_Integration/
> sudo chmod 775 OGAM_Website/



### Installation de JSDuck

Pour la génération de la doc javascript

>sudo apt-get update
>sudo apt-get install -y ruby ruby-dev
>sudo apt-get install curl

Cf http://stackoverflow.com/questions/29317640/gem-install-rails-fails-on-ubuntu
>export https_proxy=https://proxy.ign.fr:3128   
>export http_proxy=http://proxy.ign.fr:3128	
>gpg --keyserver hkp://keys.gnupg.net --recv-keys 409B6B1796C275462A1703113804BB82D39DC0E3
>\curl --proxy https://proxy.ign.fr:3128 -sSL https://get.rvm.io | sudo bash -s stable

CURL n'a pas fonctionné avec le proxy de l'IGN, j'ai donc directement téléchargé le contenu de cette adresse [https://raw.githubusercontent.com/wayneeseguin/rvm/master/binscripts/rvm-installer](rvm-installer) dans un fichier texte "rvminstall" que j'ai rendu exécutable.
>chmod 744 rvminstall
>./rvminstall
>source /home/admin/.rvm/scripts/rvm
>rvm requirements



>sudo apt-get install -y ruby ruby-dev

>sudo gem install --http-proxy http://proxy.ign.fr:3128/ jsduck


Modification de la configuration de Jenkins pour désactiver le Sandboxing lors de la publication de HTML

Dans /etc/default/jenkins on ajoute 
>-Dhudson.model.DirectoryBrowserSupport.CSP=


### Installation de Sencha Cmd


On télécharge Sencha Cmd et on le stocke dans un répertoire /home/Sencha
>sudo  apt-get install  unzip
>export https_proxy=proxy.ign.fr:3128
>export http_proxy=proxy.ign.fr:3128
>sudo mkdir -p /home/Sencha/Cmd/6.0.2.14/

>wget http://cdn.sencha.com/cmd/6.0.2.14/no-jre/SenchaCmd-6.0.2.14-linux-amd64.sh.zip
>unzip ./SenchaCmd-6.0.2.14-linux-amd64.sh.zip

>sudo ./SenchaCmd-6.0.2.14-linux-amd64.sh -q -dir /home/Sencha/Cmd/6.0.2.14/

>sudo chown -R admin:admin Sencha/
>sudo chmod -R 774 Sencha/

>sudo echo '-Dhttp.proxyHost=proxy.ign.fr' >> /home/Sencha/Cmd/6.0.2.14/sencha.vmoptions 

Ajout de la commande sencha dans le path utilisateur
On ajoute les lignes suivantes dans le .profile
>export PATH="$PATH:/home/Sencha/Cmd"

Puis
>source .profile


Ajout de Jenkins dans le groupe admin
>sudo usermod -G admin -a jenkins


### Installation des outils de build PHP

Il faut installer Subversion pour Composer
>sudo apt-get install subversion

Evidemment ça ne fonctionne pas à cause du proxy.
J'ai tenté plusieurs modifications sans résultat (création d'un .profile pour le compte jenkins, ...)/

Au final la conf dans ce fichier :
>sudo nano /etc/subversion/servers





## Installation d'un site OGAM de démo



### Installation de Mapserver

> sudo apt-get install cgi-mapserver mapserver-bin
> sudo apt-get install libapache2-mod-fcgid

Test : /usr/lib/cgi-bin/mapserv -v

Utilisation de Fast CGI
> sudo a2enmod cgi
> sudo cp /usr/lib/cgi-bin/mapserv /usr/lib/cgi-bin/mapserv.fcgid
> sudo /etc/init.d/apache2 restart

Test : Appeler l'URL http://ogam-integration.ign.fr/cgi-bin/mapserv.fcgid?

### Copie des fichiers PHP

Après execution de la tâche "deploy" du script de build, on recopie le répertoire généré dans /var/www/html

On donne l'accès à Apache
> cd /var/www/
> sudo chown root:www-data -R html/
> sudo chmod 775 -R html/


### Configuration de Apache

Ajout d'un nouveau fichier ogam.conf dans /etc/apache2/sites-enabled   (cf le fichier)

Activation de Mod_Rewrite
> sudo a2enmod rewrite 

Activation de la nouvelle conf
> sudo a2ensite ogam

Désactivation de la conf par défaut
> sudo a2dissite 000-default

Redémarrage de Apache
> sudo /etc/init.d/apache2 restart


Test : Appeler l'URL http://ogam-integration.ign.fr/
On doit avoir une page blanche (erreur PHP) au lieu de la page par défaut de Debian

### Configuration du site OGAM

Recopier la configuration /ogam/application/configs/application.ini

Modifier l'url de base dans le fichier de config
> resources.frontController.baseUrl = "/"; The trailing slash is important

Mise en place de connexion persistantes
> resources.db.params.persistent = true

Augmentation du nombre de connexions max dans PostgreSQL (pour les tests unitaires)
> sudo nano /etc/postgresql/9.4/main/postgresql.conf
> max_connections = 200


Test : Appeler l'URL http://ogam-integration.ign.fr/
On doit avoir la page d'accueil du site OGAM
La connexion avec l'utilisateur admin / admin doit fonctionner
Cliquer sur le menu "Vérifier la configuration", tout doit être OK


### Installation de Tomcat

> sudo apt-get install tomcat7 tomcat7-admin
> sudo apt-get install tomcat7-admin tomcat7-examples tomcat7-docs

Test: http://ogam-integration.ign.fr:8080/   vérifier qu'on a bien la page It works !


Création d'un répertoire pour le dépot des fichiers war.

> cd /var/lib/tomcat7/
> sudo mkdir staging
> sudo chmod 775 staging/
> sudo chown tomcat7:tomcat7 staging/

Ajout de l'utilisateur admin dans le groupe "tomcat7"
> sudo usermod -G tomcat7 -a admin

On peut maintenant recopier des fichiers dans ce répertoire /var/lib/tomcat7/staging/ via WinSCP.

Config des utilisateurs Tomcat
> sudo nano /etc/tomcat7/tomcat-users.xml

Ajout les librairies supplémentaires
Les librairies pour Tomcat se trouvent dans /usr/share/tomcat7/lib
Y copier "activation.jar", "mail.jar" et "postgresql-9.3-1101.jdbc4.jar".


Création d'un répertoire temporaire pour l'upload de fichier
> cd /var/tmp
> sudo mkdir ogam_upload
> sudo chmod 664 ogam_upload
> sudo chown tomcat7:tomcat7 ogam_upload 

### Configuration des services Tomcat

Après execution de la tâche "deploy" du script de build de "service_integration"

Recopier dans /var/lib/tomcat7/staging/ les fichiers war à déployer avec les fichier xml.

Utiliser la console d'administration de Tomcat pour déployer les services.


### Configuration de Mapserver

Copier le fichier de config mapserv "ogam.map" et les fontes dans un répertoire sur le serveur.

> cd /var/www/html
> sudo mkdir mapserv
> sudo chmod 775 mapserv
> sudo chown root:www-data mapserv 


Mettre à jour le fichier "ogam.map" avec les bons chemins et la config pour la base de donnée.

> sudo nano /var/www/html/mapserv/ogam.map

Mettre à jour les chemins dans la table "mapping.layer_service"

Test : Appeler http://ogam-integration.ign.fr/cgi-bin/mapserv.fcgid?map=/var/www/html/mapserv/ogam.map&LAYERS=nuts_0&TRANSPARENT=TRUE&FORMAT=image%2FPNG&ISHIDDEN=false&ISDISABLED=false&ISCHECKED=true&ACTIVATETYPE=NONE&HASSLD=false&SESSION_ID=e5kvmhtt062k941smr07tdg2qd93165o&PROVIDER_ID=1&SERVICE=WMS&VERSION=1.1.1&REQUEST=GetMap&STYLES=&SRS=EPSG%3A3035&BBOX=1690000,1000000,6099719.8409735,5409719.8409735&WIDTH=500&HEIGHT=500

On doit avoir la carte de l'europe

### Ajout d'un alias Mapserver

Modifier la config apache pour ajouter l'alias
> sudo nano /etc/apache2/sites-available/ogam.conf


    <IfModule mod_alias.c>
        <IfModule mod_cgi.c>
                Define ENABLE_USR_LIB_CGI_BIN
        </IfModule>

        <IfModule mod_cgid.c>
                Define ENABLE_USR_LIB_CGI_BIN
        </IfModule>

		<IfDefine ENABLE_USR_LIB_CGI_BIN>
			ScriptAlias "/mapserv-ogam" "/usr/lib/cgi-bin/mapserv.fcgid"
			<Location "/mapserv-ogam">
				SetEnv MS_MAPFILE "/var/www/html/mapserv/ogam.map"
				SetEnv MS_ERRORFILE "/var/www/html/logs/mapserver_error.log"
				SetEnv MS_DEBUGLEVEL 5
			</Location>
		</IfDefine>
    </IfModule>


Redémarrage de Apache
> sudo /etc/init.d/apache2 restart

### Installation de Tilecache

Récupérer l'archive sur le site de tilecache.
Copier le fichier dans /tmp par exemple/

> sudo mv tilecache-2.11.tar.gz /var/www/
> cd /var/www/
> sudo tar -zvxf tilecache-2.11.tar.gz
> sudo chown root:www-data -R tilecache-2.11
> sudo chmod 775 -R tilecache-2.11/
> sudo mv tilecache-2.11/ tilecache

Création d'un répertoire pour le cache
> cd /var/www/tilecache
> sudo mkdir cache
> sudo chown root:www-data -R cache
> sudo chmod 775 -R cache/

Modif de la config Apache
> sudo nano /etc/apache2/sites-enabled/ogam.conf


Modif de la config Tilecache
> sudo nano /var/www/tilecache/tilecache.cfg

Test : Appeler http://ogam-integration.ign.fr/cgi-bin/tilecache, on doit avoir un XML de résultat vide

Installation de python imaging pour activer les metatiles
> sudo apt-get install python-imaging


Note : J'ai du récupérer la version de Tilecache présente sur le serveur Eforest car apparemment la dernière version dispo sur
le site web provoque un bug de calcul de coordonnée quand on zoom (cf http://osgeo-org.1560.x6.nabble.com/Current-x-value-is-too-far-from-tile-corner-x-td3964919.html). 
