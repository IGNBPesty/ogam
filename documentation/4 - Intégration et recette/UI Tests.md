# Tests d'interface.

Choix d'un outil pour réaliser les tests d'interface de OGAM.

## Généralités

Cf [https://www.sencha.com/blog/automating-unit-tests/](https://www.sencha.com/blog/automating-unit-tests/).

3 types de tests pour le javascript:

### Tests de syntaxe 

Vérifier que le code javascript est correct. On peut utiliser [JSLint](http://www.jslint.com/) ou [JSHint](http://jshint.com/) pour ça.

### Tests unitaires

Comme les tests unitaires pour le PHP ou le Java, mais côté JS.
On devrait pouvoir tester les modèles et certaines fonctions du framework OGAM sans faire appel à l'interface complète.

### Tests d'interface

La partie la plus compliquée, on doit pouvoir tester qu'un site basé sur OGAM se comporte de la façon attendu.
Nécessite de créer des scénarios de test avec des données de test et de vérifier que ce qui est affiché correspond bien à ce que l'on veut. 

## Etude des outils existants (nov 2015)

Cf une première étude par Sylvain et Paul-Emmanuel :
[http://gitlab.dockerforge.ign.fr/ogam/ogam/blob/extjs5/documentation/1%20-%20Avant-Projet/Tools%20Selections.odt](http://gitlab.dockerforge.ign.fr/ogam/ogam/blob/extjs5/documentation/1%20-%20Avant-Projet/Tools%20Selections.odt).

### [Sikuli](http://www.sikuli.org/)

Projet un peu expérimental du MIT permettant de créer des scripts de test en utilisant des copies d'écran.
Utilise de la reconnaissance d'image pour cliquer au bon endroit et tester les résultats.

Le projet est toujours actif (v1.1.0 le 07/10/2015) mais ne semble pas très connu.

Il y a peut-être moyen d'intégrer Sikuli avec Sélénium. Cf [SikuliWebDriver](https://code.google.com/p/sikuli-api/wiki/SikuliWebDriver). Cela permettrait d'utiliser la reconnaissance d'image sur la partie carto par exemple, ou pour tester des points de détail de l'interface. 

### [Mocha](https://mochajs.org/)

Mocha est un framework de test javascript qui tourne avec NodeJS ou dans un browser.

### [Siesta](http://www.bryntum.com/products/siesta/)

Framework de tests unitaire.

Une version Lite qui permet de faire des tests dans le navigateur.
Une version Standard qui permet en plus d'automatiser les tests.

La version standard supporte SauceLabs, BrowerStack, PhantomJS et Selenium.
La version standard donne aussi une info de "coverage".

[http://www.bryntum.com/blog/testing-an-ext-js-6-app-generated-with-sencha-cmd/](http://www.bryntum.com/blog/testing-an-ext-js-6-app-generated-with-sencha-cmd/).

Il possède un outil d'enregistrement de scripts dans la version standard, comme Selenium.
[http://www.bryntum.com/blog/a-look-at-the-upcoming-siesta-recorder/](http://www.bryntum.com/blog/a-look-at-the-upcoming-siesta-recorder/).

### [Jasmine](http://jasmine.github.io/)

Framework de test javascript "behavior-driven", c'est à dire que les scripts de test sont sensés ressembler à du langage naturel. On décrit un comportement attendu par une fonction.

### [PhantomJS](http://phantomjs.org/)

PhantomJS est en fait un navigateur web basé sur webkit et "headless", donc lançable en ligne de commande ou depuis un script javascript.

Cela permet de scripter des tests d'interface et de les intégrer avec Jenkins par exemple.

### [SlimerJS](http://www.slimerjs.org/)

Navigateur "headless" mais basé sur Gecjko (Firefox). Similaire ) PhantomJS.

### [CasperJS](http://casperjs.org/)

Extension pour PhantomJS ou SlimerJS, permettant de créer des tests en javascript ou de scripter de la navigation.

### [SauceLabs](https://saucelabs.com/)

Service "cloud" permettant d'automatiser le lancement de tests unitaires sur plusieurs Navigateurs/Plateformes.

Supporte Selenium de base apparemment.
Supporte le javascrpt en général, donc les autres frameworks de test probablement aussi.

Payant (12$ par mois pour 2 VM et 1 user, 49 pour 3 VM, ....).


### [BrowserStack](https://www.browserstack.com/)

Un peu comme SauceLabs, permet de lancer des tests sur différents navigateurs en parallèle.

Basé sur Selenium.
Add-on existant pour Siesta.


### [JMeter](http://jmeter.apache.org/)

JMeter est un outil java permettant de rejouer des sessions HTTP enregistrées sous forme de scripts.

Il est plutôt orienté "tests de charge".

Déjà utilisé avec succès pour tester le site carto de l'inventaire forestier.

### [Badboy](http://www.badboy.com.au/)

Petit outil permettant d'enregister une session de navigation et de l'exporter sous forme de script JMeter.

Un peu comme le plugin de Selenium, mais permet d'enregistrer une session HTTPS.

### [Selenium](http://docs.seleniumhq.org/)    

Selenium est de loin l'outil de test javascript le plus connu.
Il se compose de :
* Un plugin Firefox qui permet d'enregistrer une session de navigation et de l'exporter sous forme de script dans plusieurs formats disponibles (Selenium, JMeter, Junit, ...)/
* Un "webdriver" qui permet de rejouer les scripts ou de télécommander un navigateur à distance.

Le problème pour utiliser Selenium, c'est que comme ExtJS générère automatiquement tout le code javascript, on ne peut pas se baser sur les identifiants des éléments générer pour créer des assertions (les IDs changent), et ExtJS a tendance à générer plus d'objets DOM (DIVs, ...) que nécessaire.

### [HTML5 Robot](http://html5robot.com/) 

Extension pour Selenium permettant de tester ExtJS.

Payant.



## Choix d'un outil

Dans tous les cas, il y a du code à écrire et à maintenir pour créer les scripts de test, ce qui représente le plus gros du travail.

Avant la mise à jour du framework ExtJS, il est recommandé d'avoir des tests unitaires en place :
[https://www.sencha.com/blog/upgrading-to-ext-js-5-tests-are-your-friend/](https://www.sencha.com/blog/upgrading-to-ext-js-5-tests-are-your-friend/).

A première vue (vision entièrement subjective de Benoit Pesty), Siesta et Selenium semblent être les 2 outils les plus "matures" pour faire des tests unitaires.


Jasmin est intéressant mais demande d'écrire beaucoup de code pour scripter les tests, les autres frameworks ont des outils d'enregistrement de script. Il est plus orienté "Test unitaire" que "Test d'interface".

Siesta semble être recommandé par Sencha pour ExtJS. Cf [https://www.sencha.com/blog/introducing-siesta-a-testing-tool-for-ext-js/](https://www.sencha.com/blog/introducing-siesta-a-testing-tool-for-ext-js/).

Selenium est le plus connu et le plus recommandé partout ailleurs.
Il y a aussi une entrée pour Selenium dans le blog de Sencha : [https://www.sencha.com/blog/testing-enterprise-applications-with-selenium-and-html5-robot/]
(https://www.sencha.com/blog/testing-enterprise-applications-with-selenium-and-html5-robot/).

Une autre entrée du blog Sencha qui évoque Siesta, Selenium et CasperJS :
[https://www.sencha.com/blog/ui-testing-a-sencha-app-3/](https://www.sencha.com/blog/ui-testing-a-sencha-app-3/)


En résumé, j'ai l'impression qu'il faudrait acheter une license pour Siesta.
Une petite vidéo : [https://vimeo.com/98664641](https://vimeo.com/98664641).
