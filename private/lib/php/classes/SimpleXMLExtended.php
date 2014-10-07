<?php

class SimpleXMLExtended extends SimpleXMLElement
{
    public function prependChild($name, $value)
    {
        $dom = dom_import_simplexml($this);

        $new = $dom->insertBefore(
            $dom->ownerDocument->createElement($name, $value),
            $dom->firstChild
        );

        return simplexml_import_dom($new, get_class($this));
    }
}

?>