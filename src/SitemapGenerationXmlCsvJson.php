<?php

namespace Rei00;
use DOMAttr;
use DOMDocument;
use Exception;

class SitemapGenerationXmlCsvJson
{

    const XML = 'xml';
    const CSV = 'csv';
    const JSON = 'json';
    protected array $array;
    protected string $path;
    protected string $type;

    public function __construct(array $array, string $path, string $type)
    {
        $this->array = $array;
        $this->path = $path;
        $this->type = $type;
    }

    public function generate()
    {
        try {
            switch ($this->type) {
                case self::XML:
                    $this->generateXML();
                    break;
                case self::CSV:
                    $this->generateCSV();
                    break;
                case self::JSON:
                    $this->generateJSON();
                    break;
                default:
                    break;
            }
            echo 'Файл создан';
        } catch (Exception $e){
            echo 'Исключение: ' . $e->getMessage();
        }
    }

    private function generateXML(): void
    {
        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;

        $root = $dom->createElement('urlset');

        $attr_urlset_xmlns_xsi = new DOMAttr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttributeNode($attr_urlset_xmlns_xsi);

        $attr_urlset_xmlns = new DOMAttr('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttributeNode($attr_urlset_xmlns);

        $attr_urlset_xsi_schema_location = new DOMAttr('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $root->setAttributeNode($attr_urlset_xsi_schema_location);


        foreach ($this->array as $item)
        {
            $url_tag = $dom->createElement('url');

            try {
                foreach ($item as $key => $value) {
                    $node = $dom->createElement("$key", "$value");
                    $url_tag->appendChild($node);
                }
            } catch (Exception $e){
                echo 'Невалидные данные при создании элемента DOM';
            }
            $root->appendChild($url_tag);
        }

        $dom->appendChild($root);
        $this->path = preg_replace('/\/sitemap.xml\/?$/m', "", $this->path);

        try {
            if (!is_dir($this->path)){
                mkdir($this->path, 0777, true);
            }
            elseif (!is_writable($this->path)){
                throw new Exception('Директория недоступна для записи');
            }
            $dom->save($this->path . '/sitemap.xml');
        }
        catch (Exception $e){
            echo $e->getMessage();
        }

    }

    function generateCSV(): void
    {
        $csv = "loc;lastmod;priority;changefreq\r\n";

        foreach( $this->array as $item ){

            $csv .= implode( ';', $item ) . "\r\n";
        }

        $this->path = preg_replace('/\/sitemap.csv\/?$/m', "", $this->path);

        try {
            if (!is_dir($this->path)){
                mkdir($this->path, 0777, true);
            }
            elseif (!is_writable($this->path)){
                throw new Exception('Директория недоступна для записи');
            }
            file_put_contents( $this->path . '/sitemap.csv', $csv);

        }
        catch (Exception $e){
            echo $e->getMessage();
        }
    }

    function generateJSON(): void
    {
        $json = json_encode($this->array, JSON_UNESCAPED_SLASHES);
        $this->path = preg_replace('/\/sitemap.json\/?$/m', "", $this->path);

        try {
            if (!is_dir($this->path)){
                mkdir($this->path, 0777, true);
            }
            elseif (!is_writable($this->path)){
                throw new Exception('Директория недоступна для записи');
            }
            file_put_contents( $this->path . '/sitemap.json', $json);

        }
        catch (Exception $e){
            echo $e->getMessage();
        }

    }

}




