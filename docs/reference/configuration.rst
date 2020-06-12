Configuration
=============

More information can be found `here`_

Full configuration options:

.. configuration-block::

    .. code-block:: yaml

        # config/packages/sonata_article.yaml

        # Default configuration for extension with alias: "sonata_page"
        sonata_article:
            class:
                article: App\Entity\SonataArticleArticle
                fragment: App\Entity\SonataArticleFragment
                category: App\Entity\SonataArticleCategory
                tag: App\Entity\SonataArticleTag
                media: App\Entity\SonataArticleMedia

            fragment_whitelist_provider:
                simple_array_provider:
                    - sonata.article.fragment.title
                    - sonata.article.fragment.text

    .. code-block:: yaml

        # config/packages/doctrine.yaml

        # Enable Doctrine to map the provided entities
        doctrine:
            orm:
                entity_managers:
                    default:
                        mappings:
                            ApplicationSonataArticleBundle: ~
                            SonataArticleBundle: ~

.. _`here`: https://sonata-project.org/bundles/article
