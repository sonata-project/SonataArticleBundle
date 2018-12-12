# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).


## [1.1.0](https://github.com/sonata-project/SonataArticleBundle/compare/1.0.0...1.1.0) - 2018-12-11
### Added
- Added `Article::isValidated()` method

### Fixed
- Fixed `Article::getValidatedAt()` to be able to return null

### Changed
- In `AbstractArticle`, `getValidatedAt(): \DateTimeInterface` becomes
  `getValidatedAt(): ?\DateTimeInterface`, this is technically a BC-break
