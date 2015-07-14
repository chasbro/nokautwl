<?php
namespace NokautWL\Admin;

use NokautWL\ApiKitFactory;
use NokautWL\View\Products\ShortCode\ProductsBox;

class Admin
{
    const NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY = 'nokautwl-config';

    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::initHooks();
        }
    }

    public static function initHooks()
    {
        self::$initiated = true;

        add_action('admin_init', array(__CLASS__, 'adminInit'));
        add_action('admin_menu', array(__CLASS__, 'adminMenu'), 1);

        add_action('wp_ajax_category_search', array(__CLASS__, 'ajaxCategorySearchCallback'));
        add_action('wp_ajax_categories_get_by_ids', array(__CLASS__, 'ajaxCategoriesGetByIdsCallback'));


        wp_register_style('select2.css', NOKAUTWL_PLUGIN_URL . 'public/vendor/select2/select2.css', array(), '3.4.8');
        wp_enqueue_style('select2.css');

        wp_register_script('select2.js', NOKAUTWL_PLUGIN_URL . 'public/vendor/select2/select2.js', array('jquery'), '3.4.8');
        wp_enqueue_script('select2.js');

        wp_register_script('nokaut-wl-admin.js', NOKAUTWL_PLUGIN_URL . 'public/js/nokaut-wl-admin.js', array('jquery'), NOKAUTWL_VERSION);
        wp_localize_script('nokaut-wl-admin.js', 'ajax_object',
            array('ajax_url' => admin_url('admin-ajax.php'), 'we_value' => 1234));
        wp_enqueue_script('nokaut-wl-admin.js');
    }

    public static function adminInit()
    {
        Options::init();
    }

    public static function adminMenu()
    {
        $hook = add_options_page(__('NokautWL', 'NokautWL'), __('NokautWL', 'NokautWL'), 'manage_options', self::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY, array(__CLASS__, 'displayPage'));

        // top right corner help tabs
        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
            add_action("load-$hook", array(__CLASS__, 'adminHelp'));
        }
    }

    public static function displayPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        echo '<div class="wrap">';
        echo '<h2>NokautWL - konfiguracja wtyczki porównywarki cen Nokaut.pl</h2>';
        Options::form();
        echo '</div>';
    }

    public static function ajaxCategorySearchCallback()
    {
        $term = trim($_POST['term']);
        $data = array();

        $apiKit = ApiKitFactory::getApiKit();
        $categoriesRepository = $apiKit->getCategoriesRepository();
        $categories = $categoriesRepository->fetchHeaderCategoriesByTitlePhrase($term);

        foreach ($categories as $category) {
            $data[] = array(
                'id' => $category->getId() . ':' . $category->getUrl(),
                'title' => $category->getTitle()
            );
        }

        echo json_encode($data);
        die();
    }

    public static function ajaxCategoriesGetByIdsCallback()
    {
        $categories = $_POST['categories'];
        $data = array();

        $apiKit = ApiKitFactory::getApiKit();
        $categoriesRepository = $apiKit->getCategoriesRepository();

        foreach ($categories as $categoryId) {
            list($categoryId) = explode(":", $categoryId);
            if (!$categoryId) {
                continue;
            }
            $category = $categoriesRepository->fetchHeaderCategoryById($categoryId);
            $data[] = array(
                'id' => $category->getId() . ':' . $category->getUrl(),
                'title' => $category->getTitle()
            );
        }

        echo json_encode($data);
        die();
    }

    /**
     * Add help to the NokautWL page
     *
     * @return false if not the NokautWL page
     */
    public static function adminHelp()
    {
        $current_screen = get_current_screen();

        $current_screen->add_help_tab(
            array(
                'id' => 'overview',
                'title' => "Wprowadzenie",
                'content' =>
                    '<p><strong>NokautWL - wtyczka porównywarki cen Nokaut.pl</strong></p>
                    <ul>Wtyczka umożliwia integrację funkcjonalności porównywarki cen z Twoim blogiem za pomocą:
                        <li>strony produktów kategorii,</li>
                        <li>strony produktu,</li>
                        <li>elementów umieszczanych w treści postów (short tags), plikach szablonów wordpress (metody).</li>
                    </ul>
                    <p>Wygląd poszczególnych elementów można całkowicie zmieniać i dostosowywać do swoich potrzeb, startowa wizualizacja jest tylko przykładem wykorzystania dostępnych danych.</p>'
            )
        );

        $current_screen->add_help_tab(
            array(
                'id' => 'configuration',
                'title' => 'Konfiguracja',
                'content' =>
                    '<p><strong>NokautWL - konfiguracja wtyczki</strong></p>
                    <p>Aby uzyskać dostęp do Nokaut Search API, skontaktuj się z Nokaut.pl. Jeśli posiadasz już klucz dostępowy do Nokaut Search API, skonfiguruj wtyczkę.</p>
                    <ul>API URL:
                    <li>http://92.43.117.190:8080/api/v2/ - serwer produkcyjny</li>
                    <li>http://92.43.117.190:8088/api/v2/ - serwer testowy</li>
                    </ul>
                    ',
            )
        );

        $current_screen->add_help_tab(
            array(
                'id' => 'features',
                'title' => 'Integracja z blogiem',
                'content' =>
                    '<p><strong>NokautWL - integracja elemntów wtyczki z blogiem</strong></p>
                    <h3>ShortTag - link tekstowy do produktu [nokautwl-product-text-link]</h3>
                    <p>Generowany jest link tekstowy, treścią jest przekazany tekst lub tytuł produktu.
                    <ul>Opcje:
                    <li>url - adres produktu na Twoim blogu, dokładnie taki do jakiego można dojść w blogu, bez domeny, ze znakiem / na początku</li>
                    <li>tooltip - adres produktu na Twoim blogu, przyjmowane wartości (domyślnie: simple):
                        <ul>
                        <li>off - wyłączony tooltip, nad linkiem pojawi się standardowy title linka</li>
                        <li>simple - włączony standardowy, prosty tooltip</li>
                        <li>advanced - włączony rozbudowany tooltip z elementami html, ze zdjęciem, cenami</li>
                        <li>nazwa szablonu - włączony rozbudowany tooltip z zawartością wygenerowaną z podanego szablonu</li>
                        </ul>
                    </li>
                    </ul>
                    <ul>Przykłady użycia:
                    <li>[nokautwl-product-text-link url=\'/product/gry-psp/sony-eyepet.html\' type=\'off\'/]</li>
                    <li>[nokautwl-product-text-link url=\'/product/gry-psp/sony-eyepet.html\']zobacz nowy produkt![/nokautwl-product-text-link]</li>
                    <li>[nokautwl-product-text-link url=\'/product/gry-psp/sony-eyepet.html\' type=\'product/short-code/text-link/advanced.twig\'/]</li>
                    </ul>
                    </p>

                    <h3>ShortTag - box jednego produktu [nokautwl-product-box]</h3>
                    <p>Generowany jest box produktu.
                    <ul>Opcje:
                    <li>url - adres produktu na Twoim blogu, dokładnie taki do jakiego można dojść w blogu, bez domeny, ze znakiem / na początku</li>
                    </ul>
                    <ul>Przykłady użycia:
                    <li>[nokautwl-product-box url=\'/product/gry-psp/sony-eyepet.html\'/]</li>
                    </ul>
                    </p>

                    <h3>ShortTag - boxy wielu produktów [nokautwl-products-box]</h3>
                    <p>Generowany są boksy wielu produktów.
                    <ul>Opcje:
                    <li>url - adres produktów na Twoim blogu, dokładnie taki do jakiego można dojść w blogu, bez domeny, ze znakiem / na początku</li>
                    <li>limit - maksymalna ilość wyświetlanych produktów (domyślna ilość produktów: ' . ProductsBox::DEFAULT_PRODUCTS_LIMIT . ')</li>
                    <li>columns - ilość kolumn w wyświetlanych wyników (możliwe wartości: 1,2,3,4,6,12, domyślna wartość: 2)</li>
                    </ul>
                    <ul>Przykłady użycia:
                    <li>[nokautwl-products-box url=\'/category/opony/opony-zimowe/products/poziom-emisji-halasu:77.html\'/]</li>
                    <li>[nokautwl-products-box url=\'/category/opony/opony-zimowe/products/poziom-emisji-halasu:77.html\' limit=\'4\'/]</li>
                    <li>' . htmlentities('<?php echo \NokautWL\View\Products\ShortCode\ProductsBox::render($url,$limit,$columns); ?>') . ' - bezpośrednie wywołanie w pliku szablonu wordpress\'a,
                    np. w szablonie kategorii (w tym przypadku nie przekazujemy url, ustawiamy jako null, zostanie on automatycznie pobrany z bieżącej kategorii).
                    </ul>
                    </p>
                    '
            )
        );

        $current_screen->add_help_tab(
            array(
                'id' => 'customize',
                'title' => 'Dostosowywanie wyglądu',
                'content' =>
                    '<p><strong>NokautWL - dostosowanie wyglądu elementów wtyczki</strong></p>
                    <p>Wtyczka pozwala na dostosowanie wygladu każdego elementu do swoich potrzeb.<p>
                    <p>Wtyczka domyślnie wykorzystuje system szablonów <a href="http://twig.sensiolabs.org/" target="_blank">Twig</a>
                    oraz framework <a href="http:/getbootstrap.com/" target="_blank">Bootstrap</a> </p>
                    <p>Nie należy modyfikować kodu wtyczki, gdyż uniemożliwi to bezproblemowe wykonywanie jej aktualizacji
                    w przyszłości. Należy skopiować odpowienie pliki z wtyczki (wp-content/plugins/nokaut-wl/templates/)
                    do utworzonego katalogu aktywnego motywu (wp-content/themes/AKTYWNY_MOTYW/nokaut-wl/templates/) Wordpress,
                    dopiero skopiowane pliki szablonów są bazą do indywidualnych zmian.</p>
                    <ul>Kolejność wczytywania plików szablonów wtyczki:
                    <li>wp-content/themes/AKTYWNY_MOTYW/nokaut-wl/templates/</li>
                    <li>wp-content/plugins/nokaut-wl/templates/</li>
                    </ul>

                    <ul>Kolejność wczytywania plików CSS wtyczki:
                    <li>wp-content/themes/AKTYWNY_MOTYW/nokaut-wl/public/css/nokaut-wl.css</li>
                    <li>wp-content/plugins/nokaut-wl/public/css/nokaut-wl.css</li>
                    </ul>

                    <ul>Kolejność wczytywania plików javascript wtyczki:
                    <li>wp-content/themes/AKTYWNY_MOTYW/nokaut-wl/public/js/nokaut-wl.js</li>
                    <li>wp-content/plugins/nokaut-wl/public/js/nokaut-wl.js</li>
                    </ul>

                    <p>Alternatywą dla kopiowania pliów CSS jest stosowanie bardziej prezyzyjnych selektorów dla elementów HTML we własnych plikach CSS.</p>
                    ',
            )
        );

        // Help Sidebar
        $current_screen->set_help_sidebar(
            '<p><strong>Więcej informacji</strong></p>' .
            '<p><a href="http://nokaut.pl/" target="_blank">nokaut.pl</a></p>' .
            '<p><a href="mailto:partnerzy@nokaut.pl" target="_blank">partnerzy@nokaut.pl</a></p>'
        );
    }
}