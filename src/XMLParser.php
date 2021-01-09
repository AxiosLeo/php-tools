<?php

declare(strict_types=1);

namespace axios\tools;

class XMLParser
{
    public static function decode(string $xml_string): array
    {
        return json_decode(
            json_encode(
                simplexml_load_string($xml_string, 'SimpleXMLElement', LIBXML_NOCDATA)
            ),
            true
        );
    }

    /**
     * @param string $root_node <data></data>
     * @param array  $root_attr <data attrs></data>
     * @param string $item_node <data><item></item></data>
     * @param string $item_key  <data><item id=""></item></data>
     * @param string $encoding
     */
    public static function encode(array $data, $root_node = 'data', $root_attr = [], $item_node = 'item', $item_key = 'id', $encoding = 'utf-8'): string
    {
        $attr = '';
        if (!empty($root_attr)) {
            $array = [];
            foreach ($root_attr as $key => $value) {
                $array[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $array);
        }
        $attr = empty($attr) ? '' : " {trim({$attr})}";
        $xml  = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml .= "<{$root_node}{$attr}>";
        $xml .= self::dataToXml($data, $item_node, $item_key);
        $xml .= "</{$root_node}>";

        return $xml;
    }

    /**
     * convert array to xml string.
     *
     * @param array  $data array data
     * @param string $item <item></item>
     * @param string $id   <item id=""></item>
     */
    protected static function dataToXml(array $data, string $item, string $id): string
    {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key         = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (\is_array($val) || \is_object($val)) ? self::dataToXml($val, $item, $id) : $val;
            $xml .= "</{$key}>";
        }

        return $xml;
    }
}
