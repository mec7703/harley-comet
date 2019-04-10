<?php
/**
 * Docs Viewer
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Docs Viewer plugin.
 * 
 * @package Omeka\Plugins\DocsViewer
 */
class DocsViewerPlugin extends Omeka_Plugin_AbstractPlugin
{
    const API_URL = '//docs.google.com/viewer';
    
    const DEFAULT_VIEWER_EMBED = 1;
    
    const DEFAULT_VIEWER_WIDTH = 500;
    
    const DEFAULT_VIEWER_HEIGHT = 600;
    
    // http://docs.google.com/support/bin/answer.py?hl=en&answer=1189935
    protected $_supportedFileFormats = array(
        'doc|docx' => 'Microsoft Word',
        'ppt|pptx' => 'Microsoft PowerPoint',
        'xls|xlsx' => 'Microsoft Excel',
        'tif|tiff' => 'Tagged Image File Format',
        'eps|ps' => 'PostScript',
        'pdf' => 'Adobe Portable Document Format',
        'pages' => 'Apple Pages',
        'ai' => 'Adobe Illustrator',
        'psd' => 'Adobe Photoshop',
        'dxf' => 'Autodesk AutoCad',
        'svg' => 'Scalable Vector Graphics',
        'ttf' => 'TrueType',
        'xps' => 'XML Paper Specification',
    );

    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'initialize',
        'config_form',
        'config',
        'admin_items_show',
        'public_items_show',
    );
    
    protected $_options = array(
        'docsviewer_embed_admin' => self::DEFAULT_VIEWER_EMBED,
        'docsviewer_width_admin' => self::DEFAULT_VIEWER_WIDTH,
        'docsviewer_height_admin' => self::DEFAULT_VIEWER_HEIGHT,
        'docsviewer_embed_public' => self::DEFAULT_VIEWER_EMBED,
        'docsviewer_width_public' => self::DEFAULT_VIEWER_WIDTH,
        'docsviewer_height_public' => self::DEFAULT_VIEWER_HEIGHT,
        'docsviewer_file_formats' => array(),
    );
    
    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        $this->_options['docsviewer_file_formats'] = json_encode(array_keys($this->_supportedFileFormats));
        $this->_installOptions();
    }
    
    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $this->_uninstallOptions();
    }

    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];

        if (version_compare($oldVersion, '2.0', '<=')) {
            set_option('docsviewer_file_formats', json_encode(array_keys($this->_supportedFileFormats)));
        }
    }
    
    /**
     * Initialize the plugin.
     */
    public function hookInitialize()
    {
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }
    
    /**
     * Display the config form.
     */
    public function hookConfigForm()
    {
        echo get_view()->partial(
            'plugins/docs-viewer-config-form.php',
            array(
                'supportedFileFormats' => $this->_supportedFileFormats,
                'fileFormats' => json_decode(get_option('docsviewer_file_formats'), true)
            )
        );
    }
    
    /**
     * Handle the config form.
     */
    public function hookConfig()
    {
        if (!is_numeric($_POST['docsviewer_width_admin']) || 
            !is_numeric($_POST['docsviewer_height_admin']) || 
            !is_numeric($_POST['docsviewer_width_public']) || 
            !is_numeric($_POST['docsviewer_height_public'])) {
            throw new Omeka_Validate_Exception('The width and height must be numeric.');
        }

        $fileFormats = array();
        foreach (array_keys($_POST['docsviewer_file_formats']) as $fmt) {
            if (isset($this->_supportedFileFormats[$fmt])) {
                $fileFormats[] = $fmt;
            }
        }

        set_option('docsviewer_embed_admin', (int) (boolean) $_POST['docsviewer_embed_admin']);
        set_option('docsviewer_width_admin', $_POST['docsviewer_width_admin']);
        set_option('docsviewer_height_admin', $_POST['docsviewer_height_admin']);
        set_option('docsviewer_embed_public', (int) (boolean) $_POST['docsviewer_embed_public']);
        set_option('docsviewer_width_public', $_POST['docsviewer_width_public']);
        set_option('docsviewer_height_public', $_POST['docsviewer_height_public']);
        set_option('docsviewer_file_formats', json_encode($fileFormats));
    }
    
    /**
     * Display the document viewer in admin items/show.
     */
    public function hookAdminItemsShow($args)
    {
        // Embed viewer only if configured to do so.
        if (!get_option('docsviewer_embed_admin')) {
            return;
        }
        echo $args['view']->docsViewer($args['item']->Files, 
                                       get_option('docsviewer_width_admin'), 
                                       get_option('docsviewer_height_admin'));
    }
    
    /**
     * Display the document viewer in public items/show.
     */
    public function hookPublicItemsShow($args)
    {
        // Embed viewer only if configured to do so.
        if (!get_option('docsviewer_embed_public')) {
            return;
        }
        echo $args['view']->docsViewer($args['item']->Files, 
                                       get_option('docsviewer_width_public'), 
                                       get_option('docsviewer_height_public'));
    }
}
