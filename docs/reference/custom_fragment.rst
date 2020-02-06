Creating a Custom Fragment
==========================


Defining a fragment Service
---------------------------

First you need to create a class that extends ``Sonata\ArticleBundle\FragmentService\AbstractFragmentService``::


    namespace App\FragmentService;

    use Sonata\AdminBundle\Form\FormMapper;
    use Sonata\ArticleBundle\FragmentService\ExtraContentProviderInterface;
    use Sonata\ArticleBundle\Model\FragmentInterface;

    class MyAwesomeFragmentService extends AbstractFragmentService implements ExtraContentProviderInterface
    {
        public function buildEditForm(FormMapper $form, FragmentInterface $fragment)
        {
            $form->add('settings', 'sonata_type_immutable_array', [
                'keys' => [
                    ['text', 'text', [
                        'label' => 'Text',
                    ]],
                    ['text2', 'textarea', [
                        'label' => 'Text 2',
                    ]],
                ],
                'label' => false,
            ]);
        }

        public function getTemplate()
        {
            return '@App/Fragment/fragment_my_awesome.html.twig';
        }

        public function getExtraContent()
        {
            return [
                'foo' => 'bar',
            ];
        }
    }

Using ``settings`` field with ``keys`` option in ``buildEditForm`` method, you can define all elements that compose your fragment.
Every key is a field that is displayed on the fragment edit form.
The ``getTemplate`` method allows you to define which template should be used when rendering this fragment type.
If needed, your custom fragment can also implement ``ExtraContentProviderInterface``.
So you will be able to implement ``getExtraContent`` method to complete
the content of the fragment before the templating engine renders it.

Then you need to declare the service:

.. code-block:: yaml

    # config/services.yaml

    services:
        app.fragment.my_awesome:
            class: App\FragmentService\MyAwesomeFragmentService
            arguments:
                - 'My Awesome Fragment Name' # Fragment name in admin interface
            tags:
                - { name: sonata.article.fragment, key: app.fragment.my_awesome } # key is your fragment type unique identifier

Defining a fragment template
----------------------------

To render the fragment, simply create a template as you defined it in the service.
Using the twig helper, you will be able to access the following variables inside this template:

* ``fragment`` : The full fragment object.
* ``fields`` : The values that were set in fragment settings.
* ``foo`` : Any keys you may have defined when implementing ``ExtraContentProviderInterface``.

.. code-block:: twig

    {# article_template.html.twig #}

    {# ... #}
    {{ sonata_article_render_article_fragments(article) }}
    {# ... #}


.. code-block:: html+twig

    {# @App/Fragment/fragment_my_awesome.html.twig #}

    <h2>{{ fields.text }}</h2>
    <p>{{ fields.text2 }}</p>
