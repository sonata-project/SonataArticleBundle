Configuration
=============

More information can be found `here`_

Full configuration options:

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml

        # Default configuration for extension with alias: "sonata_page"
        sonata_article:
            class:
                article:  Application\Sonata\ArticleBundle\Entity\Article
                fragment: Application\Sonata\ArticleBundle\Entity\Fragment
                category: Application\Sonata\ArticleBundle\Entity\Category
                tag:      Application\Sonata\ArticleBundle\Entity\Tag
                media:    Application\Sonata\ArticleBundle\Entity\Media

    .. code-block:: yaml

        # app/config/config.yml

        # Enable Doctrine to map the provided entities
        doctrine:
            orm:
                entity_managers:
                    default:
                        mappings:
                            ApplicationSonataArticleBundle: ~
                            SonataArticleBundle: ~

.. _`here`: https://sonata-project.org/bundles/article
