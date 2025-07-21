# CHANGELOG

## 3.0.0 - 2025-07-21
### Changed
- All methods in the trait `ExtendedTestCase` are renamed to `create<old name>Assertion`

## 2.0.1 - 2025-05-25
### Fixed
- `assertContentType(string $contentType)`, `document()` and `json()` methods don't fail when the header contains charset or boundary directives

## 2.0.0 - 2025-05-18
### Changed
- Trait and interface names: `TestCase -> ExtendedTestCase`, `ExtendedTestCase -> ExtendedTestCaseInterface`

## 1.0.0 - 2025-04-19
Release
