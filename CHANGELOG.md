# Changelog

All notable changes to this project will be documented in this file.

 ---

## Release 3.0.0 

### Breaking Changes

- Minimum PHP requirement increased to **PHP 8.1**
- Removed deprecated GraphQL fields from `paynlGetTransactionOutput`:
    - `amountOriginal`
    - `amountPaid`
    - `amountPaidOriginal`
    - `amountRefundOriginal`
- Removed `logo` field from `PayIssuer` type

These fields are no longer available in the GraphQL schema and will cause errors if still queried.

---

