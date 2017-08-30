<?php

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
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class FragmentAdminController extends CRUDController
{
    /**
     * @return Response
     *
     * @throws \RuntimeException
     * @throws AccessDeniedException
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
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        $request = $this->getRequest();
        // We need to replace name attributes to have a s20003903[fragments][n][...] format as this form doesn't.
        // Know of its parent: s/s20003903_fragments_n/s20003903[fragments][n]
        $search = sprintf('name="%s_%d', $request->get('elementId'), $request->get('fragCount', 0));
        $replace = sprintf('name="%s[fragments][%d]', $request->get('uniqid'), $request->get('fragCount', 0));

        $response = $this->render($this->admin->getTemplate('edit'), array(
            'form' => $view,
            'object' => $object,
            'settings' => $this->admin->getSettings(),
        ));

        $newContent = str_replace($search, $replace, $response->getContent());

        $response->setContent($newContent);

        return $response;
    }
}
