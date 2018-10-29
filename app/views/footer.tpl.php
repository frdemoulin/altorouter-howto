<footer>
    <ul>
        <li><a href="<?= $this->router->generate('home'); ?>">Home</a></li>
        <li><a href="<?= $this->router->generate('legal-mentions'); ?>">Mentions légales</a></li>
        <li><a href="<?= $this->router->generate('product', array('id' => 1)); ?>">Produit 1</a></li>
        <li><a href="<?= $this->router->generate('product', array('id' => 2)); ?>">Produit 2</a></li>
        <li><a href="<?= $this->router->generate('error404'); ?>">Erreur 404</a></li>
    </ul>
</footer>
    
</body>
</html>