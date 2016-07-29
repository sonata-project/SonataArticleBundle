Introduction
============

The first goal of SonataArticle Bundle is to provide a starter kit to manage content.

An ``Article`` is like a skeleton for a piece of content : it contains the main logic of what you want to build,
 but not the content itself.. For example, this is where you can configure content types, or
 publication status, titles, categories...
A ``Fragment`` will handle a part of a the content. It can be very simple like only a title.
  Or more complex, with an associated media, a rich text editor, or something else.

When we built this bundle we kept in mind the PageBundle logic, which we thought was really interesting.
If you understand the logic between ``Pages`` and ``Blocks``, you will understand a lot of similarity
between ``Articles`` ands ``Fragments``.

.. note::

    We decided to create a separate bundle from ``SonataNewsBundle`` because we consider a ``News`` matches the need
    to build a blog, something with comments and notification. The SonataArticleBundle is only a Content Management
    Bundle. It means you will only display a content for the user. No interactions are planned with an article.
    If you wish to add some, use external tools like Discuss, or AddThis, ShareThis, ...
    In another way, fragments are built like Block objects, so you can add your own fragments to fit what you need.
    That implies there is no limit to the kind of content you can manage.
