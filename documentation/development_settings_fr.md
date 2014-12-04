# Installation et configuration de l'environnement de développement

##1. Système d'exploitation :

###Windows

- Certain navigateur limitant par défaut à deux le nombre de requêtes simultanées sur un même sous-domaine, il est nécessaire de céer des alias pour contourner cette limitation.  
Dans `C:/Windows/system32/drivers/etc/hosts` ajouter les lignes : (x étant une adresse libre et différent de 1 (Par exemple pour une configuration vierge x = 2))  

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

##2. Environnement de Serveur web préconfiguré : MS4W/FGS

Note : Une liste de package Windows et Linux incluant Apache, PHP et MapServer sont disponibles [ici](http://mapserver.org/fr/download.html).

###Windows

- Télécharger : [MS4W](http://www.maptools.org/ms4w/index.phtml?page=downloads.html)
- L'installer par exemple dans `C:\Program Files\ms4w` et non dans `C:\Program Files (x86)\ms4w` (Pour ne pas avoir à changer tous les chemins dans les fichiers de configuration par défaut).

###Linux

- Télécharger et installer : [FGS](http://www.maptools.org/fgs/)

**Serveur HTTP : Apache**

- Lancer le programme `Démarrer\Programmes\MS4w\Apache\MS4W-Apache-Monitor` et arrêter l'instance d'Apache en cours.
**Note :** Dans certain cas (selon votre profil utilisateur sous Windows) il peut être nécessaire de copier les fichiers de configuration sur votre bureau afin de pouvoir enregistrer les modifications et ensuite de les replacer dans leur répertoire d'origine (Messages d'erreur : Fichier en cours d'utilisation par un autre programme ou accès au fichier refusé...). Une autre solution consiste à ouvrir votre éditeur de texte en mode "Administrateur".
- Dans le fichier `C:\Program Files\ms4w\Apache\conf\httpd.conf` d'Apache, déplacer la partie `<IfModule alias_module> ... </IfModule>` à la fin du fichier.  
**Note :** Ceci n'est pas obligatoire mais évite des warnings lors des tests de syntaxe. 
(Problème d'imbrication de SrciptAlias dans le répertoire cgi-bin)
- Dans le `httpd.conf` d'Apache, Il est recommandé de changer le path des fichiers httpd_*.conf (en dessous de '# parse MS4W apache conf files') pour un répertoire en dehors 
de ms4w et sauvegardé régulièrement afin d'éviter la perte de vos fichiers de configuration.  
**Exemple :** include `D:\DONNEES\Ms4w\httpd.d\httpd_*.conf`
- Dans le `httpd.conf` d'Apache, décommenter la ligne "LoadModule rewrite_module modules/mod_rewrite.so".
- Dans le `httpd.conf` d'Apache, décommenter la ligne "LoadModule expires_module modules/mod_expires.so".
- Copier `D:\DONNEES\Workspace\OGAM\website\config\httpd_ogam.conf` dans votre répertoire httpd.d (adapter les répertoires)
- Vérifiez votre configuration Apache (Dans le menu "Démarrer" de windows lancer la commande "cmd". Puis se placer dans le répertoire "bin" de Apache (`C:\Program Files\ms4w\Apache\bin`). Lancer ensuite la commande `httpd -t`).
- Redémarrer le serveur Apache via le moniteur.

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

##3. Conteneur de servlets : TOMCAT

###Windows

- Télécharger et installer : [Tomcat](http://tomcat.apache.org/) (Prendre de préférence le "Windows Service Installer")
- Vérifier la présence ou ajouter les libs suivantes dans le répertoire `lib` de tomcat:

		>	postgresql-9.3-xxxx.jdbc4.jar (libs_java\import\org\postgresql\)
		>	mail.jar (libs_java\import\com\sun\mail\)

##4. SGBD et extension cartographique : Postgresql / Postgis

###Windows

Prendre le package fourni par EDB ENTERPRISEDB : [pgdownload](http://www.enterprisedb.com/products-services-training/pgdownload#windows)

- Laisser les chemins par défaut (C:\Programmes Files\PostgreSQL\9.3...)
- Laisser le port par défaut 5432 si celui est libre (non utilisé par une précédent installation).
- Dans le `Stack Builder` sélectionner votre installation PostgreSQL 9.3.
- Dans le `Stack Builder` cocher les lignes suivantes et lancer l'installation :

		>	Database Drivers\pgJDBC v9.3.xxxx-x
		>	Database Drivers\psqlODBC v09.03.xxxx-x
		>	Database Server\PostgreSQL v9.3.x-x
		>	Spatial Extensions\PostGIS 2.1 Bundle for PostgreSQL 9.3

- Procéder à l'installation des deux drivers en laissant les paramètres par défaut.
- Dans l'installation de postgis cocher la ligne "Create spatial database" (Cette database servira de templette) et valider la création des variables d'environnement.
- Procéder à l'installation de PostgreSQL en laissant les paramètres par défaut.
- Redémarrer votre système.

###Linux
...

##5. IDE : Eclipse

- Installer dans `C:\Eclipse-OGAM-luna-R-win32-x86_64` le package suivant : [Eclipse IDE for Java and Report Developers](https://www.eclipse.org/downloads/packages/eclipse-ide-java-and-report-developers/lunasr1)  

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
	
- Ouvrir Eclipse en executant le fichier `C:\Eclipse-OGAM-luna-R-win32-x86_64\eclipse.exe`.
- Renseigner le workspace (Exemple: `D:\DONNEES\Workspace`).
- Ouvrir le menu `Help\Eclipse Marketplace...`
- Ajouter ensuite dans **L'ORDRE** les plugins suivants et redémarrer Eclipse à chaque installation :

		> Dans Yoxos Marketplace (Logo orange) : 
			>  Plugin PDT et GIT : EPP PHP Feature by Eclipse.org

		> Dans Eclipse Marketplace (Logo violet) :
			>  Plugin SVN : Subclipse 1.10.5 by Subclipse Project
			>  Plugin Tomcat : Mongrel 1.0.0 by Jochen Wiedmann
			>  Plugin Markdown : Markdown Text Editor 1.1.0 by Winterwell Associates Ltd

##6. Système de synchronisation de fichiers : GIT

- Télécharger et installer : [GIT](http://git-scm.com/downloads).
- Laisser la configuration par défaut et cocher la case 'Use a TrueType font in all console (not only for Git bash)'.
- Le livre [Pro Git](http://git-scm.com/book/fr), écrit par Scott Chacon et publié par Apress, est disponible entièrement au format PDF [ICI](http://geekographie.maieul.net/IMG/pdf/progit.fr.pdf).

##7. Système de build : Sencha Cmd
...
D:\DONNEES\Workspace\OGAM\website\htdocs\client\ogamDesktop>sencha app build -c -e development
D:\DONNEES\Workspace\OGAM\website\htdocs\client\ogamDesktop>sencha app watch -e development
Mise à jour de la commande : 
sencha upgrade --check
sencha upgrade --check --beta
Mise à jour d'une application et de son workspace :
D:\DONNEES\Workspace\OGAM\website\htdocs\client\ogamDesktop>sencha app upgrade -noframework
Mise à jour d'un pachage :
D:\DONNEES\Workspace\OGAM\website\htdocs\client\packages\ogamDesktop-theme\sencha package upgrade




