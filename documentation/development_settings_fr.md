# Installation et configuration de l'environnement de développement

## 1. Environnement de Serveur web préconfiguré : MS4W/FGS

Note : Une liste de package Windows et Linux incluant Apache, PHP et MapServer sont disponibles [ici](http://mapserver.org/fr/download.html).

###Windows

- Télécharger : [MS4W](http://www.maptools.org/ms4w/index.phtml?page=downloads.html)
- L'installer par exemple dans `C:\Program Files\ms4w`.

###Linux

- Télécharger et installer : [FGS](http://www.maptools.org/fgs/)

**Serveur HTTP : Apache**

- Dans le `httpd.conf` d'Apache, déplacer la partie `<IfModule alias_module> ... </IfModule>` à la fin du fichier.  
**Note :** Ceci n'est pas obligatoire mais évite des warnings lors des tests de syntaxe. 
(Problème d'imbrication de SrciptAlias dans le répertoire cgi-bin)
- Dans le `httpd.conf` d'Apache, Il est recommandé de changer le path des fichiers httpd_*.conf pour un répertoire en dehors 
de ms4w et sauvegardé régulièrement afin d'éviter la perte de vos fichiers de configuration.  
**Exemple :** include `D:/DONNEES/Ms4w/httpd.d/httpd_*.conf`
- Copier `website/config/httpd_ogam.conf` dans votre répertoire httpd.d (adapter les répertoires)
- Vérifiez votre configuration Apache (Dans le menu "Démarrer" de windows lancer la commande "cmd". Puis se placer dans le répertoire "bin" de Apache (`C:\Program Files\ms4w\Apache\bin`). Lancer ensuite la commande `httpd -t`).

**Langage serveur : PHP**

- Changer les limites suivantes dans `php.ini` (`C:\Program Files\ms4w\Apache\cgi-bin`) :

		>	post_max_size = 100M
		>	upload_max_filesize = 100M
		>	memory_limit = 128M

**Serveur Cartographique : MAPSERVER**

Dans certains cas il peut vous être nécessaire de monter en local un serveur cartographique contenant certaines de vos données.
Pour cela il faut:

- Copier vos données cartographiques dans le répertoire `gdaldata` de votre installation ms4w. Il est cependant recommandé de placer vos données dans un répertoire en dehors de ms4w et sauvegardé régulièrement afin d'éviter la perte de vos fichiers. Pour cela créer par exemple un répertoire `D:\DONNEES\Ms4w\gdaldata\data` dans lequel seront placées vos données. Puis placer un raccourci vers ce répertoire dans `C:\Program Files\ms4w\gdaldata`.
- Copier le Fichier `mapserver/data/ogam.map` dans `D:\DONNEES\Ms4w\gdaldata\mapfiles` et le modifier selon la configuration locale choisie.
- Dans le fichier `D:/DONNEES/Ms4w/httpd.d/httpd_ogam.conf` décommenter les lignes en dessous du commentaire `# Config for Mapserv` et les modifier selon la configuration locale choisie.

##2. Conteneur de servlets : TOMCAT

###Windows

- Télécharger et installer : [Tomcat](http://tomcat.apache.org/)
- Vérifier la présence ou ajouter les libs suivantes dans le répertoire `lib` de tomcat:

		>	postgresql-x.x-xxxx.jdbc4.jar
		>	mail.jar

##3. SGBD et extension cartographique : Postgresql / Postgis

###Windows

Prendre le package fourni par EDB ENTERPRISEDB : [pgdownload](http://www.enterprisedb.com/products-services-training/pgdownload#windows)

- Dans le `Stack Builder` cocher les lignes suivantes :

		>	Database Drivers\pgJDBC v9.3.xxxx-x
		>	Database Drivers\psqlODBC v09.03.xxxx-x
		>	Database Server\PostgreSQL v9.3.x-x
		>	Spatial Extensions\PostGIS 2.1 Bundle for PostgreSQL 9.3

###Linux
...

##4. Système d'exploitation :

###Windows

- Certain navigateur limitant par défaut à deux le nombre de requêtes simultanées sur un même sous-domaine, il est nécessaire de céer des alias pour contourner cette limitation.  
Dans `C:/Windows/system32/drivers/etc/hosts` ajouter les lignes : (x étant une adresse libre et différent de 1)  

		>	127.0.0.x ogam
		>	127.0.0.x www.ogam.fr
		>	127.0.0.x www-1.ogam.fr
		>	127.0.0.x www-2.ogam.fr
		>	127.0.0.x www-3.ogam.fr
		>	127.0.0.x www-4.ogam.fr
		>	127.0.0.x www-5.ogam.fr
		>	127.0.0.x www-6.ogam.fr
		>	127.0.0.x www-7.ogam.fr
		>	127.0.0.x www-8.ogam.fr
		>	127.0.0.x www-9.ogam.fr

###Linux
...

##5. IDE : Eclipse

- Installer le package suivant : [Eclipse IDE for Java and Report Developers](https://www.eclipse.org/downloads/packages/eclipse-ide-java-and-report-developers/lunasr1)  

		Description du package : 
		 
		Java EE tools and BIRT reporting tool for Java developers to create Java EE  
		and Web applications that also have reporting needs.  

		Ce package inclut :

		>	BIRT Framework
		>	Data Tools Platform
		>	Eclipse Java Development Tools
		>	Eclipse Java EE Developer Tools
		>	JavaScript Development Tools
		>	Mylyn Task List
		>	Remote System Explorer
		>	Eclipse XML Editors and Tools
	
- Ajouter ensuite les plugins suivants :  

		>	EPP PHP Feature by Eclipse.org in Yoxos Marketplace (include PDT and GIT)
		>	Subclipse 1.10.5 by Subclipse Project in Eclipse Marketplace (SVN plugin)
		>	Mongrel 1.0.0 by Jochen Wiedmann in Eclipse Marketplace (Tomcat plugin)
		>	Markdown Text Editor 1.1.0 by Winterwell Associates Ltd in Eclipse Marketplace

##6. Système de synchronisation de fichiers : GIT

- Télécharger et installer : [GIT](http://git-scm.com/downloads).
- Le livre [Pro Git](http://git-scm.com/book/fr), écrit par Scott Chacon et publié par Apress, est disponible entièrement au format PDF [ICI](http://geekographie.maieul.net/IMG/pdf/progit.fr.pdf).



