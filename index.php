<?php
define('ROOT', __DIR__);
require_once './vendor/autoload.php';

$tplDir   = ROOT.'/resources/views';
$transDir = ROOT.'/resources/translations';
$domain   = 'messages';
$tmpDir   = "/tmp/cache/{$domain}/";

$loader = new Twig_Loader_Filesystem($tplDir);

$twig = new Twig_Environment($loader, [
	'cache'       => $tmpDir,
	'auto_reload' => true
]);

//http://twig.sensiolabs.org/doc/extensions/i18n.html
//$twig->addExtension(new Twig_Extensions_Extension_I18n());

// http://twig.sensiolabs.org/doc/extensions/intl.html
$twig->addExtension(new Twig_Extensions_Extension_Intl());

$supportedLang = ['de_DE', 'en_US', 'en_GB', 'ru_RU'];

// Set language
$locale = isset($_GET['lang']) && in_array($_GET['lang'], $supportedLang) ? $_GET['lang'] : 'en_US';

// Set language
Locale::setDefault($locale);

// iterate over all your templates
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tplDir), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
	// force compilation
	if ($file->isFile()) {
		$twig->loadTemplate(str_replace($tplDir.'/', '', $file));
	}
}

// http://symfony.com/doc/current/book/translation.html
// http://symfony.com/doc/current/components/translation/introduction.html#constructing-the-translator
$translate = new \Symfony\Component\Translation\Translator($locale);
$translate->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
$translate->addResource('php', "{$transDir}/{$domain}.{$locale}.php", $locale);

//$messages = $translate->getMessages();

$content = [
	'address_name' => $translate->trans('company.address.name'),
	'address1'     => $translate->trans('company.address1'),
	'city'         => $translate->trans('company.city'),
	'state'        => $translate->trans('company.state'),
	'zip'          => $translate->trans('company.zip'),
	'welcome'      => $translate->trans('Hello %your_name%', ['%your_name%' => 'Aaron']),
	'text'         => 'Hello World!',
	'amount'       => 20,
	'date'         => new DateTime(),
	'todays_date'  => $translate->trans('todays_date'),
	'were_paid'    => $translate->trans('were_paid')

];

echo $twig->render('page.twig', $content);