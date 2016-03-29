# rabbit-mq-admin-toolkit-cli

[![Build Status](https://travis-ci.org/olaurendeau/rabbit-mq-admin-toolkit-cli.svg?branch=master)](https://travis-ci.org/olaurendeau/rabbit-mq-admin-toolkit-cli)

CLI wrapper for RabbitMQ admin toolkit

## Installation

### PHP Archive (PHAR)

The easiest way to obtain `rabbit-mq-admin-toolkit` is to download a [PHP Archive (PHAR)](http://php.net/phar) that has all required dependencies of `rabbit-mq-admin-toolkit` bundled in a single file:

    wget https://github.com/olaurendeau/rabbit-mq-admin-toolkit-cli/releases/download/v1.0.0/rabbit-mq-admin-toolkit.phar
    chmod +x rabbit-mq-admin-toolkit.phar
    mv rabbit-mq-admin-toolkit.phar /usr/local/bin/rabbit-mq-admin-toolkit

You can also immediately use the PHAR after you have downloaded it, of course:

    wget https://github.com/olaurendeau/rabbit-mq-admin-toolkit-cli/releases/download/v1.0.0/rabbit-mq-admin-toolkit.phar
    php rabbit-mq-admin-toolkit.phar
    
## Configuration

`rabbit-mq-admin-toolkit` expect to find a `.rabbit-mq-admin-toolkit.yml` file, in your project folder, looking like that:

```yml
# .rabbit-mq-admin-toolkit.yml
delete_allowed: true # Allow deletion of exchange, queues and binding for updating configuration. Shouldn't be enabled in production
connections:
    default: http://user:password@localhost:15672
vhosts:
    default:
        name: /my_vhost
        permissions:
            user: ~
        exchanges:
            exchange.a: ~
        queues:
            queue.a:
                bindings:
                    - { exchange: exchange.a, routing_key: "a.#" }
                    - { exchange: exchange.a, routing_key: "b.#" }
```

see [olaurendeau/RabbitMqAdminToolkitBundle](https://github.com/olaurendeau/RabbitMqAdminToolkitBundle) for all configuration possibilities

## Usage

```bash
rabbit-mq-admin-toolkit define
```

Optionnaly it's possible to specify the configuration file

```bash
rabbit-mq-admin-toolkit define .rabbit-mq-admin-toolkit.dev.yml
```
