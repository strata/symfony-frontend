<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Strata\SymfonyBundle\Twig\TwigExtension;
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

    public function testTableOfContents()
    {
        $loader = new ArrayLoader([
            'test1' => '{% set toc = table_of_contents(content) %}{{ toc.headings | raw }}',
            'test2' => '{% set toc = table_of_contents(content) %}{{ toc.html | raw }}',
        ]);
        $twig = new Environment($loader);
        $twig->enableDebug();
        $twig->addExtension(new TwigExtension());

        $html = file_get_contents(__DIR__ . '/html/example.html');
        $this->assertStringContainsString('<a href="#rigging">Rigging</a>', $twig->render('test1', ['content' => $html]));
        $this->assertStringContainsString('<a href="#topmast">Topmast</a>', $twig->render('test1', ['content' => $html]));
        $this->assertStringNotContainsString('Test heading', $twig->render('test1', ['content' => $html]));
        $this->assertStringContainsString('<a href="#jolly-roger">Jolly Roger</a>', $twig->render('test1', ['content' => $html]));

        $this->assertStringContainsString('<h2 id="rigging">Rigging</h2>', $twig->render('test2', ['content' => $html]));
        $this->assertStringContainsString('<h3 id="topmast">Topmast</h3>', $twig->render('test2', ['content' => $html]));
        $this->assertStringNotContainsString('<h4 id="test-heading">Test heading</h4>', $twig->render('test2', ['content' => $html]));
        $this->assertStringContainsString('<h2 id="jolly-roger">Jolly Roger</h2>', $twig->render('test2', ['content' => $html]));
    }

    public function testTableOfContentsDifferentHeadingLevels()
    {
        // h2 only
        $loader = new ArrayLoader([
            'test1' => "{% set toc = table_of_contents(content, ['h2']) %}{{ toc.headings() | raw }}",
            'test2' => "{% set toc = table_of_contents(content, ['h2']) %}{{ toc.html | raw }}'"
        ]);
        $twig = new Environment($loader);
        $twig->enableDebug();
        $twig->addExtension(new TwigExtension());

        $html = file_get_contents(__DIR__ . '/html/example.html');

        $this->assertStringContainsString('<a href="#rigging">Rigging</a>', $twig->render('test1', ['content' => $html]));
        $this->assertStringNotContainsString('<a href="#topmast">Topmast</a>', $twig->render('test1', ['content' => $html]));
        $this->assertStringContainsString('<h2 id="rigging">Rigging</h2>', $twig->render('test2', ['content' => $html]));
        $this->assertStringNotContainsString('<h3 id="topmast">Topmast</h3>', $twig->render('test2', ['content' => $html]));

        // h2-h4 with duplicates
        $loader = new ArrayLoader([
            'test1' => "{% set toc = table_of_contents(content, ['h2', 'h3', 'h4']) %}{{ toc.headings() | raw }}",
            'test2' => "{% set toc = table_of_contents(content, ['h2', 'h3', 'h4']) %}{{ toc.html | raw }}",
        ]);
        $twig = new Environment($loader);
        $twig->enableDebug();
        $twig->addExtension(new TwigExtension());

        $this->assertStringContainsString('<a href="#rigging">Rigging</a>', $twig->render('test1', ['content' => $html]));
        $this->assertStringContainsString('<a href="#topmast">Topmast</a>', $twig->render('test1', ['content' => $html]));
        $this->assertStringContainsString('<a href="#test-heading">Test heading</a>', $twig->render('test1', ['content' => $html]));
        $this->assertStringContainsString('<a href="#test-heading-1">Test heading</a>', $twig->render('test1', ['content' => $html]));
        $this->assertStringContainsString('<a href="#test-heading-2">Test heading</a>', $twig->render('test1', ['content' => $html]));

        $this->assertStringContainsString('<h2 id="rigging">Rigging</h2>', $twig->render('test2', ['content' => $html]));
        $this->assertStringContainsString('<h3 id="topmast">Topmast</h3>', $twig->render('test2', ['content' => $html]));
        $this->assertStringContainsString('<h4 id="test-heading">Test heading</h4>', $twig->render('test2', ['content' => $html]));
        $this->assertStringContainsString('<h4 id="test-heading-1">Test heading</h4>', $twig->render('test2', ['content' => $html]));
        $this->assertStringContainsString('<h4 id="test-heading-2">Test heading</h4>', $twig->render('test2', ['content' => $html]));
    }

    public function testRelativeUrl()
    {
        $loader = new ArrayLoader([
            'test' => '{{ url | relative_url }}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertEquals('/contact/', $twig->render('test', ['url' => 'https://domain.com/contact/']));
        $this->assertEquals('/contact/child-page', $twig->render('test', ['url' => 'https://domain.com/contact/child-page']));
        $this->assertEquals('/contact/child-page?foo=bar#bookmark', $twig->render('test', ['url' => 'https://domain.com/contact/child-page?foo=bar#bookmark']));
    }

    public function testTrailingSlash()
    {
        $loader = new ArrayLoader([
            'test1' => '{{ url | trailing_slash }}',
            'test2' => '{{ url | no_trailing_slash }}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertEquals('/contact/', $twig->render('test1', ['url' => '/contact']));
        $this->assertEquals('/contact', $twig->render('test2', ['url' => '/contact/']));
    }

    public function testCombinedFilters()
    {
        $loader = new ArrayLoader([
            'test1' => '{{ url | relative_url | trailing_slash }}',
            'test2' => '{{ url | relative_url | no_trailing_slash }}',
        ]);
        $twig = new Environment($loader);
        $twig->addExtension(new TwigExtension());

        $this->assertEquals('/contact/', $twig->render('test1', ['url' => 'https://domain.com/contact']));
        $this->assertEquals('/contact', $twig->render('test2', ['url' => 'https://domain.com/contact/']));
    }
}
