Installation
============

SonataArticleBundle can be installed at any moment during a project's lifecycle,
whether it's a clean Symfony installation or an existing project.

Downloading the code
--------------------

Use composer to manage your dependencies and download SonataArticleBundle:

.. code-block:: bash

    $ php composer.phar require sonata-project/article-bundle

Check `packagist <https://packagist.org/packages/sonata-project/article-bundle>`_
for all versions.


Enabling SonataArticleBundle
----------------------------

You have to enable it in your ``AppKernel.php``, and configure it manually.

.. code-block:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...

            // Add SonataArticleBundle
            new Sonata\ArticleBundle\SonataArticleBundle(),

            // ...
        );
    }

Configuring SonataArticleBundle dependencies
--------------------------------------------

You will need to configure SonataArticleBundle's entities, if you plan to use them.
You can also configure the list of available fragments for your articles.

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml

        sonata_article:
            class:
                article:  Application\Sonata\ArticleBundle\Entity\Article
                fragment: Application\Sonata\ArticleBundle\Entity\Fragment

            fragment_whitelist_provider:
                simple_array_provider:
                    - sonata.article.fragment.title
                    - sonata.article.fragment.text

.. note::

    We plan to improve the fragments available to allow a configuration for each article type.
    For example, you will want the fragment 'Comments' only on articles of type 'Blog'.

Cleaning up
-----------

Usually, when installing new bundles, it is a good practice to delete your cache:

.. code-block:: bash

    $ php bin/console cache:clear

At this point, your Symfony installation should be fully functional, with no errors
showing up from SonataArticleBundle. SonataArticleBundle is installed
but not yet configured (more on that in the next section), so you won't be able to
use it yet.

If, at this point or during the installation, you come across any errors, don't panic:

    - Read the error message carefully. Try to find out exactly which bundle is causing the error.
      Is it SonataArticleBundle or one of the dependencies?
    - Make sure you followed all the instructions correctly, for both SonataArticleBundle and its dependencies.
    - Odds are that someone already had the same problem, and it's documented somewhere.
      Check Google_, `Sonata Users Group`_, `Stack Overflow`_ or `Symfony Support`_ to see if you can find a solution.
    - Still no luck? Try checking the project's `open issues on GitHub`_.

After you have successfully installed the above bundles you need to configure SonataArticleBundle.
All that is needed to quickly set up SonataArticleBundle is described in the :doc:`getting_started` chapter.

.. _Google: http://www.google.com
.. _`Sonata Users Group`: https://groups.google.com/group/sonata-users
.. _`Symfony Support`: http://symfony.com/support
.. _`Stack Overflow`: https://stackoverflow.com/search?q=sonata-article-bundle
.. _`open issues on GitHub`: https://github.com/sonata-project/SonataArticleBundle/issues

