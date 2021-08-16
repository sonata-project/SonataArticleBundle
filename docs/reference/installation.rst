.. index::
    single: Installation
    single: Configuration

Installation
============

Prerequisites
-------------

PHP ^7.2 and Symfony ^4.4 are needed to make this bundle work, there are
also some Sonata dependencies that need to be installed and configured beforehand.

Required dependencies:

* `SonataAdminBundle <https://docs.sonata-project.org/projects/SonataAdminBundle/en/3.x/>`_
* `SonataClassificationBundle <https://docs.sonata-project.org/projects/SonataClassificationBundle/en/3.x/>`_
* `SonataMediaBundle <https://docs.sonata-project.org/projects/SonataMediaBundle/en/3.x/>`_

And the persistence bundle (currently, not all the implementations of the Sonata persistence bundles are available):

* `SonataDoctrineOrmAdminBundle <https://docs.sonata-project.org/projects/SonataDoctrineORMAdminBundle/en/3.x/>`_

Follow also their configuration step; you will find everything you need in
their own installation chapter.

.. note::

    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

Add ``SonataArticleBundle`` via composer::

    composer require sonata-project/article-bundle

Next, be sure to enable the bundles in your ``config/bundles.php`` file if they
are not already enabled::

    // config/bundles.php

    return [
        // ...
        Sonata\ArticleBundle\SonataArticleBundle::class => ['all' => true],
    ];

Configuration
=============

SonataArticleBundle Configuration
---------------------------------

.. code-block:: yaml

    # config/packages/sonata_article.yaml

    sonata_article:
        class:
            article: App\Entity\SonataArticleArticle
            fragment: App\Entity\SonataArticleFragment

        fragment_whitelist_provider:
            simple_array_provider:
                - sonata.article.fragment.title
                - sonata.article.fragment.text

.. note::

    We plan to improve the fragments available to allow a configuration for each article type.
    For example, you will want the fragment 'Comments' only on articles of type 'Blog'.

Doctrine ORM Configuration
--------------------------

Add the bundle in the config mapping definition (or enable `auto_mapping`_)::

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataArticleBundle: ~

And then create the corresponding entities, ``src/Entity/SonataArticleArticle``::

    // src/Entity/SonataArticleArticle.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ArticleBundle\Entity\AbstractArticle;

    /**
     * @ORM\Entity
     * @ORM\Table(name="article__article")
     */
    class SonataArticleArticle extends AbstractArticle
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

and ``src/Entity/SonataArticleFragment``::

    // src/Entity/SonataArticleFragment.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\ArticleBundle\Entity\AbstractFragment;

    /**
     * @ORM\Entity
     * @ORM\Table(name="article__fragment")
     */
    class SonataArticleFragment extends AbstractFragment
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

The only thing left is to update your schema::

    bin/console doctrine:schema:update --force

Next Steps
----------

At this point, your Symfony installation should be fully functional, without errors
showing up from SonataArticleBundle. If, at this point or during the installation,
you come across any errors, don't panic:

    - Read the error message carefully. Try to find out exactly which bundle is causing the error.
      Is it SonataArticleBundle or one of the dependencies?
    - Make sure you followed all the instructions correctly, for both SonataArticleBundle and its dependencies.
    - Still no luck? Try checking the project's `open issues on GitHub`_.

After you have successfully installed the above bundles you need to configure SonataArticleBundle.
All that is needed to quickly set up SonataArticleBundle is described in the :doc:`getting_started` chapter.

.. _`open issues on GitHub`: https://github.com/sonata-project/SonataArticleBundle/issues
.. _`auto_mapping`: http://symfony.com/doc/4.4/reference/configuration/doctrine.html#configuration-overviews
