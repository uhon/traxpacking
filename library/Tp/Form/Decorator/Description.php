<?php
/**
 * User: uhon
 * Date: 2012/02/15
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Form_Decorator_Description extends Zend_Form_Decorator_Description {
    /**
     * Whether or not to escape the description
     * @var bool
     */
    protected $_escape = false;


    /**
     * Get HTML tag, if any, with which to surround description
     *
     * @return string
     */
    public function getTag()
    {
        if (null === $this->_tag) {
            $tag = $this->getOption('tag');
            if (null !== $tag) {
                $this->removeOption('tag');
            } else {
                $tag = 'td';
            }

            $this->setTag($tag);
            return $tag;
        }

        return $this->_tag;
    }

    /**
     * Render a description
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $description = $element->getDescription();
        $description = trim($description);

        if (!empty($description) && (null !== ($translator = $element->getTranslator()))) {
            $description = $translator->translate($description);
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $class     = $this->getClass();
        $escape    = $this->getEscape();

        $options   = $this->getOptions();

        if ($escape) {
            $description = $view->escape($description);
        }

        if (!empty($tag)) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $options['tag'] = $tag;
            $decorator = new Zend_Form_Decorator_HtmlTag($options);
            $description = $decorator->render($description);
        }

        switch ($placement) {
            case self::PREPEND:
                return $description . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $description;
        }
    }
}
