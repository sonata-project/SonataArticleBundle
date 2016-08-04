Creating a Custom Fragment
==========================


Defining a fragment Service
---------------------------

First you need to create a class that extends ``Sonata\ArticleBundle\FragmentService\AbstractFragmentService``


.. code-block:: php

    namespace Acme\DummyBundle\FragmentService;

    use Sonata\AdminBundle\Form\FormMapper;
    use Sonata\ArticleBundle\Model\FragmentInterface;

    class MyAwesomeFragmentService extends AbstractFragmentService
    {
        public function buildEditForm(FormMapper $form, FragmentInterface $fragment)
        {
            $form->add('settings', 'sonata_type_immutable_array', array(
                'keys' => array(
                    array('text', 'text', array(
                        'label' => 'Text',
                    )),
                    array('text2', 'textarea', array(
                        'label' => 'Text 2',
                    )),
                ),
                'label' => false,
            ));
        }

        public function getTemplate()
        {
            return 'AcmeDummyBundle:Fragment:fragment_my_awesome.html.twig';
        }
    }


Using ``settings`` field with ``keys`` option in ``buildEditForm`` method, you can define all elements that compose your fragment.
Every key is a field that is displayed on the fragment edit form.
The ``getTemplate`` method allows you to define which template should be used when rendering this fragment type.


Then you need to declare the service:


.. code-block:: yaml

    services:
        acme.dummy.fragment.my_awsome:
            class: Acme\DummyBundle\FragmentService\MyAwesomeFragmentService
            arguments:
                - My Awesome Fragment Name # Fragment name in admin interface
            tags:
                - { name: sonata.article.fragment, key: acme.dummy.fragment.my_awesome }
                # Where key is your fragment type unique identifier


Defining a fragment template
----------------------------

To render the fragment, simply create a template as you defined it in the service.
Using the twig helper, you will be able to access the following variables inside this template:

* ``fragment`` : The full fragment object.
* ``fields`` : The values that were set in fragment settings.


.. code-block:: jinja

    {# article_template.html.twig #}
    {# ... #}
    {{ sonata_article_render_article_fragments(article) }}
    {# ... #}


.. code-block:: jinja

    {# AcmeDummyBundle:Fragment:fragment_my_awesome.html.twig #}
    <h2>{{ fields.text }}</h2>
    <p>{{ fields.text2 }}</p>
