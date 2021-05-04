<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Strata\Symfony\TwigExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigTest extends TestCase
{

    public function testSlugify()
    {
        $loader = new ArrayLoader([
            'test' => '{{ title | slugify }}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertEquals('my-name-is-earl', $twig->render('test', ['title' => 'My name is Earl']));
    }

    public function testFixUrl()
    {
        $loader = new ArrayLoader([
            'test' => '{{ url | fix_url }}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertEquals('https://domain.com/contact/', $twig->render('test', ['url' => 'domain.com/contact/']));
    }

    public function testExcerpt()
    {
        $loader = new ArrayLoader([
            'test' => '{{ text | excerpt(30) }}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertEquals('Mary had a little lamb, Its…', $twig->render('test', ['text' => 'Mary had a little lamb, Its fleece was white as snow']));
    }

    public function testBuildRevisionFilter()
    {
        $loader = new ArrayLoader([
            'test' => '{{ file | build_version }}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $expectedHash = '2f59d2b6';
        $this->assertEquals(__DIR__ . '/assets/styles.css?v=' . $expectedHash, $twig->render('test', ['file' => __DIR__ . '/assets/styles.css']));
    }

    public function testIsProd()
    {
        $loader = new ArrayLoader([
            'test' => '{% if env is is_prod %} true {% else %} false {% endif %}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertStringContainsString('true', $twig->render('test', ['env' => 'prod']));
        $this->assertStringContainsString('false', $twig->render('test', ['env' => 'staging']));
    }

    public function testStagingBanner()
    {
        $loader = new ArrayLoader([
            'test' => '{{ staging_banner(env) | raw }}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertEmpty($twig->render('test', ['env' => 'prod']));
        $this->assertStringContainsString('staging-banner', $twig->render('test', ['env' => 'staging']));
    }

    public function testNotEmpty()
    {
        $loader = new ArrayLoader([
            'test' => '{% if not_empty(item1, item2) %} true {% else %} false {% endif %}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertStringContainsString('false', $twig->render('test', ['item1' => 0, 'item2' => null]));
        $this->assertStringContainsString('true', $twig->render('test', ['item1' => 'My name is Earl', 'item2' => null]));
        $this->assertStringContainsString('true', $twig->render('test', ['item1' => 'My name is Earl', 'item2' => 'Some text']));
    }

    public function testAllNotEmpty()
    {
        $loader = new ArrayLoader([
            'test' => '{% if all_not_empty(item1, item2) %} true {% else %} false {% endif %}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertStringContainsString('false', $twig->render('test', ['item1' => null, 'item2' => null]));
        $this->assertStringContainsString('true', $twig->render('test', ['item1' => 'My name is Earl', 'item2' => 'Some text']));
    }
}
