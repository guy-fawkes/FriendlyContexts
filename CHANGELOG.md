# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
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
