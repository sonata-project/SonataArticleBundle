# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [1.6.1](https://github.com/sonata-project/SonataArticleBundle/compare/1.6.0...1.6.1) - 2021-07-17
### Changed
- [[#280](https://github.com/sonata-project/SonataArticleBundle/pull/280)] JS will now use the delete field to mark a fragment as deleted instead of not sending the value. ([@srascar](https://github.com/srascar))

### Fixed
- [[#284](https://github.com/sonata-project/SonataArticleBundle/pull/284)] Back Office Title is correctly submitted as empty string when empty in form request ([@srascar](https://github.com/srascar))

## [1.6.0](https://github.com/sonata-project/SonataArticleBundle/compare/1.5.0...1.6.0) - 2021-02-15
### Added
- [[#216](https://github.com/sonata-project/SonataArticleBundle/pull/216)] Added dutch translations ([@zghosts](https://github.com/zghosts))

### Changed
- [[#171](https://github.com/sonata-project/SonataArticleBundle/pull/171)] SonataEasyExtendsBundle is now optional, using SonataDoctrineBundle is preferred ([@jordisala1991](https://github.com/jordisala1991))

### Deprecated
- [[#171](https://github.com/sonata-project/SonataArticleBundle/pull/171)] Using SonataEasyExtendsBundle to add Doctrine mapping information ([@jordisala1991](https://github.com/jordisala1991))

## [1.5.0](https://github.com/sonata-project/SonataArticleBundle/compare/1.4.1...1.5.0) - 2020-07-16
### Removed
- [[#173](https://github.com/sonata-project/SonataArticleBundle/pull/173)] SonataCoreBundle dependencies ([@jordisala1991](https://github.com/jordisala1991))
- [[#173](https://github.com/sonata-project/SonataArticleBundle/pull/173)] Support for Symfony < 4.4 ([@jordisala1991](https://github.com/jordisala1991))

## [1.4.1](https://github.com/sonata-project/SonataArticleBundle/compare/1.4.0...1.4.1) - 2020-03-19
### Fixed
- article fragments reset order bug

## [1.4.0](https://github.com/sonata-project/SonataArticleBundle/compare/1.3.0...1.4.0) - 2020-01-06
### Added
- new Twig blocks in `edit_collection_fragment.html.twig`

## [1.3.0](https://github.com/sonata-project/SonataArticleBundle/compare/1.2.2...1.3.0) - 2019-12-14
### Added
- Added to fragmentList.jquery.js `moveSelectedFragmentOutsideOfForm()` to move selected fragment in tmp dom
- Added to fragmentList.jquery.js `cancelFragmentDeletion()`  to cancel the fragment deletion

### Changed
The 'removed' fragments are not removed completely just moved in another dom,
which allows the cancellation.

## [1.2.2](https://github.com/sonata-project/SonataArticleBundle/compare/1.2.1...1.2.2) - 2019-11-14
### Fixed
- special chars handling in the "type" field

## [1.2.1](https://github.com/sonata-project/SonataArticleBundle/compare/1.2.0...1.2.1) - 2019-10-14
### Fixed
- Syntax error in template

## [1.2.0](https://github.com/sonata-project/SonataArticleBundle/compare/1.1.0...1.2.0) - 2019-05-28

### Added
- Add `|trans()` in the follow twig files: `edit_collection_fragment.html.twig`
  and `form.html.twig` to translate the fragment name.
- Add Hungarian translations

### Changed
- Changed `ArticleInterface`, `FragmentInterface` and
`ArticleFragmentInterface` to allow null return types.

### Fixed
- Fix deprecation for symfony/config 4.2+

## [1.1.0](https://github.com/sonata-project/SonataArticleBundle/compare/1.0.0...1.1.0) - 2018-12-11
### Added
- Added `Article::isValidated()` method

### Fixed
- Fixed `Article::getValidatedAt()` to be able to return null

### Changed
- In `AbstractArticle`, `getValidatedAt(): \DateTimeInterface` becomes
  `getValidatedAt(): ?\DateTimeInterface`, this is technically a BC-break
