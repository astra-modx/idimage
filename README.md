# Creating an components for MODX Revolution 2 in Docker

Rapid application deployment for MODX Revolution 2

```shell
cp .env.example .env
```

Change your package name `PACKAGE_NAME=myExtra` [.env](.env)

## Fast start

Create directory `Extras/myapp` and run docker container

```shell
make run-app # all steps in one
```

## Steps install

```shell
make package-create-new # Rename package from .env PACKAGE_NAME
```

### Run project

```shell
make remake
```

### Build transport package for dev

```shell
make package-build  
```

### Install package system

```shell
make package-install 
```

Then you can work with files from the `Extras/myapp` directory

# Gitify

[github документация](https://modmore.github.io/Gitify/ru/)

Change your `packages` install file [.gitify](.gitify)

Default:

- ace-1.9.4-pl
- adminpanel-1.1.0-pl
- moddevtools-1.2.1-pl
- pdotools-2.13.2-pl

### Build for repository

```shell
make package-build-deploy # your zip pack to dist ./target
```

### All commands

See [Makefile](Makefile)
