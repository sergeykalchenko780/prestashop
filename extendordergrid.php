<?php

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class ExtendOrderGrid extends \Module
{
    public const HOOKS = [
        'actionOrderGridDefinitionModifier',
        'actionOrderGridQueryBuilderModifier',
    ];

    public function __construct()
    {
        $this->name = 'extendordergrid';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Sergey K';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.8.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Extend Order Grid');
        $this->description = $this->l('Module for adding additional columns to order grid.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        return parent::install() && $this->registerHook(self::HOOKS);
    }

    public function hookActionOrderGridQueryBuilderModifier(array $params) : void
    {
        $service = $this->get('PrestaShop\Module\ExtendOrderGrid\QueryModifier');
        $service->addFieldsForOrderGrid($params);
    }

    public function hookActionOrderGridDefinitionModifier(array $params) : void
    {
        $service = $this->get('PrestaShop\Module\ExtendOrderGrid\GridModifier');
        $service->modifyGrid($params);
    }

    public function uninstall()
    {
        foreach (self::HOOKS as $hook) {
            $this->unregisterHook($hook);
        }

        return (
            parent::uninstall()
            && Configuration::deleteByName('MYMODULE_NAME')
        );
    }
}
