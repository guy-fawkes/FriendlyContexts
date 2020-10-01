# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added

### Removed

## [0.10.3] - 2020-10-01
### Fixed
- Another fix for associating Alice v3 created entities with inline entities from EntityContext
- Removed debug output used for investigating Alice issue which was fixed in 0.10.2.

## [0.10.2] - 2020-10-01
### Fixed
- Fixed ability to associate inline entities with those created with Alice v3 via AliceContext.

## [0.9.1, 0.10.1] - 2020-09-25
### Changed
- `guzzle\guzzle` is now a dev dependency.

## [0.10.0] - 2020-09-23
### Added
- Add Support for PHP >=7.0,<7.5
- Add Support for Alice v3
- Add Support for Symfony 4

### Removed
- Dropped support for PHP 5.6

## [0.9.0] - 2020-09-23

### Added
- Support Symfony 3

### Removed
- Support for PHP <=5.5
- Support for Symfony 2.8
- Removed `doctrine\data-fixtures` as a dependency

## [0.8.2] - 2017-19-04

### Fixed
- Fix `existLikeFollowing` method in EntityContext

## [0.8.1] - 2017-6-04
### Added
- Fix integer generation and decimal generation for entity context in specific cases
- Fix phpspec dependency and update faker
