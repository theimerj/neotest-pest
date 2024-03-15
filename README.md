# neotest-pest

This plugin provides a [Pest](https://pestphp.com) adapter for the [Neotest](https://github.com/nvim-neotest/neotest) framework.

This is a fork of `neotest-pest` originally by [@theutz](https://github.com/theutz/neotest-pest), with some fixes and updates:

- Updated to work with [Pest](https://pestphp.com) 2.0
- Support for (and automatic detection of) Laravel Sail
  - Note: This also moves junit output files into `storage/app/`
- Support for running in Docker (custom docker compose)
- Parallel testing support

> [!WARNING]
> _Ive only focused on making this work for me.
> Please test against your Pest tests_
## :package: Installation

Install the plugin using your favorite package manager.

Here's an example for lazy.nvim:

```lua
{
  'nvim-neotest/neotest',
  dependencies = {
    -- ...
    'V13Axel/neotest-pest',
  },
  opts = {
    adapters = {
      ["neotest-pest"] = {
        -- your configuration goes here
      }
    }
  }
}
```

## :wrench: Configuration

> [!TIP]
> Any of these options can be set to a lua function that returns the desired result. For example, wanna run tests in parallel, one for each CPU core?
> `parallel = function() return #vim.loop.cpu_info() end,`

```lua
adapters = {
  ['neotest-pest'] = {
    -- Ignore these directories when looking for tests
    -- -- Default: { "vendor", "node_modules" }
    ignore_dirs = { "vendor", "node_modules" }

    -- Ignore any projects containing "phpunit-only.tests"
    -- -- Default: {}
    root_ignore_files = { "phpunit-only.tests" },

    -- Specify suffixes for files that should be considered tests
    -- -- Default: { "Test.php" }
    test_file_suffixes = { "Test.php", "_test.php", "PestTest.php" },

    -- Sail not properly detected? Explicitly enable it
    -- -- Default: function() that checks for sail presence
    sail_enabled = function() return false end,

    -- Custom sail executable. Not running in Sail, but running bare Docker?
    -- Set `sail_enabled` = true and `sail_executable` to { "docker", "exec", "[somecontainer]" }
    -- -- Default: "vendor/bin/sail"
    sail_executable = "vendor/bin/sail",

    -- Custom pest binary.
    -- -- Default: function that checks for sail presence
    pest_cmd = "vendor/bin/pest",

    -- Run N tests in parallel, <=1 doesn't pass --parallel to pest at all
    -- -- Default: 0
    parallel = 16

    -- Enable ["compact" output printer](https://pestphp.com/docs/optimizing-tests#content-compact-printer)
    -- -- Default: false
    compact = false,

    -- Set a custom path for the results XML file, parsed by this adapter
    --
    ------------------------------------------------------------------------------------
    -- NOTE: This must be a path accessible by both your test runner AND your editor! --
    ------------------------------------------------------------------------------------
    --
    -- -- Default: function that checks for sail presence.
    -- --      - If no sail: Numbered file in randomized /tmp/ directory (using async.fn.tempname())
    -- --      - If sail: "storage/app/" .. os.date("pest-%Y%m%d-%H%M%S")
    results_path = function() "/some/accessible/path" end,
  }),
}
```

### :whale: Docker config example

```lua
adapters = {
  ['neotest-pest'] = {
    -- Enable Docker support
    sail_enabled = function()
      return true
    end,

    -- Set custom docker command.
    pest_cmd = {
      "docker",
      "compose",
      "-f",
      "docker-compose.dev.yml",
      "exec",
      "api-fpm",
      "vendor/bin/pest",
    },

    -- You can omit this, because we explicitly set `sail_enabled` to true
    -- sail_executable = "vendor/bin/sail",

    -- Results path for the junit output
    -- The path is set relative to the root of the Laravel project
    -- This means something like "~/code/my-laravel-app/storage/app/tests/pest-20240315-123456"
    -- and most probably "/var/www/html/storage/app/tests/pest-20240315-123456" in Docker
    results_path = function()
      return "storage/app/tests/" .. os.date("pest-%Y%m%d-%H%M%S")
    end,
  }
}
```

## :rocket: Usage

### Running tests

#### Test single method

To test a single test, hover over the test and run `lua require('neotest').run.run()`.

As an example, I have mine setup with <leader>t(est)n(earest) as such:

```lua
vim.keymap.set('n', '<leader>tn', function() require('neotest').run.run() end)
```

#### Test file

To test a file run `lua require('neotest').run.run(vim.fn.expand('%'))`

Example - <leader>t(est)f(ile):

```lua
vim.keymap.set('n', '<leader>tf', function() require('neotest').run.run(vim.fn.expand('%')) end)
```

#### Test directory

To test a directory run `lua require('neotest').run.run("path/to/directory")`

#### Test suite

To test the full test suite run `lua require('neotest').run.run({ suite = true })`

#### Lazyvim has you covered

> [!TIP]
> Have a look at [Lazyvim Neotest Extra](https://www.lazyvim.org/extras/test/core)
> which you can enable by running `:LazyExtra` and enabling `test.core` extra.
> Checkout `neotest > Full Spec` on the Lazyvim docs to find out that sensible
> keycaps are already set for you and so on.

## :gift: Contributing

I'm just one guy, maintaining this in my spare time and when I can get to it.
Please raise a PR if you are interested in adding new functionality or fixing any bugs.
When submitting a bug, please include an example test that I can test against.

To run the tests for the adapter, run:

```sh
./scripts/test
```

## :clap: Prior Art

This package is a fork of [neotest-pest](https://github.com/theutz/neotest-pest) by [@theutz](https://github.com/olimorris), which relied _heavily_ on [olimorris/neotest-phpunit](https://github.com/olimorris/neotest-phpunit) by [@olimorris](https://github.com/olimorris).
