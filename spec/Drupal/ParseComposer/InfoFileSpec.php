<?php

namespace spec\Drupal\ParseComposer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InfoFileSpec extends ObjectBehavior
{
    function it_defaults_to_reasonable_constraint()
    {
        $viewsUiInfo = <<<'EOF'
name = Views UI
description = Administrative interface to views. Without this module, you cannot create or edit your views.
package = Views
core = 7.x
configure = admin/structure/views
dependencies[] = views
files[] = views_ui.module
files[] = plugins/views_wizard/views_ui_base_views_wizard.class.php
EOF;
        $this->beConstructedWith('views_ui', $viewsUiInfo, 7);
        $this->constraint('views')->shouldReturn(['drupal/views' => '7.*']);
    }

    function it_understands_full_versions_in_constraints()
    {
        $fooInfo = <<<'EOF'
name = Foo
description = Without this module, you cannot create or edit your foo.
package = Foo
core = 7.x
configure = admin/structure/foo
dependencies[] = bar (7.x-2.x-dev)
files[] = plugins/foo_wizard/foo_base_foo_wizard.class.php
EOF;
        $this->beConstructedWith('foo', $fooInfo, 7);
        $this->constraint('bar (7.x-2.x-dev)')->shouldReturn(['drupal/bar' => '7.2.x-dev']);
    }

    function it_understands_operators_in_constraints()
    {
        $fooInfo = '';
        $this->beConstructedWith('foo', $fooInfo, 7);

        // Valid constraints in Drupal: https://www.drupal.org/node/542202#dependencies
        // Valid constraints in composer: https://getcomposer.org/doc/01-basic-usage.md#package-versions

        $this->constraint('bar (1.0)')->shouldReturn(['drupal/bar' => '7.1.0']);
        $this->constraint('bar (1.x)')->shouldReturn(['drupal/bar' => '7.1.*']);
        $this->constraint('bar (>=1.x)')->shouldReturn(['drupal/bar' => '>=7.1.0']);
        $this->constraint('bar (>= 1.x)')->shouldReturn(['drupal/bar' => '>=7.1.0']);
        $this->constraint('bar (>1.0, <=3.2, !=3.0)')->shouldReturn(['drupal/bar' => '>7.1.0, <=7.3.2, !=7.3.0']);
        $this->constraint('bar (>1.0)')->shouldReturn(['drupal/bar' => '>7.1.0']);
        $this->constraint('bar (>7.x-1.5)')->shouldReturn(['drupal/bar' => '>7.1.5']);
        $this->constraint('system (>=7.53)')->shouldReturn(['drupal/system' => '>=7.53.0']);
        $this->constraint('menu (>7.11)')->shouldReturn(['drupal/menu' => '>7.11.0']);
        $this->constraint('not_core (>=7.53)')->shouldReturn(['drupal/not_core' => '>=7.7.53']);
    }
}
