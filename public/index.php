<?php

/*
****************************************
I. INSTALLATION
****************************************
*/

/**
 * 1. Installation de composer (cf fiche recap)
 * 2. Installation d'AltoRouter : composer require altorouter/altorouter (dans le dossier de travail)
 * https://packagist.org/packages/altorouter/altorouter
 * 3. Installation de var_dumper
 * composer require symfony/var-dumper (dans le dossier de travail)
 * https://packagist.org/packages/symfony/var-dumper
 */

// inclusion de l'autoload de Composer
require '../vendor/autoload.php';

// inclusion des controllers
require __DIR__ . '/../app/controllers/MainController.php';
require __DIR__ . '/../app/controllers/CatalogController.php';

// Instanciation d'AltoRouter
// pas de require de la classe AltoRouter (pris en charge par l'autoload de Composer)
/** La classe AltoRouter possède comme attributs :
 * routes: []
 * namedRoutes: []
 * basePath: ""
 * matchTypes: array:6 [regex]
*/

$router = new AltoRouter();

/*
*******************************************
II. DEFINITION DU CHEMIN DE BASE (BASE_URI)
*******************************************
*/

/**
 * 1. Définition du chemin de base (basePath d'AltoRouter)
 * doc : http://altorouter.com/usage/rewrite-requests.html
 * Optionally, if your project lives in a sub-folder of your web root you use the setBasePath() method to set a base path.
 * depuis http://localhost jusqu'à public
 * exemple d'URL : http://localhost//revisions/poo-php/routing/public/
 * donne "BASE_URI" : /revisions/poo-php/routing/public
 * ATTENTION : SANS SLASH FINAL dans le chemin précédent
*/

$baseUrl = isset($_SERVER['BASE_URI']) ? $_SERVER['BASE_URI'] : '';

/** 2. Création d'un fichier .htaccess
 * cette url est transmise depuis le .htaccess vers la variable d'environnement $_SERVER de PHP
 * "BASE_URI" => "/revisions/poo-php/routing/public"
 * on rend dynamique l'url de base quelque soit le chemin du projet
*/

/** 3. Appel de la méthode setBasePath() sur l'objet router
 * on stocke $baseUrl dans l'attribut basePath de la classe AltoRouter via son setter setBasePath(), d'où la correspondance :
 * basePath: "/revisions/poo-php/routing/public"
*/

$router->setBasePath($baseUrl);
//dump($router);
/**
 * --EXEMPLE-- dump de $router :
 * #routes: []
 * #namedRoutes: []
 * #basePath: "/revisions/poo-php/routing/public"
 * #matchTypes: array:6 [▼
 *     regex
 * ]
 */

/*
**********************************************
III. MAPPING DES ROUTES avec la méthode map()
**********************************************
*/

/**
 * On définit des routes de redirection à l'aide de la méthode map() d'AltoRouter
 * doc : http://altorouter.com/usage/mapping-routes.html
 * paramètre 1 : méthode HTTP (GET / POST / ...)
 * paramètre 2 : pattern de la route, c'est la partie de l'url qui va être réécrite
 * paramètre 3 : cible de la route (target), de la forme Controller#method
 * on demande à AltoRouter d'associer cette cible à la méthode HTTP et au pattern de la route
 * on instancie le contrôleur Controller et on appelle sa méthode method()
 * paramètre 4 : nom de la route à titre de référence ultérieure
*/

$router->map('GET', '/', 'MainController#home', 'home');
//dump($router);
/**
 * --EXEMPLE-- dump de $router :
 * #routes: array:1 [▼
 *     0 => array:4 [▼
 *       0 => "GET"
 *       1 => "/"
 *       2 => "MainController#home"
 *       3 => "home"
 *     ]
 * ]
 * #namedRoutes: array:1 [▼
 *     "home" => "/"
 *   ]
 * #basePath: "/revisions/poo-php/routing/public"
 * #matchTypes: array:6 [▼
 *     regex
 * ]
 */

$router->map('GET', '/mentions-legales', 'MainController#legalMentions', 'legal-mentions');
$router->map('GET', '/catalogue/produit/[i:id]', 'CatalogController#product', 'product');
$router->map('GET', '/erreur-404', 'MainController#error404', 'error404');

/*
*********************************************
IV. MATCHING REQUESTS avec la méthode match()
*********************************************
*/

/**
 * On saisit une URL dans la barre d'adresse du navigateur
 * On demande alors à AltoRouter de vérifier si cette URL est le pattern d'une route existante dans le mapping (paramètre 2 de la méthode map)
 * Autrement dit, on cherche à savoir s'il y a "match" (on dit que l'on fait une "matching request")
 * /!\ la méthode match() s'appelle sans paramètre
 * doc : http://altorouter.com/usage/matching-requests.html
 */

$match = $router->match();

/**
 * RETOUR de la méthode match()
 * si pas de match, $match == false
 * si match entre URL saisie et pattern dans le mapping, $match == true
 * La méthode match() retourne alors un array associatif contenant 3 clés :
 * clé 'target' (mixed) : cible de la route (param. 3 de la méthode map() : Controller#method)
 * clé 'params' (array) : paramètres se trouvant dans l'url
 * clé 'name' (string) : nom de la route (param. 4 de la méthode map())
*/

//dump($match);
/**
 * --EXEMPLE-- dump de $match dans le cas de la home (on saisit / dans l'URL)
 * array:3 [▼
 * "target" => "MainController#home"
 * "params" => []
 * "name" => "home"
 * ]
 */

/*
*********************************************
V. REQUESTS PROCESSING avec un dispatcher
*********************************************
*/

/**
 * Le routing fixe les directions à suivre, puis le dispatcher les suit en pratique en générant les pages demandées
 * 
 * si pas de match, $match == false (on renvoie vers une 404)
 * sinon, dynamiquement on instancie le controller et on appelle la méthode associée (paramètre 3 de la méthode map())
 */

if ($match) {
    /**
     * on récupère les données du dispatcher contenues dans le tableau $match à la clé target, il s'agit de Controller#method
     * on sépare la chaîne de part et d'autre de # avec la fonction explode()
     * lien doc php explode : http://php.net/manual/fr/function.explode.php
     * on stocke alors les infos dans un tableau indexé $dispatcherInfos
     * index 0 : Controller
     * index 1 : method 
     */

    $dispatcherInfos = explode('#', $match['target']);
    
    // on récupère le contrôleur à l'index 0
    // $controllerName est une string
    $controllerName = $dispatcherInfos[0];

    // on récupère la méthode à l'index 1
    // $methodName est une string
    $methodName = $dispatcherInfos[1];
    //dump($dispatcherInfos);
    /**
     * --EXEMPLE-- dump de $dispatcherInfos dans le cas de la home
     * array:2 [▼
     * 0 => "MainController"
     * 1 => "home"
     * ]
     */

    // on instancie le contrôleur en dynamique
    // php remplace le contenu de $controllerName par sa valeur
    // http://php.net/manual/fr/language.variables.variable.php
    // on passe le router au contrôleur via son constructeur
    $controller = new $controllerName($router); // par ex. MainController

    // on appelle la méthode $methodName selon le même concept
    // $match['params'] contient les paramètres d'URL (exemple "id" => "2" dans le cas d'un paramètre dynamique [i:id])
    // on convertit une chaîne de caractère en méthode
    // on les transmet à la méthode
    $controller->$methodName($match['params']); // par ex. home
    //dump($controller);
} else {
    // page non trouvée 404
    // on modifie l'entête de réponse pour avoir un statut 404
    header("HTTP/1.0 404 Not Found");

    $controller = new MainController($router);
    $controller->error404();
}

/*
****************************************************
VI. GENERATION DES ROUTES avec la méthode generate()
****************************************************
*/

/**
 * Pour générer une URL, on utilise la méthode generate() d'AltoRouter
 * elle prend deux arguments :
 * argument 1 : nom de la route (paramètre 4 de la méthode map())
 * argument 2 (optionnel) : array associatif des paramètres de l'URL
 *  clé 'xx' (venant de [i:xx]) => valeur
 * exemple : page produit d'id 1
 * $router->generate('product', array('id' => 1));
 */