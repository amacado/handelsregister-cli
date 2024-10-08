# TL;DR
```
docker run --volume ${pwd}/handelsregister-cli-storage:/app/storage amacado/handelsregister-cli:latest --help
```

# About
This is an **unofficial** CLI allowing to interact with [handelsregister.de](https://www.handelsregister.de/) as an alternative approach to
the python CLI provided by [github.com/bundesAPI/handelsregister](https://github.com/bundesAPI/handelsregister).

The _Handelsregister_ is a publicly accessible directory that records information on registered merchants in a specific region 
within the framework of registration law. Entries are mandatory for the facts or legal relationships listed exhaustively in the 
HGB, AktG and GmbHG. In addition, other facts can also be entered if this serves the purpose of the commercial register and
there is a significant interest of legal transactions in their entry.

Access to the commercial register and the documents deposited there is permitted to anyone for information purposes in accordance 
with § 9 Abs. 1 HGB[^1].

> [!IMPORTANT] 
> With reference to the terms of use[^2], a limit of **60 requests per hour** applies, which can be **penalized 
> if exceeded**. The use of this tool is at your own risk and the authors distance themselves from any misuse of the service.

> [...]
> 5.) Es ist unzulässig, mehr als 60 Suchen oder Aufrufe von Rechtsträgern pro Stunde im Registerportal vorzunehmen. 
> Kann das berechtigte Erfordernis für eine höhere Abrufhäufigkeit nachgewiesen werden, besteht die Möglichkeit, 
> bei der Servicestelle Registerportal beim AG Hagen einen Antrag auf einen Zugang mit einer registrierten IP-Adresse 
> nach Ziffer 7) zu stellen.
> [...]
>
> 7.) Bei der Servicestelle Registerportal kann beantragt werden, eine IP-Adresse zur Nutzung des 
> Registerportals zu registrieren, für die die Regel nach Ziffer 5) Satz 1 nicht gilt (Whitelist-IP).
> In dem Antrag muss angegeben werden, in welchem Umfang Informationen aus dem Registerportal 
> abgerufen werden und zu welchen Zwecken diese Abrufe erfolgen. Die Antragstellerin/ der Antragsteller muss
> sich verpflichten, die Vorgaben aus der Nutzungsordnung einzuhalten. Falls sich Anhaltspunkte ergeben, dass 
> eine Nutzerin/Nutzer mit ihren/seinen Abrufen oder Suchen in einem bestimmten Zeitpunkt gegen 
> § 9 Abs. 1 S. 1 HGB verstößt, muss gegenüber der Servicestelle das Interesse an der übermäßigen Nutzung
> des Services nachgewiesen werden können. Kann ein Nachweis nicht erbracht werden, kann die Registrierung
> widerrufen werden.
> [...]

## License
The CLI and source code is published under **Apache License 2.0** (see [LICENSE.md](./LICENSE.md)).

# Usage
The CLI must be executed using `php` this can be achieved either by locally installing php[^3] or using the provided
Docker Container. For readability the following examples are based on the standalone version.

## Parameters and examples
See a full list of all parameters and options by passing `--help` as option to the CLI.

```shell
php handelsregister-cli --help
```
```
Usage:
  handelsregister-cli [options] [--] [<language> [<download>]]

Arguments:
  language                                     Language which is used for interaction with `Handelsregister`. [default: "DE"]
  download                                     Select which file type you want to download (AD|CD|HD|SI). [default: "AD"] 

Options:
      --state[=STATE]                          Select zero, one or more federal states to search in. If none are passed, the search will not be limited to a specific state. Valid codes are based on iso:code:3166:DE. (multiple values allowed)
      --keywords[=KEYWORDS]                    Search for company or keywords.
      --keywords-option[=KEYWORDS-OPTION]      Select an option how `keywords` are treated; One of (all|any|exact). [default: "any"]
      --keywords-match-similar                 If flag is set, similar matching keywords will be included in results.
      --subsidiary-office[=SUBSIDIARY-OFFICE]  Subsidiary / registered office.
      --include-deleted                        If flag is set, search results will also include deleted companies.
      --only-branches                          If flag is set, search only for branches in accordance with new law.
      --register-type[=REGISTER-TYPE]          Type of register.
      --register-number[=REGISTER-NUMBER]      Company register number.
      --register-court[=REGISTER-COURT]        Register court.
      --legal-status[=LEGAL-STATUS]            Company legal status (input value is language-dependent).
      --country[=COUNTRY]                      Country where the company is located (input value is language-dependent).
      --postal-code[=POSTAL-CODE]              Postal code where the company is located.
      --location[=LOCATION]                    Location/City where the company is located.
```

Perform a search with a selected court, register type and number (Deutsche Bahn AG):
```shell
php handelsregister-cli --register-type=HRB --register-number=50000 --register-court='Berlin (Charlottenburg)'
```

Perform a search with a selected court, register type and number (Deutsche Bahn AG) and download the structured xml data (`SI`): 
```shell
php handelsregister-cli DE SI --register-type=HRB --register-number=50000 --register-court='Berlin (Charlottenburg)'
```

Perform a search in multiple federal states (`BW` Baden-Württemberg, `NI` Niedersachsen):
```shell
php handelsregister-cli --state=BW --state=NI
```

### Docker Container (Quickstart) ![image](https://badgen.net/static/-/recommended?label&color=blue)
Replace `${pwd}/handelsregister-cli-storage` with a local path on your hostsystem where the downloaded files, logs and screenshots will be stored.
Pass any additional parameters like `--help` at the end:
```shell
docker run --volume ${pwd}/handelsregister-cli-storage:/app/storage amacado/handelsregister-cli:latest [--help]
```

### Standalone CLI
#### Requirements
* `"php": "^8.2.0"`
* php extension `curl`
* php extension `fileinfo`
* php extension `zip`

> [!CAUTION]
> Standalone CLI is a prototype and not yet fully functional. Usage of the Docker container (quickstart) is recommended.

# Development
## Getting started
We use [Docker](https://www.docker.com/) to ship a build and development environment to get you started:
```shell
docker compose up [--build] [-d]
```
Connect to the development which allows you to interact with the cli:
```shell
docker exec -it handelsregister-cli-development /bin/bash
php ./handelsregister-cli -v
```

## Build production docker container
```shell
docker build -f .\docker\production\Dockerfile . -t amacado/handelsregister-cli:latest
```

## Build a standalone application
See [laravel-zero.com/docs/build-a-standalone-application](https://laravel-zero.com/docs/build-a-standalone-application) for more details:
```shell
php ./handelsregister-cli app:build
```

## Work in progress / ideas / TODO / bugs
GitHub [issues](https://github.com/amacado/handelsregister-cli/issues) are used to track features, ideas and bugs. Feel free to contribute
via pull request or issue creation with bug reports and feature requests.

[^1]: [https://www.gesetze-im-internet.de/hgb/__9.html](https://www.gesetze-im-internet.de/hgb/__9.html) (2024-10-04)
[^2]: [https://www.handelsregister.de/rp_web/welcome.xhtml](https://www.handelsregister.de/rp_web/welcome.xhtml) | Auszug aus Nutzungsbedingungen (2024-10-04)
[^3]: [https://www.php.net/manual/en/install.php](https://www.php.net/manual/en/install.php)
