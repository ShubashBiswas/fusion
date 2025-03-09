<?php

/**
 * @file FusionThemePlugin.php
 *
 * Copyright (c) 2014-2023 Simon Fraser University
 * Copyright (c) 2003-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class FusionThemePlugin
 * @brief Example fusion theme plugin
 */

namespace APP\plugins\themes\fusion;

use APP\core\Application;
use APP\file\PublicFileManager;
use PKP\config\Config;
use PKP\session\SessionManager;
use APP\template\TemplateManager;

use PKP\plugins\ThemePlugin;

class FusionThemePlugin extends ThemePlugin
{
    /**
     * @copydoc ThemePlugin::isActive()
     */
    public function isActive()
    {
        if (SessionManager::isDisabled()) {
            return true;
        }
        return parent::isActive();
    }

    /**
     * Initialize the theme's styles, scripts and hooks. This is only run for
     * the currently active theme.
     *
     * @return null
     */

    public function init()
    {

        // Register theme options

        // Font options
        $this->addOption('typography', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.fusion.option.typography.label'),
            'description' => __('plugins.themes.fusion.option.typography.description'),
            'options' => [
                [
                    'value' => 'Roboto',
                    'label' => __('plugins.themes.fusion.option.typography.Roboto'),
                ],
                [
                    'value' => 'OpenSans',
                    'label' => __('plugins.themes.fusion.option.typography.OpenSans'),
                ],
                [
                    'value' => 'Montserrat',
                    'label' => __('plugins.themes.fusion.option.typography.Montserrat'),
                ],
            ],
            'fusion' => 'roboto',
        ]);

        // Journal Summary display
        $this->addOption('showDescriptionInJournalIndex', 'FieldOptions', [
            'label' => __('manager.setup.contextSummary'),
            'options' => [
                [
                    'value' => true,
                    'label' => __('plugins.themes.fusion.option.showDescriptionInJournalIndex.option'),
                ],
            ],
            'default' => false,
        ]);

        // Header Background Image
        $this->addOption('useHomepageImageAsHeader', 'FieldOptions', [
            'label' => __('plugins.themes.fusion.option.useHomepageImageAsHeader.label'),
            'description' => __('plugins.themes.fusion.option.useHomepageImageAsHeader.description'),
            'options' => [
                [
                    'value' => true,
                    'label' => __('plugins.themes.fusion.option.useHomepageImageAsHeader.option')
                ],
            ],
            'default' => false,
        ]);

        // Color theme options
        $this->addOption('fusionBaseColour', 'FieldOptions', [
            'type' => 'radio',
            'label' => 'Base colour',
            'options' => [
                [
                    'value' => 'green',
                    'label' => 'Green',
                ],
                [
                    'value' => 'indigo',
                    'label' => 'Indigo',
                ],
                [
                    'value' => 'blue',
                    'label' => 'Blue',
                ],
                [
                    'value' => 'sky',
                    'label' => 'Sky',
                ],
                [
                    'value' => 'orange',
                    'label' => 'Orange',
                ],
            ],
            'default' => 'sky',
        ]);

        // Usage stats display options
        $this->addOption('displayStats', 'FieldOptions', [
            'type' => 'radio',
            'label' => __('plugins.themes.fusion.option.displayStats.label'),
            'options' => [
                [
                    'value' => 'none',
                    'label' => __('plugins.themes.fusion.option.displayStats.none'),
                ],
                [
                    'value' => 'bar',
                    'label' => __('plugins.themes.fusion.option.displayStats.bar'),
                ],
                [
                    'value' => 'line',
                    'label' => __('plugins.themes.fusion.option.displayStats.line'),
                ],
            ],
            'default' => 'none',
        ]);

        $request = Application::get()->getRequest();
        $templateManager = TemplateManager::getManager($request);

        $templateManager->assign('jquery', $this->getJqueryPath($request));
        $templateManager->assign('jqueryUI', $this->getJqueryUIPath($request));

        $templateManager->assign(
            'gradientImageUrl',
            $request->getBaseUrl() . '/plugins/themes/fusion/resources/gradient-noise-purple.png'
        );

        $this->setParent('defaultthemeplugin');
        $this->modifyStyle('stylesheet', ['addLess' => ['styles/remove-borders.less']]);

        // Load primary stylesheet
        $this->addStyle('stylesheet', 'styles/output.css');

        // Load alpinejs for this theme
        $this->addScript('alpinejs', 'js/alpinejs@3.x.x/dist/cdn.min.js');
        $this->addScript('mainjs', 'js/main.js');

        // Add navigation menu areas for this theme
        $this->addMenuArea(['primary', 'user']);
    }

    /**
     * Get the name of the settings file to be installed on new journal
     * creation.
     *
     * @return string
     */
    public function getContextSpecificPluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    /**
     * Get the name of the settings file to be installed site-wide when
     * OJS is installed.
     *
     * @return string
     */
    public function getInstallSitePluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    /**
     * Get the display name of this plugin
     * @return string
     */
    public function getDisplayName()
    {
        return __('plugins.themes.fusion.name');
    }

    /**
     * Get the description of this plugin
     * @return string
     */
    public function getDescription()
    {
        return __('plugins.themes.fusion.description');
    }

    /**
     * Get the jquery path
     *
     * @return string
     */
    public function getJqueryPath($request)
    {
        $min = Config::getVar('general', 'enable_minified') ? '.min' : '';
        return $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jquery/jquery' . $min . '.js';
    }

    /**
     * Get the jqueryUI path
     *
     * @return string
     */
    public function getJqueryUIPath($request)
    {
        $min = Config::getVar('general', 'enable_minified') ? '.min' : '';
        return $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jqueryui/jquery-ui' . $min . '.js';
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\themes\fusion\FusionThemePlugin', '\FusionThemePlugin');
}
