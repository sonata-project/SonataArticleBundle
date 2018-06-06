<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ArticleBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class FragmentAdminController extends CRUDController
{
    /**
     * @throws \RuntimeException
     * @throws AccessDeniedException
     *
     * @return Response
     */
    public function formAction()
    {
        if (!$this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException('Access Denied to the action create fragment');
        }

        $object = $this->admin->getNewInstance();

        $this->admin->setSubject($object);

        /** @var $form Form */
        $form = $this->admin->getForm();
        $form->setData($object);

        $view = $form->createView();

        // Set the theme for the current Admin Form
        $this->setFormTheme($view, $this->admin->getFormTheme());

        $request = $this->getRequest();
        // We need to replace name attributes to have a s20003903[fragments][n][...] format as this form doesn't.
        // Know of its parent: s/s20003903_fragments_n/s20003903[fragments][n]
        $search = sprintf('name="%s_%d', $request->get('elementId'), $request->get('fragCount', 0));
        $replace = sprintf('name="%s[fragments][%d]', $request->get('uniqid'), $request->get('fragCount', 0));

        $response = $this->render($this->admin->getTemplate('edit'), [
            'form' => $view,
            'object' => $object,
            'settings' => $this->admin->getSettings(),
        ]);

        $newContent = str_replace($search, $replace, $response->getContent());

        $response->setContent($newContent);

        return $response;
    }

    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     */
    private function setFormTheme(FormView $formView, $theme): void
    {
        $twig = $this->get('twig');
        // BC for Symfony < 3.2 where this runtime does not exist
        if (!method_exists(AppVariable::class, 'getToken')) {
            $twig->getExtension(FormExtension::class)
                ->renderer->setTheme($formView, $theme);

            return;
        }
        // BC for Symfony < 3.4 where runtime should be TwigRenderer
        if (!method_exists(DebugCommand::class, 'getLoaderPaths')) {
            $twig->getRuntime(TwigRenderer::class)->setTheme($formView, $theme);

            return;
        }
        $twig->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
    }
}
