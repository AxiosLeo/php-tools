# PHP Tools

[![CI Build Status](https://github.com/AxiosCros/php-tools/workflows/CI/badge.svg)](https://github.com/AxiosCros/php-tools/actions?query=workflow%3ACI)
[![Latest Stable Version](https://poser.pugx.org/axios/tools/v)](//packagist.org/packages/axios/tools)

> Some code snippets that are often used in PHP.
> Recommended for use in development environments only.
> Need PHP 7.4/8.0 and above.

- Util Class

| Class Name                               | Feature Description                |
| :--------------------------------------- | :--------------------------------- |
| [SM3](./src/SM3.php)                     | SM3 crypt tool                     |
| [HMac](./src/HMac.php)                   | support custom algorithm with HMac |
| [UUID](./src/UUID.php)                   | generate uuid string               |
| [Path](./src/Path.php)                   | path operator                      |
| [CRC64](./src/CRC64.php)                 | tool for crc64 arithmetic          |
| [ArrayMap](./src/ArrayMap.php)           | tool for array and map data        |
| [RSACrypt](./src/RSACrypt.php)           | RSA crypt tool                     |
| [Datetime](./src/Datetime.php)           | tool for date time operation       |
| [XMLParser](./src/XMLParser.php)         | the parser for XML string          |
| [ListToTree](./src/ListToTree.php)       | convert list to tree               |
| [TreeToList](./src/TreeToList.php)       | convert tree to list               |
| [ForkProcess](./src/ForkProcess.php)     | multi-process demo                 |
| [PharOperator](./src/PharOperator.php)   | tool for build phar file           |
| [BHDConverter](./src/BHDConverter.php)   | tool for binary conversions        |
| [CDKEYProducer](./src/CDKEYProducer.php) | tool for produce CDKEY             |
| [functions](./functions.php)              | helper functions                   |

- Util Functions

| Function Name            | Description                            |
| :----------------------- | :------------------------------------- |
| [sm3][sm3]               | encode string with sm3 algorithm       |
| [sm3_file][sm3_file]     | encode file with sm3 algorithm         |
| [hmac][hmac]             | encode string with hmac algorithm      |
| [halt][halt]             | dump some information and exit process |
| [xml_encode][xml_encode] | convert array to xml string            |
| [xml_decode][xml_decode] | convert xml string to array            |
| [uuid][uuid]             | generate uuid string                   |
| [path_join][path_join]   | join path                              |
| [client_ip][client_ip]   | get client ip                          |

## Install

```bash
composer require axios/tools
```

## Usage

> see [Unit Test Case](./tests/unit/)

[sm3]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L28

[sm3_file]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L38

[hmac]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L8

[halt]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L19

[xml_encode]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L48

[xml_decode]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L55

[uuid]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L62

[path_join]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L73

[client_ip]: https://github.com/AxiosCros/php-tools/blob/8f914703845099a6e91f123f31b3c0972ea3d941/funtions.php#L88