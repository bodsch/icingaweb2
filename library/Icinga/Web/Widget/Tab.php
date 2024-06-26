<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Web\Widget;

use Icinga\Web\Url;

/**
 * A single tab, usually used through the tabs widget
 *
 * Will generate an &lt;li&gt; list item, with an optional link and icon
 *
 * @property string $name      Tab identifier
 * @property string $title     Tab title
 * @property string $icon      Icon URL, preferrably relative to the Icinga
 *                             base URL
 * @property string|URL $url   Action URL, preferrably relative to the Icinga
 *                             base URL
 * @property string $urlParams Action URL Parameters
 *
 */
class Tab extends AbstractWidget
{
    /**
     * Whether this tab is currently active
     *
     * @var bool
     */
    private $active = false;

    /**
     * Default values for widget properties
     *
     * @var ?string
     */
    private $name = null;

    /**
     * The title displayed for this tab
     *
     * @var string
     */
    private $title = '';

    /**
     * The label displayed for this tab
     *
     * @var string
     */
    private $label = '';

    /**
     * The Url this tab points to
     *
     * @var Url|null
     */
    private $url = null;

    /**
     * The parameters for this tab's Url
     *
     * @var array
     */
    private $urlParams = array();

    /**
     * The icon image to use for this tab or null if none
     *
     * @var string|null
     */
    private $icon = null;

    /**
     * The icon class to use if $icon is null
     *
     * @var string|null
     */
    private $iconCls = null;

    /**
     * Additional a tag attributes
     *
     * @var array
     */
    private $tagParams;

    /**
     * Whether to open the link target on a new page
     *
     * @var boolean
     */
    private $targetBlank = false;

    /**
     * Data base target that determines if the link will be opened in a side-bar or in the main container
     *
     * @var null
     */
    private $baseTarget = null;

    /**
     * Sets an icon image for this tab
     *
     * @param string $icon      The url of the image to use
     */
    public function setIcon($icon)
    {
        if (is_string($icon) && strpos($icon, '.') !== false) {
            $icon = Url::fromPath($icon);
        }
        $this->icon = $icon;
    }

    /**
     * Set's an icon class that will be used in an <i> tag if no icon image is set
     *
     * @param string $iconCls       The CSS class of the icon to use
     */
    public function setIconCls($iconCls)
    {
        $this->iconCls = $iconCls;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ?string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the tab label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get the tab label
     *
     * @return string
     */
    public function getLabel()
    {
        if (! $this->label) {
            return $this->title;
        }

        return $this->label;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set the Url this tab points to
     *
     * @param string|Url $url       The Url to use for this tab
     */
    public function setUrl($url)
    {
        if (is_string($url)) {
            $url = Url::fromPath($url);
        }
        $this->url = $url;
    }

    /**
     * Get the tab's target URL
     *
     * @return Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the parameters to be set for this tabs Url
     *
     * @param array $url        The Url parameters to set
     */
    public function setUrlParams(array $urlParams)
    {
        $this->urlParams = $urlParams;
    }

    /**
     * Set additional a tag attributes
     *
     * @param array $tagParams
     */
    public function setTagParams(array $tagParams)
    {
        $this->tagParams = $tagParams;
    }

    public function setTargetBlank($value = true)
    {
        $this->targetBlank =  $value;
    }

    public function setBaseTarget($value)
    {
        $this->baseTarget = $value;
    }

    /**
     * Create a new Tab with the given properties
     *
     * Allowed properties are all properties for which a setter exists
     *
     * @param array $properties     An array of properties
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $name => $value) {
            $setter = 'set' . ucfirst($name);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * Set this tab active (default) or inactive
     *
     * This is usually done through the tabs container widget, therefore it
     * is not a good idea to directly call this function
     *
     * @param  bool $active Whether the tab should be active
     *
     * @return $this
     */
    public function setActive($active = true)
    {
        $this->active = (bool) $active;
        return $this;
    }

    /**
     * @see Widget::render()
     */
    public function render()
    {
        $view = $this->view();
        $classes = array();
        if ($this->active) {
            $classes[] = 'active';
        }

        $caption = $view->escape($this->getLabel());
        $tagParams = $this->tagParams;
        if ($this->targetBlank) {
            // add warning to links that open in new tabs to improve accessibility, as recommended by WCAG20 G201
            $caption .= '<span class="info-box display-on-hover"> opens in new window </span>';
            $tagParams['target'] ='_blank';
        }

        if ($this->title) {
            if ($tagParams !== null) {
                $tagParams['title'] = $this->title;
                $tagParams['aria-label'] = $this->title;
            } else {
                $tagParams = array(
                    'title'         => $this->title,
                    'aria-label'    => $this->title
                );
            }
        }

        if ($this->baseTarget !== null) {
            $tagParams['data-base-target'] = $this->baseTarget;
        }

        if ($this->icon !== null) {
            if (strpos($this->icon, '.') === false) {
                $caption = $view->icon($this->icon) . $caption;
            } else {
                $caption = $view->img($this->icon, null, array('class' => 'icon')) . $caption;
            }
        }

        if ($this->url !== null) {
            $this->url->overwriteParams($this->urlParams);

            if ($tagParams !== null) {
                $params = $view->propertiesToString($tagParams);
            } else {
                $params = '';
            }

            $tab = sprintf(
                '<a href="%s"%s>%s</a>',
                $this->view()->escape($this->url->getAbsoluteUrl()),
                $params,
                $caption
            );
        } else {
            $tab = $caption;
        }

        $class = empty($classes) ? '' : sprintf(' class="%s"', implode(' ', $classes));
        return '<li ' . $class . '>' . $tab . "</li>\n";
    }
}
