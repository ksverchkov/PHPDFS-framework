![PHPDFS Logo](php-dfs.png)

# PHPDFS - Distributed Filesystem Framework for PHP

PHPDFS is a powerful Distributed Filesystem Framework/Library for PHP that enables developers to build and manage distributed file systems, implement routing, handle permissions, and more. Whether you're building a large-scale distributed storage solution or need a flexible framework for managing files and permissions, PHPDFS has you covered.

## Features

- **Distributed Filesystem**: Build and manage a distributed file storage system across multiple servers.
- **Routing and Controllers**: Define routes and actions for handling incoming requests.
- **Flexible Permissions**: Implement granular permissions and access control for files and actions.
- **Token-based Authentication**: Use tokens with associated permissions for secure authentication.
- **YAML Configuration**: Configure routes, actions, and settings using YAML files.
- **RedBeanPHP Integration**: Easily manage database operations with the powerful RedBeanPHP ORM.
- **Composer Integration**: Install and manage PHPDFS as a Composer package.

## Installation

You can install PHPDFS using Composer. Run the following command in your project directory:

```sh
composer require ksverchkov/phpdfs
```

## Getting Started

Follow these steps to get started with PHPDFS:

1. Configure your `.env` file with your desired settings:

   ```plaintext
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=phpdfs_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Set up your routes and actions in your application.

3. Start building your distributed filesystem solution with PHPDFS!

## Documentation

For detailed usage instructions, configuration options, and examples, please refer to the [documentation](https://github.com/ksverchkov/phpdfs-framework/wiki).

## Contributing

Contributions are welcome! If you'd like to contribute to PHPDFS, please refer to the [contribution guidelines](CONTRIBUTING.md).

## License

PHPDFS is released under the [MIT License](LICENSE).
