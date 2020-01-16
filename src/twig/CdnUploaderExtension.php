<?php

namespace kamaelkz\yii2cdnuploader\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Расширения twig для SEO настроек
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CdnUploaderExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $constants = [];

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'uploader_constant',
                function($value) {
                    list($class, $constant) = explode('::', $value);
                    $namespace = "kamaelkz\\yii2cdnuploader\\enum\\{$class}";
                    $hash = md5($namespace);
                    if(! isset($this->constants[$hash])) {
                        $reflection = new \ReflectionClass($namespace);
                        $constants = $reflection->getConstants();
                        $this->constants[$hash] = $constants;
                    } else {
                        $constants = $this->constants[$hash];
                    }

                    return $constants[$constant] ?? null;
                }
            )
        ];
    }
}