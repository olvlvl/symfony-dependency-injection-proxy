# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/olvlvl/symfony-dependency-injection-proxy).



## Pull Requests

- **Code style** — We're following [PSR-12 Coding Standard][]. Check the code style with `make lint`.
- **Code health** — We're using [PHPStan][] to analyse the code, with maximum scrutiny. Check the code with `make lint`.
- **Add tests!** — Your contribution won't be accepted if it doesn't have tests.
- **Document any change in behaviour** — Make sure the `README.md` and any other relevant documentation are kept
  up-to-date.
- **Consider our release cycle** — We follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not
  an option.
- **Create feature branches** — We won't pull from your main branch.
- **One pull request per feature** — If you want to do more than one thing, send multiple pull requests.
- **Send coherent history** — Make sure each individual commit in your pull request is meaningful. If you had to make
  multiple intermediate commits while developing, please [squash them][git-squash] before submitting.



## Running Tests

A few Docker containers are provided for local development:

- Run `make test-container-72` for PHP 7.2. Use this one by default, unless you want to test features of more recent
  PHP versions.
- Run `make test-container-74` for PHP 7.4.
- Run `make test-container-80` for PHP 8.0. Use this one if you want to run static analysis.

Inside the container, run `make test` to execute the test suite. Alternatively, run `make test-coverage` to run the test
suite with coverage report. The coverage report is available at `build/coverage/index.html`. Before committing your
changes run `make lint` to make sure the code is healthy and follows our code style.



[PSR-12 Coding Standard]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-coding-style-guide.md
[git-squash]: http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages
[PHPStan]: https://phpstan.org/user-guide/getting-started
