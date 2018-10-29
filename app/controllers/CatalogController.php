<?php

class CatalogController
{
    // On stocke le router dans le contrôleur
    public $router;

    public function __construct($routerParam)
    {
        // on transmet le router à l'instanciation du contrôleur
        $this->router = $routerParam;     
    }

    public function product($params = [])
    {
        // on récupère les infos du produit
        $this->show('product', [
            'title' => 'Page du produit ' . $params['id'],
            'id' => $params['id']
        ]);
        dump($params);
    }

    protected function show($viewName, $viewVars = array())
    {
        // $viewVars est disponible dans chaque fichier de vue
        $viewVars['baseUrl'] = $_SERVER['BASE_URI'];
        
        include(__DIR__ . '/../views/header.tpl.php');
        include(__DIR__ . '/../views/' . $viewName . '.tpl.php');
        include(__DIR__ . '/../views/footer.tpl.php');
    }
}