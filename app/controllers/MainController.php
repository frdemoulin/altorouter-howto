<?php

class MainController
{
    // On stocke le router dans le contrôleur
    public $router;

    public function __construct($routerParam)
    {
        // on transmet le router à l'instanciation du contrôleur
        $this->router = $routerParam;     
    }

    public function home()
    {
        $this->show('home', [
            'title' => 'Vive la home !'
        ]);
    }

    public function legalMentions()
    {
        $this->show('legal_mentions', [
            'title' => 'Mentions légales'
        ]);
    }

    public function error404()
    {
        $this->show('error404', [
            'title' => 'Page non trouvée'
        ]);
    }

    public function show($viewName, $viewVars = array())
    {
        // $viewVars est disponible dans chaque fichier de vue
        $viewVars['baseUrl'] = $_SERVER['BASE_URI'];

        include(__DIR__.'/../views/header.tpl.php');
        include(__DIR__.'/../views/'.$viewName.'.tpl.php');
        include(__DIR__.'/../views/footer.tpl.php');
    }
}