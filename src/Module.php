<?php
namespace mxdiModule;

class Module
{
    public function getConfig()
    {
        return (array)require __DIR__ . '/../config/module.config.php';
    }
}
