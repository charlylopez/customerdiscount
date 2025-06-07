<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class customerdiscount extends Module
{
	const ENABLE_MODULE = 1;

	public $name;
    public $version;
    public $author;
    public $need_instance;
    public $bootstrap;
    public $displayName;
    public $description;
    public $logo_path;
    public $module_path;
    public $confirmUninstall;
    public $ps_url;

	public function __construct()
	{
		$this->name = 'customerdiscount';
        $this->tab = 'pricing_promotion';
        $this->version = '1.0.0';
        $this->author = 'Carlos Lopez';
        $this->need_instance = false;
        $this->bootstrap = true;
        parent::__construct();

        if ($this->context->link == null) {
            $protocolPrefix = Tools::getCurrentUrlProtocolPrefix();
            $this->context->link = new Link($protocolPrefix, $protocolPrefix);
        }

        $this->displayName = 'Descuentos Personalizados por Usuario';
        $this->description = 'Permite asignar descuentos especificos a usuarios individuales.';

        if (!$this->_path) {
            $this->_path = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        }

	    $this->confirmUninstall = 'Seguro que desea desinstalar este modulo?';
        $this->ps_url = $this->context->link->getBaseLink();
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
	}

	public function install()
	{
		// Sql
		$sqlQuery = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'customerdiscount` (
            `id_customerdiscount` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_customer` int(10) unsigned NOT NULL,
            `id_category` int(10) unsigned NOT NULL,
            `title` varchar(255) NOT NULL,
            `discount` decimal(20,6) NOT NULL,
            `discount_type` tinyint(1) NOT NULL DEFAULT "0",
            `date_add` datetime NOT NULL,
            `date_upd` datetime NULL,
            PRIMARY KEY (`id_customerdiscount`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';

        if (Db::getInstance()->execute($sqlQuery) == false) {
            return false;
        }

        // Configuration
        Configuration::updateValue('CUSTOMERDISCOUNT_ENABLE_MODULE', self::ENABLE_MODULE);

        // Hooks
        if (parent::install() &&
            $this->registerHook('actionProductPriceCalculation')
        ) {
            return true;
        }

        $this->_errors[] = 'Hubo un error durante la instalacion.';

        return false;
	}

	public function uninstall()
	{
		// SQL
        $sqlQuery = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'customerdiscount`';
        if (Db::getInstance()->execute($sqlQuery) == false) {
            return false;
        }

        // Configuration
        Configuration::deleteByName('CUSTOMERDISCOUNT_ENABLE_MODULE');

        if (parent::uninstall()) {
            return true;
        }

        $this->_errors[] = 'Hubo un error durante la desinstalacion.';

        return false;
	}

	public function getContent()
	{
        $this->context->controller->addCSS($this->_path . 'views/css/back.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/back.js');

        $idLang = $this->context->language->id;
		$output = '';

		if (Tools::isSubmit('submit-' . $this->name)) {
            $enableModule = 0;
            if (Tools::isSubmit('enable-module')) {
                $enableModule = 1;
            }

	       Configuration::updateValue('CUSTOMERDISCOUNT_ENABLE_MODULE', $enableModule);
	       $output = $this->displayConfirmation($this->l('Configuracion actualizada'));
	    }

        if (Tools::isSubmit('submit-discountform')) {
            $discountForm = (array) Tools::getValue('discountform');
            foreach ($discountForm as $key => $value) {
                $discountForm[$key] = htmlspecialchars(trim($value));
            }
            
            if (empty($discountForm['id_customerdiscount'])) {
                if ($this->create($discountForm)) {
                    $output = $this->displayConfirmation($this->l('Se creo el descuento correctamente'));
                }
            } elseif (is_numeric($discountForm['id_customerdiscount']) && !empty($discountForm['id_customerdiscount'])) {
                if ($this->edit($discountForm)) {
                    $output = $this->displayConfirmation($this->l('Se actualizo el descuento correctamente'));
                }
            } else {
                $output = $this->displayError($this->l('Invalid request'));
            }
        }

		$this->context->smarty->assign([
            'enable_module' => (int) Configuration::get('CUSTOMERDISCOUNT_ENABLE_MODULE'),
            'moduleName' => $this->name,
            'customers' => Customer::getCustomers(),
            'categories' => Category::getAllCategoriesName(null, $idLang),
            'allDiscounts' => $this->getAllDiscounts($idLang),
        ]);

        return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
	}

    private function getAllDiscounts($idLang)
    {
        $sqlQuery = 'SELECT
                      cd.`id_customerdiscount` AS `id`,
                      cd.`id_customer`,
                      cd.`id_category`,
                      cd.`title`,
                      cd.`discount`,
                      cd.`discount_type`,
                      cu.`firstname`,
                      cu.`lastname`,
                      cu.`email`,
                      cl.`name` AS `category`
                    FROM `' . _DB_PREFIX_ . 'customerdiscount` cd
                    LEFT JOIN `' . _DB_PREFIX_ . 'customer` cu ON (cd.`id_customer` = cu.`id_customer`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'category` ca ON (cd.`id_category` = ca.`id_category`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (ca.`id_category` = cl.`id_category`)
                    WHERE
                        cl.`id_lang` = ' . (int) $idLang . '
                    ORDER BY cd.`date_add` ASC';

        $dbResult = Db::getInstance()->executeS($sqlQuery);

        $result = [];
        foreach ($dbResult as $key => $value) {
            $result[$key]['id'] = $value['id'];
            $result[$key]['id_customer'] = $value['id_customer'];
            $result[$key]['id_category'] = $value['id_category'];
            $result[$key]['title'] = $value['title'];
            $result[$key]['discount'] = round($value['discount'], 2);
            $formatDiscount = number_format($value['discount'], 2, '.', '');
            $result[$key]['discount_format'] = ($value['discount_type'] == 0) ? '$ '. $formatDiscount : $formatDiscount . ' %';
            $result[$key]['discount_type'] = $value['discount_type'];
            $result[$key]['firstname'] = $value['firstname'];
            $result[$key]['lastname'] = $value['lastname'];
            $result[$key]['email'] = $value['email'];
            $result[$key]['category'] = $value['category'];
        }

        return $result;
    }

    private function create($values)
    {
        $sqlQuery = 'INSERT INTO ' . _DB_PREFIX_ . 'customerdiscount (id_customer, id_category, title, discount, discount_type, date_add) VALUES '
            . "(" . $values['id_customer'] . ", " . $values['id_category'] . ", '" . $values['title'] . "', " . $values['discount'] . ", " . $values['discount_type'] . ", now())";
        if (Db::getInstance()->execute($sqlQuery) == false) {
            return false;
        }
        return true;
    }

    private function edit($values)
    {
        $sqlQuery = 'UPDATE ' . _DB_PREFIX_ . "customerdiscount SET id_customer='" . $values['id_customer'] . "', id_category='" . $values['id_category'] . "', title='" . $values['title'] . "', discount='" . $values['discount'] . "', discount_type='" . $values['discount_type'] . "', date_upd=now() WHERE id_customerdiscount=" . $values['id_customerdiscount'];
        if (Db::getInstance()->execute($sqlQuery) == false) {
            return false;
        }
        return true;
    }

    public function hookActionProductPriceCalculation($params)
    {
    	if (Configuration::get('CUSTOMERDISCOUNT_ENABLE_MODULE') == 1 && !empty($params['id_customer'])) {
            $id_customer = $params['id_customer'];

            $sqlQuery = 'SELECT
                      cd.`id_customerdiscount` AS `id`,
                      cd.`id_customer`,
                      cd.`id_category`,
                      cd.`title`,
                      cd.`discount`,
                      cd.`discount_type`
                    FROM `' . _DB_PREFIX_ . 'customerdiscount` cd
                    WHERE
                        cd.`id_customer` = ' . (int) $params['id_customer'] . '
                    LIMIT 1';

            $dbResult = Db::getInstance()->executeS($sqlQuery);

            $id_category = 0;
            $discount = 0;
            $discount_type = 0;
            foreach ($dbResult as $key => $value) {
                $id_category = $value['id_category'];
                $discount = $value['discount'];
                $discount_type = $value['discount_type'];
            }

            if ($id_category > 1) {
                $categories[] = array('id_category' => $id_category);
                if (!Product::idIsOnCategoryId($params['id_product'], $categories)) {
                    return false;
                }
            }

            if ($discount_type == 0) {
                $newPrice = round($params['price'] - $discount, 2);
            } else {
                $newPrice = round($params['price'] - ($params['price'] * ($discount / 100)), 2);
            }

            $params['price'] = $newPrice;
        }
    }

}